<?php

//note Generic exception class
class OAuthException extends Exception {
	//note pass
}

class OAuthConsumer {
	public $key;
	public $secret;

	function __construct($key, $secret, $callback_url=NULL) {
		$this->key = $key;
		$this->secret = $secret;
		$this->callback_url = $callback_url;
	}

	function __toString() {
		return "OAuthConsumer[key=$this->key,secret=$this->secret]";
	}
}

class OAuthToken {
	//note access tokens and request tokens
	public $key;
	public $secret;

	//note key = the token
	//note secret = the token secret
	function __construct($key, $secret) {
		$this->key = $key;
		$this->secret = $secret;
	}

	//note generates the basic string serialization of a token that a server
	//note would respond to request_token and access_token calls with
	function to_string() {
		return "oauth_token=" .
			OAuthUtil::urlencode_rfc3986($this->key) .
			"&oauth_token_secret=" .
			OAuthUtil::urlencode_rfc3986($this->secret);
	}

	function __toString() {
		return $this->to_string();
	}
}

//note A class for implementing a Signature Method
//note See section 9 ("Signing Requests") in the spec
abstract class OAuthSignatureMethod {
	//note Needs to return the name of the Signature Method (ie HMAC-SHA1)
	//note @return string
	abstract public function get_name();

	//note Build up the signature
	//note NOTE: The output of this function MUST NOT be urlencoded.
	//note the encoding is handled in OAuthRequest when the final
	//note request is serialized
	//note @param OAuthRequest $request
	//note @param OAuthConsumer $consumer
	//note @param OAuthToken $token
	//note @return string
	abstract public function build_signature($request, $consumer, $token);

	//note Verifies that a given signature is correct
	//note @param OAuthRequest $request
	//note @param OAuthConsumer $consumer
	//note @param OAuthToken $token
	//note @param string $signature
	//note @return bool
	public function check_signature($request, $consumer, $token, $signature) {
		$built = $this->build_signature($request, $consumer, $token);
		return $built == $signature;
	}
}

//note The HMAC-SHA1 signature method uses the HMAC-SHA1 signature algorithm as defined in [RFC2104] 
//note where the Signature Base String is the text and the key is the concatenated values (each first 
//note encoded per Parameter Encoding) of the Consumer Secret and Token Secret, separated by an '&' 
//note character (ASCII code 38) even if empty.
//note   - Chapter 9.2 ("HMAC-SHA1")
class OAuthSignatureMethod_HMAC_SHA1 extends OAuthSignatureMethod {
	function get_name() {
		return "HMAC-SHA1";
	}

	public function build_signature($request, $consumer, $token) {
		$base_string = $request->get_signature_base_string();
		$request->base_string = $base_string;

		$key_parts = array(
			!empty($consumer->secret) ? $consumer->secret : '',
			($token) ? $token->secret : ""
		);

		$key_parts = OAuthUtil::urlencode_rfc3986($key_parts);
		$key = implode('&', $key_parts);

		if(!function_exists('hash_hmac')) {
			return base64_encode(hmac($key, $base_string));
		} else {
			return base64_encode(hash_hmac('sha1', $base_string, $key, true));
		}
	}
}

function hmac($key, $data) {
	// RFC 2104 HMAC implementation for php.
	// Creates an md5 HMAC.
	// Eliminates the need to install mhash to compute a HMAC
	// Hacked by Lance Rushing
	$b = 64; // byte length for md5
	if (strlen($key) > $b) {
		$key = pack("H*",md5($key));
	}
	$key  = str_pad($key, $b, chr(0x00));
	$ipad = str_pad('', $b, chr(0x36));
	$opad = str_pad('', $b, chr(0x5c));
	$k_ipad = $key ^ $ipad ;
	$k_opad = $key ^ $opad;
	return md5($k_opad  . pack("H*",md5($k_ipad . $data)));
}

//note The PLAINTEXT method does not provide any security protection and SHOULD only be used 
//note over a secure channel such as HTTPS. It does not use the Signature Base String.
//note   - Chapter 9.4 ("PLAINTEXT")
class OAuthSignatureMethod_PLAINTEXT extends OAuthSignatureMethod {
	public function get_name() {
		return "PLAINTEXT";
	}

	//note oauth_signature is set to the concatenated encoded values of the Consumer Secret and 
	//note Token Secret, separated by a '&' character (ASCII code 38), even if either secret is 
	//note empty. The result MUST be encoded again.
	//note   - Chapter 9.4.1 ("Generating Signatures")
	//note Please note that the second encoding MUST NOT happen in the SignatureMethod, as
	//note OAuthRequest handles this!
	public function build_signature($request, $consumer, $token) {
		$key_parts = array(
		$consumer->secret,
			($token) ? $token->secret : ""
		);

		$key_parts = OAuthUtil::urlencode_rfc3986($key_parts);
		$key = implode('&', $key_parts);
		$request->base_string = $key;

		return $key;
	}
}

//note The RSA-SHA1 signature method uses the RSASSA-PKCS1-v1_5 signature algorithm as defined in 
//note [RFC3447] section 8.2 (more simply known as PKCS#1), using SHA-1 as the hash function for 
//note EMSA-PKCS1-v1_5. It is assumed that the Consumer has provided its RSA public key in a 
//note verified way to the Service Provider, in a manner which is beyond the scope of this 
//note specification.
//note   - Chapter 9.3 ("RSA-SHA1")
abstract class OAuthSignatureMethod_RSA_SHA1 extends OAuthSignatureMethod {
	public function get_name() {
		return "RSA-SHA1";
	}

	//note Up to the SP to implement this lookup of keys. Possible ideas are:
	//note (1) do a lookup in a table of trusted certs keyed off of consumer
	//note (2) fetch via http using a url provided by the requester
	//note (3) some sort of specific discovery code based on request
	//note Either way should return a string representation of the certificate
	protected abstract function fetch_public_cert(&$request);

	//note Up to the SP to implement this lookup of keys. Possible ideas are:
	//note (1) do a lookup in a table of trusted certs keyed off of consumer
	//note Either way should return a string representation of the certificate
	protected abstract function fetch_private_cert(&$request);

	public function build_signature($request, $consumer, $token) {
		$base_string = $request->get_signature_base_string();
		$request->base_string = $base_string;

		//note Fetch the private key cert based on the request
		$cert = $this->fetch_private_cert($request);

		//note Pull the private key ID from the certificate
		$privatekeyid = openssl_get_privatekey($cert);

		//note Sign using the key
		$ok = openssl_sign($base_string, $signature, $privatekeyid);

		//note Release the key resource
		openssl_free_key($privatekeyid);

		return base64_encode($signature);
	}

	public function check_signature($request, $consumer, $token, $signature) {
		$decoded_sig = base64_decode($signature);

		$base_string = $request->get_signature_base_string();

		//note Fetch the public key cert based on the request
		$cert = $this->fetch_public_cert($request);

		//note Pull the public key ID from the certificate
		$publickeyid = openssl_get_publickey($cert);

		//note Check the computed signature against the one passed in the query
		$ok = openssl_verify($base_string, $decoded_sig, $publickeyid);

		//note Release the key resource
		openssl_free_key($publickeyid);

		return $ok == 1;
	}
}

class OAuthRequest {
	private $parameters;
	private $http_method;
	private $http_url;
	//note for debug purposes
	public $base_string;
	public static $version = '1.0';
	public static $POST_INPUT = 'php://input';

	function __construct($http_method, $http_url, $parameters=NULL) {
		@$parameters or $parameters = array();
		$parameters = array_merge( OAuthUtil::parse_parameters(parse_url($http_url, PHP_URL_QUERY)), $parameters);
		$this->parameters = $parameters;
		$this->http_method = $http_method;
		$this->http_url = $http_url;
	}

	//note attempt to build up a request from what was passed to the server
	public static function from_request($http_method=NULL, $http_url=NULL, $parameters=NULL) {
		$scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on")
			? 'http'
			: 'https';
		@$http_url or $http_url = $scheme .
			'://' . $_SERVER['HTTP_HOST'] .
			':' .
			$_SERVER['SERVER_PORT'] .
			$_SERVER['REQUEST_URI'];
		@$http_method or $http_method = $_SERVER['REQUEST_METHOD'];

		//note We weren't handed any parameters, so let's find the ones relevant to
		//note this request.
		//note If you run XML-RPC or similar you should use this to provide your own
		//note parsed parameter-list
		if (!$parameters) {
			//note Find request headers
			$request_headers = OAuthUtil::get_headers();

			//note Parse the query-string to find GET parameters
			$parameters = OAuthUtil::parse_parameters($_SERVER['QUERY_STRING']);

			//note It's a POST request of the proper content-type, so parse POST
			//note parameters and add those overriding any duplicates from GET
			if ($http_method == "POST"
				&& @strstr($request_headers["Content-Type"],
				"application/x-www-form-urlencoded")
			) {
				$post_data = OAuthUtil::parse_parameters(
					file_get_contents(self::$POST_INPUT)
				);
				$parameters = array_merge($parameters, $post_data);
			}

			//note We have a Authorization-header with OAuth data. Parse the header
			//note and add those overriding any duplicates from GET or POST
			if (@substr($request_headers['Authorization'], 0, 6) == "OAuth ") {
				$header_parameters = OAuthUtil::split_header(
					$request_headers['Authorization']
				);
				$parameters = array_merge($parameters, $header_parameters);
			}

		}

		return new OAuthRequest($http_method, $http_url, $parameters);
	}

	//note pretty much a helper function to set up the request
	public static function from_consumer_and_token($consumer, $token, $http_method, $http_url, $parameters=NULL) {
		@$parameters or $parameters = array();
		$defaults = array("oauth_version" => OAuthRequest::$version,
			"oauth_nonce" => OAuthRequest::generate_nonce(),
			"oauth_timestamp" => OAuthRequest::generate_timestamp(),
			"oauth_consumer_key" => !empty($consumer->key) ? $consumer->key : '');
		if ($token)
			$defaults['oauth_token'] = $token->key;

		$parameters = array_merge($defaults, $parameters);

		return new OAuthRequest($http_method, $http_url, $parameters);
	}

	public function set_parameter($name, $value, $allow_duplicates = true) {
		if ($allow_duplicates && isset($this->parameters[$name])) {
			//note We have already added parameter(s) with this name, so add to the list
			if (is_scalar($this->parameters[$name])) {
				//note This is the first duplicate, so transform scalar (string)
				//note into an array so we can add the duplicates
				$this->parameters[$name] = array($this->parameters[$name]);
			}

			$this->parameters[$name][] = $value;
		} else {
			$this->parameters[$name] = $value;
		}
	}

	public function get_parameter($name) {
		return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
	}

	public function get_parameters() {
		return $this->parameters;
	}

	public function unset_parameter($name) {
		unset($this->parameters[$name]);
	}

	//note The request parameters, sorted and concatenated into a normalized string.
	//note @return string
	public function get_signable_parameters() {
		//note Grab all parameters
		$params = $this->parameters;

		//note Remove oauth_signature if present
		//note Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
		if (isset($params['oauth_signature'])) {
			unset($params['oauth_signature']);
		}

		return OAuthUtil::build_http_query($params);
	}

	//note Returns the base string of this request
	//note The base string defined as the method, the url
	//note and the parameters (normalized), each urlencoded
	//note and the concated with &.
	public function get_signature_base_string() {
		$parts = array(
			$this->get_normalized_http_method(),
			$this->get_normalized_http_url(),
			$this->get_signable_parameters()
		);

		$parts = OAuthUtil::urlencode_rfc3986($parts);

		return implode('&', $parts);
	}

	//note just uppercases the http method
	public function get_normalized_http_method() {
		return strtoupper($this->http_method);
	}

	//note parses the url and rebuilds it to be
	//note scheme://host/path
	public function get_normalized_http_url() {
		$parts = parse_url($this->http_url);

		$port = @$parts['port'];
		$scheme = $parts['scheme'];
		$host = $parts['host'];
		$path = @$parts['path'];

		$port or $port = ($scheme == 'https') ? '443' : '80';

		if (($scheme == 'https' && $port != '443')
			|| ($scheme == 'http' && $port != '80')) {
			$host = "$host:$port";
		}
		return "$scheme://$host$path";
	}

	//note builds a url usable for a GET request
	public function to_url() {
		$post_data = $this->to_postdata();
		$out = $this->get_normalized_http_url();
		if ($post_data) {
			$out .= '?'.$post_data;
		}
		return $out;
	}

	//note builds the data one would send in a POST request
	public function to_postdata() {
		return OAuthUtil::build_http_query($this->parameters);
	}

	//note builds the Authorization: header
	public function to_header($realm=null) {
		$first = true;
		if($realm) {
			$out = 'Authorization: OAuth realm="' . OAuthUtil::urlencode_rfc3986($realm) . '"';
			$first = false;
		} else {
			$out = 'Authorization: OAuth';
		}

		$total = array();
		foreach ($this->parameters as $k => $v) {
			if (substr($k, 0, 5) != "oauth") continue;
			if (is_array($v)) {
				throw new OAuthException('Arrays not supported in headers');
			}
			$out .= ($first) ? ' ' : ',';
			$out .= OAuthUtil::urlencode_rfc3986($k) .
				'="' .
				OAuthUtil::urlencode_rfc3986($v) .
				'"';
			$first = false;
		}
		return $out;
	}

	public function __toString() {
		return $this->to_url();
	}

	public function sign_request($signature_method, $consumer, $token) {
		$this->set_parameter(
			"oauth_signature_method",
			$signature_method->get_name(),
			false
		);
		$signature = $this->build_signature($signature_method, $consumer, $token);
		$this->set_parameter("oauth_signature", $signature, false);
	}

	public function build_signature($signature_method, $consumer, $token) {
		$signature = $signature_method->build_signature($this, $consumer, $token);
		return $signature;
	}

	//note util function: current timestamp
	private static function generate_timestamp() {
		return time();
	}

	//note util function: current nonce
	private static function generate_nonce() {
		$mt = microtime();
		$rand = mt_rand();

		return md5($mt . $rand); //note md5s look nicer than numbers
	}
}

class OAuthServer {
	protected $timestamp_threshold = 3600; //note in seconds, five minutes
	protected $version = '1.0';             //note hi blaine
	protected $signature_methods = array();

	protected $data_store;

	function __construct($data_store) {
		$this->data_store = $data_store;
	}

	public function add_signature_method($signature_method) {
		$this->signature_methods[$signature_method->get_name()] = $signature_method;
	}

	//note high level functions
	//note process a request_token request
	//note returns the request token on success
	public function fetch_request_token(&$request) {
		$this->get_version($request);

		$consumer = $this->get_consumer($request);

		//note no token required for the initial token request
		$token = NULL;

		$this->check_signature($request, $consumer, $token);

		//note Rev A change
		$callback = $request->get_parameter('oauth_callback');
		$new_token = $this->data_store->new_request_token($consumer, $callback);

		return $new_token;
	}

	//note process an access_token request
	//note returns the access token on success
	public function fetch_access_token(&$request) {
		$this->get_version($request);

		$consumer = $this->get_consumer($request);

		//note requires authorized request token
		$token = $this->get_token($request, $consumer, "request");

		$this->check_signature($request, $consumer, $token);

		//note Rev A change
		$verifier = $request->get_parameter('oauth_verifier');
		$new_token = $this->data_store->new_access_token($token, $consumer, $verifier);

		return $new_token;
	}

	//note verify an api call, checks all the parameters
	public function verify_request(&$request) {
		$this->get_version($request);
		$consumer = $this->get_consumer($request);
		$token = $this->get_token($request, $consumer, "access");
		$this->check_signature($request, $consumer, $token);
		return array($consumer, $token);
	}

	//note Internals from here
	//note version 1
	private function get_version(&$request) {
		$version = $request->get_parameter("oauth_version");
		if (!$version) {
			//note Service Providers MUST assume the protocol version to be 1.0 if this parameter is not present. 
			//note Chapter 7.0 ("Accessing Protected Ressources")
			$version = '1.0';
		}
		if ($version !== $this->version) {
			throw new OAuthException("OAuth version '$version' not supported");
		}
		return $version;
	}

	//note figure out the signature with some defaults
	private function get_signature_method(&$request) {
		$signature_method =
		@$request->get_parameter("oauth_signature_method");

		if (!$signature_method) {
			//note According to chapter 7 ("Accessing Protected Ressources") the signature-method
			//note parameter is required, and we can't just fallback to PLAINTEXT
			throw new OAuthException('No signature method parameter. This parameter is required');
		}

		if (!in_array($signature_method,
			array_keys($this->signature_methods))) {
			throw new OAuthException(
				"Signature method '$signature_method' not supported " .
				"try one of the following: " .
				implode(", ", array_keys($this->signature_methods))
			);
		}
		return $this->signature_methods[$signature_method];
	}

	//note try to find the consumer for the provided request's consumer key
	private function get_consumer(&$request) {
		$consumer_key = @$request->get_parameter("oauth_consumer_key");
		if (!$consumer_key) {
			throw new OAuthException("Invalid consumer key");
		}

		$consumer = $this->data_store->lookup_consumer($consumer_key);
		if (!$consumer) {
			throw new OAuthException("Invalid consumer");
		}

		return $consumer;
	}

	//note try to find the token for the provided request's token key
	private function get_token(&$request, $consumer, $token_type="access") {
		$token_field = @$request->get_parameter('oauth_token');
		$token = $this->data_store->lookup_token(
			$consumer, $token_type, $token_field
		);
		if (!$token) {
			throw new OAuthException("Invalid $token_type token: $token_field");
		}
		return $token;
	}

	//note all-in-one function to check the signature on a request
	//note should guess the signature method appropriately
	private function check_signature(&$request, $consumer, $token) {
		//note this should probably be in a different method
		$timestamp = @$request->get_parameter('oauth_timestamp');
		$nonce = @$request->get_parameter('oauth_nonce');

		$this->check_timestamp($timestamp);
		$this->check_nonce($consumer, $token, $nonce, $timestamp);

		$signature_method = $this->get_signature_method($request);

		$signature = $request->get_parameter('oauth_signature');
		$valid_sig = $signature_method->check_signature(
			$request,
			$consumer,
			$token,
			$signature
		);

		if (!$valid_sig) {
			throw new OAuthException("Invalid signature");
		}
	}

	//note check that the timestamp is new enough
	private function check_timestamp($timestamp) {
		if( ! $timestamp ) {
			throw new OAuthException(
				'Missing timestamp parameter. The parameter is required'
			);
		}

		//note verify that timestamp is recentish
		$now = time();
		if (abs($now - $timestamp) > $this->timestamp_threshold) {
			throw new OAuthException(
				"Expired timestamp, yours $timestamp, ours $now"
			);
		}
	}

	//note check that the nonce is not repeated
	private function check_nonce($consumer, $token, $nonce, $timestamp) {
		if( ! $nonce ) {
			throw new OAuthException(
				'Missing nonce parameter. The parameter is required'
			);
		}

		//note verify that the nonce is uniqueish
		$found = $this->data_store->lookup_nonce(
			$consumer,
			$token,
			$nonce,
			$timestamp
		);
		if ($found) {
			throw new OAuthException("Nonce already used: $nonce");
		}
	}

}

class OAuthDataStore {
	function lookup_consumer($consumer_key) {
		//note implement me
	}

	function lookup_token($consumer, $token_type, $token) {
		//note implement me
	}

	function lookup_nonce($consumer, $token, $nonce, $timestamp) {
		//note implement me
	}

	function new_request_token($consumer, $callback = null) {
		//note return a new token attached to this consumer
	}

	function new_access_token($token, $consumer, $verifier = null) {
		//note return a new access token attached to this consumer
		//note for the user associated with this token if the request token
		//note is authorized
		//note should also invalidate the request token
	}

}

class OAuthUtil {
	public static function urlencode_rfc3986($input) {
		if (is_array($input)) {
			return array_map(array('OAuthUtil', 'urlencode_rfc3986'), $input);
		} else if (is_scalar($input)) {
			return str_replace(
				'+',
				' ',
				str_replace('%7E', '~', rawurlencode($input))
			);
		} else {
			return '';
		}
	}

	//note This decode function isn't taking into consideration the above
	//note modifications to the encoding process. However, this method doesn't
	//note seem to be used anywhere so leaving it as is.
	public static function urldecode_rfc3986($string) {
		return urldecode($string);
	}

	//note Utility function for turning the Authorization: header into
	//note parameters, has to do some unescaping
	//note Can filter out any non-oauth parameters if needed (default behaviour)
	public static function split_header($header, $only_allow_oauth_parameters = true) {
		$pattern = '/(([-_a-z]*)=("([^"]*)"|([^,]*)),?)/';
		$offset = 0;
		$params = array();
		while (preg_match($pattern, $header, $matches, PREG_OFFSET_CAPTURE, $offset) > 0) {
			$match = $matches[0];
			$header_name = $matches[2][0];
			$header_content = (isset($matches[5])) ? $matches[5][0] : $matches[4][0];
			if (preg_match('/^oauth_/', $header_name) || !$only_allow_oauth_parameters) {
				$params[$header_name] = OAuthUtil::urldecode_rfc3986($header_content);
			}
			$offset = $match[1] + strlen($match[0]);
		}

		if (isset($params['realm'])) {
			unset($params['realm']);
		}

		return $params;
	}

	//note helper to try to sort out headers for people who aren't running apache
	public static function get_headers() {
		if (function_exists('apache_request_headers')) {
			//note we need this to get the actual Authorization: header
			//note because apache tends to tell us it doesn't exist
			$headers = apache_request_headers();

			//note sanitize the output of apache_request_headers because
			//note we always want the keys to be Cased-Like-This and arh()
			//note returns the headers in the same case as they are in the
			//note request
			$out = array();
			foreach( $headers AS $key => $value ) {
				$key = str_replace(
					" ",
					"-",
					ucwords(strtolower(str_replace("-", " ", $key)))
				);
				$out[$key] = $value;
			}
		} else {
			//note otherwise we don't have apache and are just going to have to hope
			//note that $_SERVER actually contains what we need
			$out = array();
			if( isset($_SERVER['CONTENT_TYPE']) ) {
				$out['Content-Type'] = $_SERVER['CONTENT_TYPE'];
			}
			if( isset($_ENV['CONTENT_TYPE']) ) {
				$out['Content-Type'] = $_ENV['CONTENT_TYPE'];
			}

			foreach ($_SERVER as $key => $value) {
				if (substr($key, 0, 5) == "HTTP_") {
					//note this is chaos, basically it is just there to capitalize the first
					//note letter of every word that is not an initial HTTP and strip HTTP
					//note code from przemek
					$key = str_replace(
						" ",
						"-",
						ucwords(strtolower(str_replace("_", " ", substr($key, 5))))
					);
					$out[$key] = $value;
				}
			}
		}
		return $out;
	}

	//note This function takes a input like a=b&a=c&d=e and returns the parsed
	//note parameters like this
	//note array('a' => array('b','c'), 'd' => 'e')
	public static function parse_parameters( $input ) {
		if (!isset($input) || !$input) return array();

		$pairs = explode('&', $input);

		$parsed_parameters = array();
		foreach ($pairs as $pair) {
			$split = explode('=', $pair, 2);
			$parameter = OAuthUtil::urldecode_rfc3986($split[0]);
			$value = isset($split[1]) ? OAuthUtil::urldecode_rfc3986($split[1]) : '';

			if (isset($parsed_parameters[$parameter])) {
				//note We have already recieved parameter(s) with this name, so add to the list
				//note of parameters with this name

				if (is_scalar($parsed_parameters[$parameter])) {
					//note This is the first duplicate, so transform scalar (string) into an array
					//note so we can add the duplicates
					$parsed_parameters[$parameter] = array($parsed_parameters[$parameter]);
				}

				$parsed_parameters[$parameter][] = $value;
			} else {
				$parsed_parameters[$parameter] = $value;
			}
		}
		return $parsed_parameters;
	}

	public static function build_http_query($params) {
		if (!$params) return '';

		//note Urlencode both keys and values
		$keys = OAuthUtil::urlencode_rfc3986(array_keys($params));
		$values = OAuthUtil::urlencode_rfc3986(array_values($params));
		$params = array_combine($keys, $values);

		//note Parameters are sorted by name, using lexicographical byte value ordering.
		//note Ref: Spec: 9.1.1 (1)
		uksort($params, 'strcmp');

		$pairs = array();
		foreach ($params as $parameter => $value) {
			if (is_array($value)) {
				//note If two or more parameters share the same name, they are sorted by their value
				//note Ref: Spec: 9.1.1 (1)
				natsort($value);
				foreach ($value as $duplicate_value) {
					$pairs[] = $parameter . '=' . $duplicate_value;
				}
			} else {
				$pairs[] = $parameter . '=' . $value;
			}
		}
		//note For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
		//note Each name-value pair is separated by an '&' character (ASCII code 38)
		return implode('&', $pairs);
	}
}

class CTTOAuthServer extends OAuthServer {
	public function get_signature_methods() {
		return $this->signature_methods;
	}
}

class MockOAuthDataStore extends OAuthDataStore {
	private $consumer;
	private $request_token;
	private $access_token;
	private $nonce;

	function __construct() {
		$this->consumer = new OAuthConsumer(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, NULL);
		$this->request_token = new OAuthToken(OAUTH_REQUEST_KEY, OAUTH_REQUEST_SECRET, 1);
		$this->access_token = new OAuthToken(OAUTH_ACCESS_KEY, OAUTH_ACCESS_SECRET, 1);
		$this->nonce = "nonce";
	}

	function lookup_consumer($consumer_key) {
		if ($consumer_key == $this->consumer->key) return $this->consumer;
		return NULL;
	}

	function lookup_token($consumer, $token_type, $token) {
		$token_attrib = $token_type . "_token";
		if ($consumer->key == $this->consumer->key && $token == $this->$token_attrib->key) {
			return $this->$token_attrib;
		}
		return NULL;
	}

	function lookup_nonce($consumer, $token, $nonce, $timestamp) {
		if ($consumer->key == $this->consumer->key && (($token && $token->key == $this->request_token->key) || ($token && $token->key == $this->access_token->key)) && $nonce == $this->nonce) {
			return $this->nonce;
		}
		return NULL;
	}

	function new_request_token($consumer) {
		if ($consumer->key == $this->consumer->key) {
			return $this->request_token;
		}
		return NULL;
	}

	function new_access_token($token, $consumer) {
		if ($consumer->key == $this->consumer->key && $token->key == $this->request_token->key) {
			return $this->access_token;
		}
		return NULL;
	}
}

$auth_server = new CTTOAuthServer(new MockOAuthDataStore());
$hmac_method = new OAuthSignatureMethod_HMAC_SHA1();
$auth_server->add_signature_method($hmac_method);
$sig_methods = $auth_server->get_signature_methods();

?>
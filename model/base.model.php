<?php

if(!defined('IN_WP')) {
	exit('Access Denied');
}

class baseModel {
	var $time;
	var $todaytime;
	var $dateformat;
	var $timeformat;
	var $timeoffset;
	var $onlineip;
	var $db;
	var $tpl;
	var $tpldir;
	var $imgdir;
	var $styleid;
	var $authkey;
	var $config = array();
	var $cookie = array();
	var $sid;
	var $bpp = 20;
	var $cpp = 20;

	function __construct() {
		$this->baseModel();
	}

	function baseModel() {
		$this->initVar();
		//$this->initDb();
		$this->initMongo();
		$this->initConfig();
		$this->initTemplate();
	}

	
	function initVar() {
		$this->time = time();
		$cip = getenv('HTTP_CLIENT_IP');
		$xip = getenv('HTTP_X_FORWARDED_FOR');
		$rip = getenv('REMOTE_ADDR');
		$srip = $_SERVER['REMOTE_ADDR'];
		if($cip && strcasecmp($cip, 'unknown')) {
			$this->onlineip = $cip;
		} elseif($xip && strcasecmp($xip, 'unknown')) {
			$this->onlineip = $xip;
		} elseif($rip && strcasecmp($rip, 'unknown')) {
			$this->onlineip = $rip;
		} elseif($srip && strcasecmp($srip, 'unknown')) {
			$this->onlineip = $srip;
		}
		preg_match("/[\d\.]{7,15}/", $this->onlineip, $match);
		$this->onlineip = isset($match[0]) ? $match[0] : 'unknown';

		$phpself = htmlspecialchars($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
		define('BASESCRIPT', basename($phpself));
		define('SITE_URL', 'http://'.$_SERVER['HTTP_HOST'].substr($phpself, 0, strrpos($phpself, '/') + 1));

		if($_COOKIE) {
			$prelen = strlen(COOKIE_PRE);
			foreach($_COOKIE as $key => $val) {
				if(substr($key, 0, $prelen) == COOKIE_PRE) {
					$this->cookie[(substr($key, $prelen))] = MAGIC_QUOTES_GPC ? $val : $this->_addslashes($val);
				}
			}
			unset($prelen, $key, $val);
		}
	}

	
	function initDb() {
		require_once DIR_LIB.'/db.class.php';
		$this->db = new mysqlDb();
		$this->db->connect(DB_HOST, DB_USER, DB_PW, DB_NAME, DB_CONNECT, DB_CHARSET);
	}
	
	function initMongo() {
		require_once DIR_LIB.'/mongo.class.php';
		$this->db = new mongoDbase();
		$this->db->connect(DB_HOST, DB_PORT, DB_USER, DB_PW, DB_NAME);
	}

	function initConfig() {
		
	}
		

	/**
	 * 初始化模板
	 */
	function initTemplate() {
		require_once DIR_LIB.'/template.class.php';

        $this->tpl = new template($this, $this->styleid, $this->tpldir);
		$this->tpl->assign('charset', CHARSET);

		$this->tpl->assign('access', $this->access);
		$this->tpl->assign('config', $this->config);
		$this->tpl->assign('styleid', $this->styleid);
		$this->tpl->assign('tpldir', $this->tpldir);
		$this->tpl->assign('imgdir', $this->imgdir);
		$this->tpl->assign('jspath', DEBUG == 2 ? 'js/' : 'data/cache/js/');
		$this->tpl->assign('referer', $this->referer());
		$cookiecheck = !empty($this->cookie['cookietime']) ? 'checked="checked"' : '';
		$this->tpl->assign('cookiecheck', $cookiecheck);
	}

	function _addslashes($string, $force = 0, $strip = FALSE) {
		if(!MAGIC_QUOTES_GPC || $force) {
			if(is_array($string)) {
				foreach($string as $key => $val) {
					$string[$key] = $this->_addslashes($val, $force, $strip);
				}
			} else {
				$string = addslashes($strip ? stripslashes($string) : $string);
			}
		}
		return $string;
	}

	/**
	 * 加解密
	 * @param unknown $string
	 * @param string $operation
	 * @param string $key
	 * @param number $expiry
	 * @return string
	 */
	function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

		$ckey_length = 4;
		$key = md5($key ? $key : $this->authkey);
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		if($operation == 'DECODE') {
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc.str_replace('=', '', base64_encode($result));
		}

	}

	/**
	 * 随机字符串
	 * @param unknown $length
	 * @param number $numeric
	 * @return string
	 */
	function random($length, $numeric = 0) {
		PHP_VERSION < '4.2.0' ? mt_srand((double)microtime() * 1000000) : mt_srand();
		$seed = base_convert(md5(print_r($_SERVER, 1).microtime()), 16, $numeric ? 10 : 35);
		$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
		$hash = '';
		$max = strlen($seed) - 1;
		for($i = 0; $i < $length; $i++) {
			$hash .= $seed[mt_rand(0, $max)];
		}
		return $hash;
	}

	/**
	 * 模块load 方法
	 * @param unknown $model
	 * @param string $base
	 * @return unknown
	 */
	function loadModel($model, $base = NULL) {
		$base = $base ? $base : $this;
		if(empty($_ENV[$model.'Model'])) {
			require_once DIR_MODEL."/$model.model.php";
			eval('$_ENV[$model.\'Model\'] = new '.$model.'Model($base);');
		}
		return $_ENV[$model.'Model'];
	}

	
	/**
	 * 
	 * @param string $submit
	 * @return boolean
	 */
	function submitcheck($submit = '') {
		return (!$submit || isset($_POST[$submit])) && isset($_POST['formhash']) && $_POST['formhash'] == FORMHASH ? true : false;
	}


	function _setcookie($key, $value, $life = 0, $httponly = false) {
		if($value == '' || $life < 0) {
			$value = '';
			$life = -1;
		}

		$life = $life > 0 ? $this->time + $life : ($life < 0 ? $this->time - 31536000 : 0);
		$path = $httponly && PHP_VERSION < '5.2.0' ? COOKIE_PATH."; HttpOnly" : COOKIE_PATH;
		$secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
		if(PHP_VERSION < '5.2.0') {
			setcookie(COOKIE_PRE.$key, $value, $life, $path, COOKIE_DOMAIN, $secure);
		} else {
			setcookie(COOKIE_PRE.$key, $value, $life, $path, COOKIE_DOMAIN, $secure, $httponly);
		}
	}

	function _implode($array) {
		if(!empty($array)) {
			return "'".implode("','", is_array($array) ? $array : array($array))."'";
		} else {
			return '';
		}
	}

	function referer() {
		$referer = '';
		if(empty($_POST['referer']) && isset($GLOBALS['_SERVER']['HTTP_REFERER'])) {
			$referer = preg_replace("/([\?&])((sid\=[a-z0-9]{6})(&|$))/i", '\\1', $GLOBALS['_SERVER']['HTTP_REFERER']);
			$referer = (substr($referer, -1) == '?') || (substr($referer, -1) == '&') ? substr($referer, 0, -1) : $referer;
		} elseif(!empty($_POST['referer'])) {
			$referer = str_replace('&amp;', '&', htmlspecialchars($_POST['referer']));
		}
		return $referer;
	}

	function _header($string, $replace = true, $http_response_code = 0) {
		$redirect = preg_match('/^\s*location:/is', $string) ? true : false;
		$string = str_replace(array("\r", "\n"), array('', ''), $string);
		$string .= $redirect && ISWAP && $this->sid ? (strpos($string, '?') ? '&' : '?').'sid='.$this->sid : '';
		if(empty($http_response_code) || PHP_VERSION < '4.3' ) {
			@header($string, $replace);
		} else {
			@header($string, $replace, $http_response_code);
		}
		$redirect && exit();
	}


	/**
	 * 写日志
	 * @param string $file
	 * @param string $log
	 */
	function writeLog($file, $log) {
		$yearmonth = gmdate('Ym', $this->time + $this->config['timeoffset'] * 3600);
		$logdir = DIR_DATA.'/log/';
		$logfile = $logdir.$yearmonth.'_'.$file.'.php';
		if(@filesize($logfile) > 2048000) {
			$dir = opendir($logdir);
			$length = strlen($file);
			$maxid = $id = 0;
			while($entry = readdir($dir)) {
				if($this->strexists($entry, $yearmonth.'_'.$file)) {
					$id = intval(substr($entry, $length + 8, -4));
					$id > $maxid && $maxid = $id;
				}
			}
			closedir($dir);
			$logfilebak = $logdir.$yearmonth.'_'.$file.'_'.($maxid + 1).'.php';
			@rename($logfile, $logfilebak);
		}
		if($fp = @fopen($logfile, 'a')) {
			@flock($fp, 2);
			$log = is_array($log) ? $log : array($log);
			foreach($log as $tmp) {
				fwrite($fp, "<?PHP exit;?>\t".str_replace(array('<?', '?>'), '', $tmp)."\n");
			}
			fclose($fp);
		}
	}

	function strexists($haystack, $needle) {
		return !(strpos($haystack, $needle) === FALSE);
	}

}

<?php

if(!defined('IN_WP')) {
	exit('Access Denied');
}

class email {
	var $mail;
	var $recipient;
	var $title;
	var $content;
	var $sender;
	var $delimiter;
	var $header;
	var $charset;

	function __construct(&$mail) {
		$this->email($mail);
	}

	function email(&$mail) {
		$this->mail = $mail;
		$this->charset = CHARSET;
	}

	function send($recipient, $title, $content, $sender = '') {
		if($this->mail['silent']) {
			error_reporting(0);
		}

		$tousers = array();
		foreach(explode(',', $recipient) as $touser) {
			$tousers[] = preg_match('/^(.+?) \<(.+?)\>$/', $touser, $to) ? '=?'.$this->charset.'?B?'.base64_encode($to[1])."?= <$to[2]>" : $touser;
		}
		$this->recipient = implode(',', $tousers);

		$this->title = '=?'.$this->charset.'?B?'.base64_encode(str_replace(array("\r", "\n"), '', $title)).'?=';
		$this->content = chunk_split(base64_encode(str_replace(array("\n\r", "\r\n", "\r", "\n", "\r\n."), array("\r", "\n", "\n", "\r\n", " \r\n.."), $content)));

		$this->delimiter = $this->mail['delimiter'] == 1 ? "\r\n" :
			($this->mail['delimiter'] == 2 ? "\r" : "\n");
		$this->sender = preg_match('/^(.+?) \<(.+?)\>$/', $sender, $from) ? '=?'.$this->charset.'?B?'.base64_encode($from[1])."?= <$from[2]>" : $sender;

		$this->header = "From: $this->sender{$this->delimiter}".
			"X-Priority: 3{$this->delimiter}".
			"X-Mailer: Email{$this->delimiter}".
			"MIME-Version: 1.0{$this->delimiter}".
			"Content-type: text/plain; charset={$this->charset}{$this->delimiter}".
			"Content-Transfer-Encoding: base64{$this->delimiter}";
		$this->mail['port'] = $this->mail['port'] ? $this->mail['port'] : 25;
		if($this->mail['type'] == 1) {
			return $this->sendMail();
		} elseif($this->mail['type'] == 2) {
			return $this->socketSmtp();
		} elseif($this->mail['type'] == 3) {
			return $this->phpSmtp();
		}
	}

	//note ͨ�� PHP ����� sendmail ����(�Ƽ��˷�ʽ)
	function sendMail() {
		if(!function_exists('mail')) {
			return array('status' => 1, 'error' => 'SendMail\tFunction "mail()" NOT exists!');
		}
		if(@mail($this->recipient, $this->title, $this->content, $this->header)) {
			return array('status' => 0);
		} else {
			return array('status' => 2, 'error' => 'PHP Mail\tPHP sendmail error!');
		}
	}

	//note ͨ�� SOCKET ���� SMTP ����������(֧�� ESMTP ��֤)
	function socketSmtp() {
		$error = "({$this->mail[server]}:{$this->mail[port]})";
		if(!$fp = fsockopen($this->mail['server'], $this->mail['port'], $errno, $errstr, 30)) {
			return array('status' => 4, 'error' => "SMTP\t$error CONNECT - Unable to connect to the SMTP server");
		}
	 	stream_set_blocking($fp, true);

		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != '220') {
			return array('status' => 5, 'error' => "SMTP\t$error CONNECT - $lastmessage");
		}

		fputs($fp, ($this->mail['auth'] ? 'EHLO' : 'HELO')." hello\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 220 && substr($lastmessage, 0, 3) != 250) {
			return array('status' => 6, 'error' => "SMTP\t$error HELO/EHLO - $lastmessage");
		}

		while(1) {
			if(substr($lastmessage, 3, 1) != '-' || empty($lastmessage)) {
	 			break;
	 		}
	 		$lastmessage = fgets($fp, 512);
		}

		if($this->mail['auth']) {
			fputs($fp, "AUTH LOGIN\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 334) {
				return array('status' => 7, 'error' => "SMTP\t$error AUTH LOGIN - $lastmessage");
			}

			fputs($fp, base64_encode($this->mail['auth_username'])."\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 334) {
				return array('status' => 8, 'error' => "SMTP\t$error USERNAME - $lastmessage");
			}

			fputs($fp, base64_encode($this->mail['auth_password'])."\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 235) {
				return array('status' => 9, 'error' => "SMTP\t$error PASSWORD - $lastmessage");
			}

			$this->sender = $this->mail['auth_from'];
		}

		fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $this->sender).">\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 250) {
			fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $this->sender).">\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 250) {
				return array('status' => 10, 'error' => "SMTP\t$error MAIL FROM - $lastmessage");
			}
		}

		foreach(explode(',', $this->recipient) as $touser) {
			$touser = trim($touser);
			if($touser) {
				fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $touser).">\r\n");
				$lastmessage = fgets($fp, 512);
				if(substr($lastmessage, 0, 3) != 250) {
					fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $touser).">\r\n");
					$lastmessage = fgets($fp, 512);
					return array('status' => 11, 'error' => "SMTP\t$error RCPT TO - $lastmessage");
				}
			}
		}

		fputs($fp, "DATA\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 354) {
			return array('status' => 12, 'error' => "SMTP\t$error DATA - $lastmessage");
		}

		$this->header .= 'Message-ID: <'.gmdate('YmdHs').'.'.substr(md5($this->content.microtime()), 0, 6).rand(100000, 999999).'@'.$_SERVER['HTTP_HOST'].">{$this->delimiter}";

		fputs($fp, "Date: ".gmdate('r')."\r\n");
		fputs($fp, "To: ".$this->recipient."\r\n");
		fputs($fp, "Subject: ".$this->title."\r\n");
		fputs($fp, $this->header."\r\n");
		fputs($fp, "\r\n\r\n");
		fputs($fp, "$this->content\r\n.\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 250) {
			return array('status' => 13, 'error' => "SMTP\t$error END - $lastmessage");
		}

		fputs($fp, "QUIT\r\n");
		return array('status' => 0);
	}

	//note ͨ�� PHP ���� SMTP ���� Email(�� Windows ��������Ч, ��֧�� ESMTP ��֤)
	function phpSmtp() {
		if(!function_exists('mail')) {
			return array('status' => 1, 'error' => 'SendMail\tFunction "mail()" NOT exists!');
		}
		ini_set('SMTP', $this->mail['server']);
		ini_set('smtp_port', $this->mail['port']);
		ini_set('sendmail_from', $this->sender);

		if(@mail($this->recipient, $this->title, $this->content, $this->header)) {
			return array('status' => 0);
		} else {
			return array('status' => 3, 'error' => 'PHP SMTP\tPHP smtp mail error!');
		}
	}

}

?>
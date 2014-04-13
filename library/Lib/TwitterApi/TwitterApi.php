<?php

require_once "tmhOAuth/tmhOAuth.php";

class Lib_TwitterApi_TwitterApi{
	
	public $applicationName = 'BILboard';
	public $oauthAccessToken = '62234258-RZpmVFbfwjFn83bUy3ELFIKvj0ZRTzHyGtZOZ2avm';
	public $oauthAccessTokenSecret = '5AZClTO7X58U9Y0czAEWh2oFzzIHmWHAPTWek7VzRvz48';
	public $apiKey = 'ivM1bxsD7faQCIq43iEGAw';
	public $apiSecret = "piFOy3kxwkki0bdqw4amzN139x5WjJmGTmGLGlsJyH8";
	public $redirectUri = 'http://www.bilboard.fr/link/twitter';
	
	
	private $_tmhOAuth;
	
	const TWITTERAPI_NAMESPACE = 'TwitterApi';
	
	public function __construct(){
		$settings=array(
		    'consumer_key' => $this->apiKey,
		    'consumer_secret' => $this->apiSecret,
		    'token'           => $this->oauthAccessToken, //A_USER_TOKEN,
	        'secret'          => $this->oauthAccessTokenSecret, //A_USER_SECRET,
	
	        'user_agent'      => 'tmhOAuth ' . tmhOAuth::VERSION . ' BILBoard 0.1',
		);
		$this->_tmhOAuth = new tmhOAuth($settings);
	}
	
	function request_token() {
		$session = new Zend_Session_Namespace(self::TWITTERAPI_NAMESPACE);
		
		$code = $this->_tmhOAuth->apponly_request(array(
			'without_bearer' => true,
			'method' => 'POST',
			'url' => $this->_tmhOAuth->url('oauth/request_token', ''),
			'params' => array(
				'oauth_callback' => $this->php_self(false),
			),
		));

		if ($code != 200) {
    		throw new Exception("There was an error communicating with Twitter. {$this->_tmhOAuth->response['response']}");
    		return;
  		}

		// store the params into the session so they are there when we come back after the redirect
		$session->twitterOauth = $this->_tmhOAuth->extract_params($this->_tmhOAuth->response['response']);

		// check the callback has been confirmed
		if ($session->twitterOauth['oauth_callback_confirmed'] !== 'true') {
    		throw new Exception('The callback was not confirmed by Twitter so we cannot continue.');
		} else {
   			$url = $this->_tmhOAuth->url('oauth/authorize', '') . "?oauth_token=".$session->twitterOauth['oauth_token'];
			return $url;
  		}
	}
	
	function access_token($oauth_token, $oauth_verifier) {
		$session = new Zend_Session_Namespace(self::TWITTERAPI_NAMESPACE);
		
		if ($oauth_token !== $session->twitterOauth['oauth_token']) {
		    unset($session->twitterOauth);
		    throw new Exception('The oauth token you started with doesn\'t match the one you\'ve been redirected with. do you have multiple tabs open?');
		    return;
		}
		
		if (!isset($oauth_verifier)) {
			unset($session->twitterOauth);
		    throw new Exception('The oauth verifier is missing so we cannot continue. did you deny the appliction access?');
		    return;
		}
		
		// update with the temporary token and secret
		$this->_tmhOAuth->reconfigure(array_merge($this->_tmhOAuth->config, array(
		    'token'  => $oauth_token,
		    'secret' => $oauth_verifier,
		)));
		
		$code = $this->_tmhOAuth->user_request(array(
		    'method' => 'POST',
		    'url' => $this->_tmhOAuth->url('oauth/access_token', ''),
		    'params' => array(
		      'oauth_verifier' => trim($oauth_verifier),
		    )
		));
		
		if ($code == 200) {
			$oauth_creds = $this->_tmhOAuth->extract_params($this->_tmhOAuth->response['response']);
			return $oauth_creds;
		}
		else return false;
	}
	
	protected function php_self($dropqs=true) {
		$protocol = 'http';
		if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
			$protocol = 'https';
		} elseif (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == '443')) {
			$protocol = 'https';
		}
		
		$url = sprintf('%s://%s%s',
			$protocol,
			$_SERVER['SERVER_NAME'],
			$_SERVER['REQUEST_URI']
		);
		
		$parts = parse_url($url);
		
		$port = $_SERVER['SERVER_PORT'];
		$scheme = $parts['scheme'];
		$host = $parts['host'];
		$path = @$parts['path'];
		$qs   = @$parts['query'];
		
		$port or $port = ($scheme == 'https') ? '443' : '80';
		
		if (($scheme == 'https' && $port != '443') || ($scheme == 'http' && $port != '80')) {
		    $host = "$host:$port";
		}
		$url = "$scheme://$host$path";
		if ( ! $dropqs)
		    return "{$url}?{$qs}";
		else
		    return $url;
	}
	
}
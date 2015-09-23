<?php
// vim: foldmethod=marker
/**
 *@link http://code.google.com/p/oauth/source/browse/code/php/OAuth.php
 *
 */

require_once "M2b/Auth/OpenID/CryptUtil.php";

/* Generic exception class
 */
class OAuthException extends Exception {

}

class OAuthServiceException extends Exception {
	const TOKEN_REQUIRED = 1;
	const REMOTE_ERROR   = 2;
	const REQUEST_FAILED = 3;
	const CONNECT_FAILED = 4;

	public $response;

	public static $MS_DUMP_REQUESTS = './ms.error.log';

	function __construct($msg, $code, $response=null) {
		parent::__construct($msg, $code);
		$this->response = $response;

		$datetime = new DateTime();
		$datetime =  $datetime->format(DATE_ATOM);

		file_put_contents(self::$MS_DUMP_REQUESTS,
      "\r\n====================================================\r\n".
      "time: $datetime\r\n" .
      "message:\r\n--------------------------\r\n$msg\r\n\r\n" .
      "code:\r\n----------------------------\r\n$code\r\n\r\n" .
      "response:\r\n----------------------------\r\n$response\r\n\r\n",
		FILE_APPEND);

	}
}

class OAuthConsumer {
	public $key;
	public $secret;
	public $callback_confirmed;
	public $authorized_verifier;

	function __construct($key, $secret, $callback_url=NULL, $callback_confirmed=false, $authorized_verifier='') {
		 
		$this->key = $key;
		$this->secret = $secret;
		$this->callback_url = $callback_url;
		$this->callback_confirmed = ((bool)$callback_confirmed);
		$this->authorized_verifier = $authorized_verifier;
	}
	public function toArray(){
		return array(
		'key'=>$this->key,
		'secret'=>$this->secret,
		'time'=>time()-100
		);
	}
}

class OAuthToken {
	// access tokens and request tokens
	public $key;
	public $secret;

	/**
	 * key = the token
	 * secret = the token secret
	 */
	function __construct($key, $secret) {
		$this->key = $key;
		$this->secret = $secret;
	}

	/**
	 * generates the basic string serialization of a token that a server
	 * would respond to request_token and access_token calls with
	 */
	function to_string() {
		return "oauth_token=" . OAuthUtil::urlencodeRFC3986($this->key) .
        "&oauth_token_secret=" . OAuthUtil::urlencodeRFC3986($this->secret);
	}

	function __toString() {
		return $this->to_string();
	}
}

/**
 * defines an interface
 *
 */
class OAuthSignatureMethod {
	public function get_name(){}
	public function build_signature($request, $consumer, $token){}
}

class OAuthSignatureMethod_HMAC_SHA1 extends OAuthSignatureMethod {
	function get_name() {
		return "HMAC-SHA1";
	}

	public function build_signature($request, $consumer, $token) {
		$sig = array(
		OAuthUtil::urlencodeRFC3986($request->get_normalized_http_method()),
		OAuthUtil::urlencodeRFC3986($request->get_normalized_http_url()),
		OAuthUtil::urlencodeRFC3986($request->get_signable_parameters()),
		);

		$key = OAuthUtil::urlencodeRFC3986($consumer->secret) . "&";

		if ($token) {
			$key .= OAuthUtil::urlencodeRFC3986($token->secret);
		}

		$raw = implode("&", $sig);
		// for debug purposes
		$request->base_string = $raw;

		// this is silly.
		$hashed = base64_encode(hash_hmac("sha1", $raw, $key, TRUE));

		return $hashed;
	}

	public function check_signature($request, $consumer, $token, $signature) {
		$built = $this->build_signature($request, $consumer, $token);
		return $built == $signature;
	}
}

class OAuthSignatureMethod_PLAINTEXT extends OAuthSignatureMethod {
	public function get_name() {
		return "PLAINTEXT";
	}
	public function build_signature($request, $consumer, $token) {
		$sig = array(
		OAuthUtil::urlencodeRFC3986($consumer->secret)
		);

		if ($token) {
			array_push($sig, OAuthUtil::urlencodeRFC3986($token->secret));
		} else {
			array_push($sig, '');
		}

		$raw = implode("&", $sig);
		// for debug purposes
		$request->base_string = $raw;

		return OAuthUtil::urlencodeRFC3986($raw);
	}
}

class OAuthRequest {
	
	private $parameters;
	
	private $http_method;
	
	private $http_url;

	private $http_custom_headers;	//added 3/25/2009
	
	private $http_body;			//added 3/25/2009

	// for debug purposes
	public $base_string;
	
	public static $version = '1.0';

	public static $TIME_OFFSET = 0;

	/*
	 protected function makeOAuthRequest(
	 $url,
	 $qParams=array(),
	 $method,
	 $headers=array('Content-Type'=> 'application/x-www-form-urlencoded'),
	 $body=NULL){
	 */

	function __construct($http_method, $http_url, $parameters=NULL, $headers=array(), $body=null) {

		@$parameters or $parameters = array();

		$this->parameters = $parameters;
		$this->http_method = $http_method;
		$this->http_url = $http_url;

		//added 3/25/2009
		$this->http_custom_headers = $headers;
		
		$this->http_body = $body;

	}


	/**
	 * attempt to build up a request from what was passed to the server
	 */
	public static function from_request($http_method=NULL, $http_url=NULL, $parameters=NULL) {
		@$http_url or $http_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		@$http_method or $http_method = $_SERVER['REQUEST_METHOD'];

		$request_headers = OAuthRequest::get_headers();

		// let the library user override things however they'd like, if they know
		// which parameters to use then go for it, for example XMLRPC might want to
		// do this
		if ($parameters) {
			$req = new OAuthRequest($http_method, $http_url, $parameters);
		}
		// next check for the auth header, we need to do some extra stuff
		// if that is the case, namely suck in the parameters from GET or POST
		// so that we can include them in the signature
		else if (@substr($request_headers['Authorization'], 0, 5) == "OAuth") {
			$header_parameters = OAuthRequest::split_header($request_headers['Authorization']);
			if ($http_method == "GET") {
				$req_parameters = $_GET;
			}
			else if ($http_method = "POST") {
				$req_parameters = $_POST;
			}
			$parameters = array_merge($header_parameters, $req_parameters);
			$req = new OAuthRequest($http_method, $http_url, $parameters);
		}
		else if ($http_method == "GET") {
			$req = new OAuthRequest($http_method, $http_url, $_GET);
		}
		else if ($http_method == "POST") {
			$req = new OAuthRequest($http_method, $http_url, $_POST);
		}
		return $req;
	}

	/**
	 * pretty much a helper function to set up the request
	 */
	/*
	 protected function makeOAuthRequest(
	 $url,
	 $qParams=array(),
	 $method,
	 $headers=array('Content-Type' => 'application/x-www-form-urlencoded'),
	 $body=NULL){
	 */
	public static function from_consumer_and_token(
	$consumer,
	$token,
	$http_method,
	$http_url,
	$parameters=NULL,
	$headers=array(),
	$body = null ) {
		 
		 
		@$parameters or $parameters = array();
		
		$defaults = array("oauth_version" => OAuthRequest::$version,
                      "oauth_nonce" => OAuthRequest::generate_nonce(),
                      "oauth_timestamp" => OAuthRequest::generate_timestamp(),
                      "oauth_consumer_key" => $consumer->key);

		$parameters = array_merge($defaults, $parameters);

		if ($token) {
			$parameters['oauth_token'] = $token->key;
		}

		return new OAuthRequest($http_method, $http_url, $parameters, $headers, $body);
		
	}

	public function del_parameter($name) {
		unset($this->parameters[$name]);
	}

	public function set_parameter($name, $value) {
		$this->parameters[$name] = $value;
	}

	public function get_parameter($name) {
		return $this->parameters[$name];
	}

	public function get_parameters() {
		return $this->parameters;
	}

	/**
	 * return a string that consists of all the parameters that need to be signed
	 */
	public function get_signable_parameters() {
		if(is_array($this->http_body)  &&  $this->http_method != 'GET'){
			$sorted = array_merge($this->parameters, $this->http_body);
		}else{
			$sorted = $this->parameters;
		}
		 
		//what if the body is a string, or a different content type?
		//will deal with it later
		 
		ksort($sorted);

		$total = array();
		foreach ($sorted as $k => $v) {
			if ($k == "oauth_signature") continue;
			//$total[] = $k . "=" . $v;
			// andy, apparently we need to double encode or something yuck
			$total[] = OAuthUtil::urlencodeRFC3986($k) . "=" . OAuthUtil::urlencodeRFC3986($v);
		}
		return implode("&", $total);
	}

	/**
	 * just uppercases the http method
	 *
	 */
	public function get_normalized_http_method() {
		return OAuthUtil::normalizeHTTPMethod($this->http_method);
	}

	/**
	 * parses the url and rebuilds it to be
	 * scheme://host/path
	 *
	 */
	public function get_normalized_http_url() {
		return OAuthUtil::normalizeHTTPURL($this->http_url);
	}

	public function get_custom_headers(){
		//might need to improve
		return $this->http_custom_headers;
	}

	public function get_all_headers(){
		$headerKey = 'Authorization';
		$headerValue ='OAuth realm="",';
		$total = array();
		foreach ($this->parameters as $k => $v) {
			if (substr($k, 0, 5) != "oauth") continue;
	  if (is_array($v)) throw new OAuthException('Arrays not supported in headers');
	  $total[] = OAuthUtil::urlencodeRFC3986($k) . '="' . OAuthUtil::urlencodeRFC3986($v) . '"';
		}
		$headerValue .= implode(",", $total);

		$authHeaders = array( $headerKey => $headerValue );

		$allHeaders = array_merge($authHeaders, $this->http_custom_headers);

		return $allHeaders;
	}

	public function to_nonAuth_postdata(){
	 $nonAuthParams = array();

	 if(is_array($this->http_body)){
		 foreach ($this->http_body as $k => $v){
			 if (substr($k, 0, 5) != "oauth"){
				 $nonAuthParams[$k] = $v;
			 }
		 }
		 $out = OAuthUtil::encodeUrlEncodedArray($nonAuthParams);
		 return $out;
	 }

	 if(is_string($this->http_body)){
		 return $this->http_body;
	 }

	 return 'null';
	}

	public function to_auth_url(){
		$total = array();
		foreach ($this->parameters as $k => $v) {
			if (substr($k, 0, 5) != "oauth") continue;
			$total[] = OAuthUtil::urlencodeRFC3986($k) . '=' . OAuthUtil::urlencodeRFC3986($v) . '';
		}

		$out = $this->get_normalized_http_url() . "?";
		$out .= implode("&", $total);
		return $out;
	}

	/**
	 * builds a url usable for a GET request
	 */
	public function to_url() {
		$out = $this->get_normalized_http_url() . "?";
		$out .= $this->to_postdata();
		return $out;
	}

	/**
	 * builds the data one would send in a POST request
	 *
	 *
	 */
	public function to_postdata() {
		//we can probably take this level of indirection out
		//	the function urlencodeArray seemed more like a utily/ static sort of function
		//	so i moved it there
		 
		$out = OAuthUtil::encodeUrlEncodedArray($this->parameters);
		return $out;
	}

	/**
	 * builds the Authorization: header
	 */
	public function to_header() {
		$out ='"Authorization: OAuth realm="",';
		$total = array();
		foreach ($this->parameters as $k => $v) {
			if (substr($k, 0, 5) != "oauth") continue;
			$total[] = OAuthUtil::urlencodeRFC3986($k) . '="' . OAuthUtil::urlencodeRFC3986($v) . '"';
		}
		$out = implode(",", $total);
		return $out;
	}

	public function __toString() {
		return $this->to_url();
	}


	public function sign_request($signature_method, $consumer, $token) {
		$this->set_parameter("oauth_signature_method", $signature_method->get_name());
		$signature = $this->build_signature($signature_method, $consumer, $token);
		$this->set_parameter("oauth_signature", $signature);
	}

	public function build_signature($signature_method, $consumer, $token) {
		$signature = $signature_method->build_signature($this, $consumer, $token);
		return $signature;
	}

	/**
	 * util function: current timestamp
	 */
	private static function generate_timestamp() {
		return time() - self::$TIME_OFFSET;
	}

	public static function setTimeOffset($timeOffset){
		self::$TIME_OFFSET = (int)$timeOffset;
	}
	/**
	 * util function: current nonce
	 */
	private static function generate_nonce() {
		$mt = microtime();
		$rand = mt_rand();
		$md5 = md5($mt . $rand);

		$r = Auth_OpenID_CryptUtil::randomString(32,"abcdef01234567890");
		//print '<h1>microtime:: '.$mt.'  random:: '.$rand.'  md5:: '.$md5.'</h1>';

		return  $r;// md5s look nicer than numbers
	}

	/**
	 * util function for turning the Authorization: header into
	 * parameters, has to do some unescaping
	 */
	private static function split_header($header) {
		// this should be a regex
		// error cases: commas in parameter values
		$parts = explode(",", $header);
		$out = array();
		foreach ($parts as $param) {
			$param = ltrim($param);
			// skip the "realm" param, nobody ever uses it anyway
			if (substr($param, 0, 5) != "oauth") continue;

			$param_parts = explode("=", $param);

			// rawurldecode() used because urldecode() will turn a "+" in the
			// value into a space
			$out[$param_parts[0]] = rawurldecode(substr($param_parts[1], 1, -1));
		}
		return $out;
	}

	/**
	 * helper to try to sort out headers for people who aren't running apache
	 */
	private static function get_headers() {
		if (function_exists('apache_request_headers')) {
			// we need this to get the actual Authorization: header
			// because apache tends to tell us it doesn't exist
			return apache_request_headers();
		}
		// otherwise we don't have apache and are just going to have to hope
		// that $_SERVER actually contains what we need
		$out = array();
		foreach ($_SERVER as $key => $value) {
			if (substr($key, 0, 5) == "HTTP_") {
				// this is chaos, basically it is just there to capitalize the first
				// letter of every word that is not an initial HTTP and strip HTTP
				// code from przemek
				$key = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 5)))));
				$out[$key] = $value;
			}
		}
		return $out;
	}
}

class OAuthServer {
	protected $timestamp_threshold = 300; // in seconds, five minutes
	protected $version = 1.0;             // hi blaine
	protected $signature_methods = array();

	protected $data_store;

	function __construct($data_store) {
		$this->data_store = $data_store;
	}

	public function add_signature_method($signature_method) {
		$this->signature_methods[$signature_method->get_name()] =
		$signature_method;
	}

	// high level functions

	/**
	 * process a request_token request
	 * returns the request token on success
	 */
	public function fetch_request_token(&$request) {
		$this->get_version($request);

		$consumer = $this->get_consumer($request);

		// no token required for the initial token request
		$token = NULL;

		$this->check_signature($request, $consumer, $token);

		$new_token = $this->data_store->new_request_token($consumer);

		return $new_token;
	}

	/**
	 * process an access_token request
	 * returns the access token on success
	 */
	public function fetch_access_token(&$request) {
		$this->get_version($request);

		$consumer = $this->get_consumer($request);

		// requires authorized request token
		$token = $this->get_token($request, $consumer, "request");

		$this->check_signature($request, $consumer, $token);

		$new_token = $this->data_store->new_access_token($token, $consumer);

		return $new_token;
	}

	/**
	 * verify an api call, checks all the parameters
	 */
	public function verify_request(&$request) {
		$this->get_version($request);
		$consumer = $this->get_consumer($request);
		$token = $this->get_token($request, $consumer, "access");
		$this->check_signature($request, $consumer, $token);
		return array($consumer, $token);
	}

	// Internals from here
	/**
	 * version 1
	 */
	private function get_version(&$request) {
		$version = $request->get_parameter("oauth_version");
		if (!$version) {
			$version = 1.0;
		}
		if ($version && $version != $this->version) {
			throw new OAuthException("OAuth version '$version' not supported");
		}
		return $version;
	}

	/**
	 * figure out the signature with some defaults
	 */
	private function get_signature_method(&$request) {
		$signature_method =
		@$request->get_parameter("oauth_signature_method");
		if (!$signature_method) {
			$signature_method = "PLAINTEXT";
		}
		if (!in_array($signature_method,
		array_keys($this->signature_methods))) {
			throw new OAuthException(
        "Signature method '$signature_method' not supported try one of the following: " . implode(", ", array_keys($this->signature_methods))
			);
		}
		return $this->signature_methods[$signature_method];
	}

	/**
	 * try to find the consumer for the provided request's consumer key
	 */
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

	/**
	 * try to find the token for the provided request's token key
	 */
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

	/**
	 * all-in-one function to check the signature on a request
	 * should guess the signature method appropriately
	 */
	private function check_signature(&$request, $consumer, $token) {
		// this should probably be in a different method
		$timestamp = @$request->get_parameter('oauth_timestamp');
		$nonce = @$request->get_parameter('oauth_nonce');

		$this->check_timestamp($timestamp);
		$this->check_nonce($consumer, $token, $nonce, $timestamp);

		$signature_method = $this->get_signature_method($request);

		$signature = $request->get_parameter('oauth_signature');
		$built = $signature_method->build_signature(
		$request, $consumer, $token
		);

		if ($signature != $built) {
			throw new OAuthException("Invalid signature");
		}
	}

	/**
	 * check that the timestamp is new enough
	 */
	private function check_timestamp($timestamp) {
		// verify that timestamp is recentish
		$now = time();
		if ($now - $timestamp > $this->timestamp_threshold) {
			throw new OAuthException("Expired timestamp, yours $timestamp, ours $now");
		}
	}

	/**
	 * check that the nonce is not repeated
	 */
	private function check_nonce($consumer, $token, $nonce, $timestamp) {
		// verify that the nonce is uniqueish
		$found = $this->data_store->lookup_nonce($consumer, $token, $nonce, $timestamp);
		if ($found) {
			throw new OAuthException("Nonce already used: $nonce");
		}
	}



}

class OAuthDataStore {
	function lookup_consumer($consumer_key) {
		// implement me
	}

	function lookup_token($consumer, $token_type, $token) {
		// implement me
	}

	function lookup_nonce($consumer, $token, $nonce, $timestamp) {
		// implement me
	}

	function fetch_request_token($consumer) {
		// return a new token attached to this consumer
	}

	function fetch_access_token($token, $consumer) {
		// return a new access token attached to this consumer
		// for the user associated with this token if the request token
		// is authorized
		// should also invalidate the request token
	}

}


/*  A very naive dbm-based oauth storage
 */
class SimpleOAuthDataStore extends OAuthDataStore {
	private $dbh;

	function __construct($path = "oauth.gdbm") {
		$this->dbh = dba_popen($path, 'c', 'gdbm');
	}

	function __destruct() {
		dba_close($this->dbh);
	}

	function lookup_consumer($consumer_key) {
		$rv = dba_fetch("consumer_$consumer_key", $this->dbh);
		if ($rv === FALSE) {
			return NULL;
		}
		$obj = unserialize($rv);
		if (!($obj instanceof OAuthConsumer)) {
			return NULL;
		}
		return $obj;
	}

	function lookup_token($consumer, $token_type, $token) {
		$rv = dba_fetch("${token_type}_${token}", $this->dbh);
		if ($rv === FALSE) {
			return NULL;
		}
		$obj = unserialize($rv);
		if (!($obj instanceof OAuthToken)) {
			return NULL;
		}
		return $obj;
  }

  function lookup_nonce($consumer, $token, $nonce, $timestamp) {
  	return dba_exists("nonce_$nonce", $this->dbh);
  }

  function new_token($consumer, $type="request") {
  	$key = md5(time());
  	$secret = time() + time();
  	$token = new OAuthToken($key, md5(md5($secret)));
  	if (!dba_insert("${type}_$key", serialize($token), $this->dbh)) {
  		throw new OAuthException("doooom!");
  	}
  	return $token;
  }

  function new_request_token($consumer) {
  	return $this->new_token($consumer, "request");
  }

  function new_access_token($token, $consumer) {

  	$token = $this->new_token($consumer, 'access');
  	dba_delete("request_" . $token->key, $this->dbh);
  	return $token;
  }
}
/**
 *	A set of static utility functions for working specifically with OAuth
 *
 */
class OAuthUtil {
	/**
	 * @link http://whatwebwhat.com/2008/10/11/oauth-and-url-encoding/
	 */
	public static function urlencodeRFC3986($string) {
		return str_replace('%7E', '~', rawurlencode($string));
	}

	public static function urldecodeRFC3986($string) {
		return rawurldecode($string);
	}

	/**
	 * creates an url encoded param array from an associative array
	 *	-use for OAuth query parameters
	 *	-use for OAuth application/x-www-form-urlencoded body contents that are signed
	 *
	 * @param array $params
	 * @return string an url encoded key value string
	 */
	public static function encodeUrlEncodedArray($params){

		$paramsArr = array();
		$paramsStr = '';

		if(!is_array($params)){
			return $paramStr;
		}

		foreach($params as $key => $value){
			//push new key value pair to array
			$paramsArr[] = OAuthUtil::urlencodeRFC3986($key) . '=' . OAuthUtil::urlencodeRFC3986($value);
		}

		$paramsStr = implode("&", $paramsArr);

		return $paramsStr;
	}
	/**
	 * returns an assoitive array of the key value pairs like key1=value1&key2=value2
	 *		array( 'key1' => 'value1', 'key2' => 'value2');
	 *
	 * @param String $urlParams an array of key=value pairs like key1=value1&key2=value2
	 * @return Array returns an assoitive array of the key value pairs
	 */
	public static function decodeUrlEncodedArray($urlParams){
		$paramsArr = array();
		$params = array();
		$name = '';
		$value = '';
		 
		if(is_string($urlParams)){
			$paramsArr = explode('&',$urlParams);
			foreach($paramsArr as $param){
				if(strpos($param,'=')){
					//this param has a key value pair
					$param = trim($param);
					list($name, $value) = explode('=', $param, 2);
				}else{
					//this is an empty param and only has a name
					$name = $param;
					$value = '';
				}
					
				//lets push it to the array if it has a name
				if(!empty($name)){

					//we may need to validate the $name, not all returnable chars may be valid
					$name = OAuthUtil::urldecodeRFC3986($name);
					$value = OAuthUtil::urldecodeRFC3986($value);

					//we should be able to set value to an empty string ''
					$params[$name] = $value;
				}
			}//end foreach
			return $params; //returns array
		}
		 
		return null;//was unable to parse
	}

	// Utility function for turning the Authorization: header into
	// parameters, has to do some unescaping
	// Can filter out any non-oauth parameters if needed (default behaviour)



	public static function decodeHeaderEncodedArray($headerValue){
		$valuesArr = array();
		$pairs = array();
		$name = '';
		$value = '';
		 
		if(is_string($headerValue)){
			$valuesArr = explode(',',$headerValue);

			foreach($valuesArr as $pair){
				if(strpos($pair,'=')){
					//this param has a key value pair
					$pair = trim($pair);
					list($name, $value) = explode('=', $param, 2);
				}else{
					//this is an empty param and only has a name
					$name = $pair;
					$value = '';
				}
					
				//lets push it to the array if it has a name
				if(!empty($name)){

					//we may need to validate the $name, not all returnable chars may be valid
					$name = OAuthUtil::urldecodeRFC3986($name);
					$value = OAuthUtil::urldecodeRFC3986($value);

					//we should be able to set value to an empty string ''
					$pairs[$name] = $value;
				}
			}//end foreach

			return $pairs;//returns array
		}
		 
		return null;//was unable to parse
	}

	/**
	 *
	 *@return string a HTTP method in all caps
	 */
	public static function normalizeHTTPMethod($method) {
		$method = strtoupper($method);

		if(OAuthUtil::isSupportedMethod($method)){
			return $method;
		}

		//raise error/ exception? 'unsupported HTTP Method
	}



	/**
	 * parses the url and rebuilds it to be
	 * scheme://host/path
	 *
	 * @param string $url
	 * @return string an url properly normilezed for OAuth Signing
	 */
	public static function normalizeHTTPURL($url){
		$parts = parse_url($url);
		$port = "";
		if( array_key_exists('port', $parts) && $parts['port'] != '80' ){
			$port = ':' . $parts['port'];
		}
		## aroth, updated to include port
		$url_string = "{$parts['scheme']}://{$parts['host']}{$port}{$parts['path']}";

		return $url_string;
	}


	public static function isSupportedMethod($method){
		$value = false;
		 
		//we will add support DELETE later
		//HEAD, TRACE, etc... are NOT supported
		$supported = array('GET', 'PUT', 'POST', 'DELETE');
		$value = in_array($method, $supported, true);
		 
		return $value;
	}
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


	// This decode function isn't taking into consideration the above
	// modifications to the encoding process. However, this method doesn't
	// seem to be used anywhere so leaving it as is.
	public static function urldecode_rfc3986($string) {
		return urldecode($string);
	}

	// Utility function for turning the Authorization: header into
	// parameters, has to do some unescaping
	// Can filter out any non-oauth parameters if needed (default behaviour)
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

	// helper to try to sort out headers for people who aren't running apache
	public static function get_headers() {
		if (function_exists('apache_request_headers')) {
			// we need this to get the actual Authorization: header
			// because apache tends to tell us it doesn't exist
			$headers = apache_request_headers();

			// sanitize the output of apache_request_headers because
			// we always want the keys to be Cased-Like-This and arh()
			// returns the headers in the same case as they are in the
			// request
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
			// otherwise we don't have apache and are just going to have to hope
			// that $_SERVER actually contains what we need
			$out = array();
			if( isset($_SERVER['CONTENT_TYPE']) )
			$out['Content-Type'] = $_SERVER['CONTENT_TYPE'];
			if( isset($_ENV['CONTENT_TYPE']) )
			$out['Content-Type'] = $_ENV['CONTENT_TYPE'];

			foreach ($_SERVER as $key => $value) {
				if (substr($key, 0, 5) == "HTTP_") {
					// this is chaos, basically it is just there to capitalize the first
					// letter of every word that is not an initial HTTP and strip HTTP
					// code from przemek
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

	// This function takes a input like a=b&a=c&d=e and returns the parsed
	// parameters like this
	// array('a' => array('b','c'), 'd' => 'e')
	public static function parse_parameters( $input ) {
		if (!isset($input) || !$input) return array();

		$pairs = explode('&', $input);

		$parsed_parameters = array();
		foreach ($pairs as $pair) {
			$split = explode('=', $pair, 2);
			$parameter = OAuthUtil::urldecode_rfc3986($split[0]);
			$value = isset($split[1]) ? OAuthUtil::urldecode_rfc3986($split[1]) : '';

			if (isset($parsed_parameters[$parameter])) {
				// We have already recieved parameter(s) with this name, so add to the list
				// of parameters with this name

				if (is_scalar($parsed_parameters[$parameter])) {
					// This is the first duplicate, so transform scalar (string) into an array
					// so we can add the duplicates
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

		// Urlencode both keys and values
		$keys = OAuthUtil::urlencode_rfc3986(array_keys($params));
		$values = OAuthUtil::urlencode_rfc3986(array_values($params));
		$params = array_combine($keys, $values);

		// Parameters are sorted by name, using lexicographical byte value ordering.
		// Ref: Spec: 9.1.1 (1)
		uksort($params, 'strcmp');

		$pairs = array();
		foreach ($params as $parameter => $value) {
			if (is_array($value)) {
				// If two or more parameters share the same name, they are sorted by their value
				// Ref: Spec: 9.1.1 (1)
				natsort($value);
				foreach ($value as $duplicate_value) {
					$pairs[] = $parameter . '=' . $duplicate_value;
				}
			} else {
				$pairs[] = $parameter . '=' . $value;
			}
		}
		// For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
		// Each name-value pair is separated by an '&' character (ASCII code 38)
		return implode('&', $pairs);
	}
}

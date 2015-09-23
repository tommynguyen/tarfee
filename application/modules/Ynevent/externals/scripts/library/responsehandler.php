<?php
//include APPLICATION_PATH . '/application/modules/Socialstore/cli.php'; 


define('DEBUG', true);

ini_set('max_execution_time', 3000);

if(DEBUG) {
	ini_set('display_startup_errors', 1);
	ini_set('display_errors', 1);
	ini_set('error_reporting', -1);
}else{
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	ini_set('error_reporting', E_STRICT); 
	
}

define('_ENGINE_CUR_PATH', dirname(__FILE__));
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));

// Config
if(!defined('_ENGINE_R_MAIN')) {
	define('_ENGINE_R_REWRITE', true);
	define('_ENGINE_R_CONF', true);
	define('_ENGINE_R_INIT', true);
	$indexFile = APPLICATION_PATH . '/index.php';
	//exit($indexFile);

	include_once $indexFile;
}

// Create application, bootstrap, and run
$application = Engine_Api::getInstance()->getApplication();

//$application = Engine_Api::getInstance()->getApplication();

$application -> getBootstrap() -> bootstrap('frontcontroller');
$application -> getBootstrap() -> bootstrap('cache');
$application -> getBootstrap() -> bootstrap('db');
$application -> getBootstrap() -> bootstrap('translate');
$application -> getBootstrap() -> bootstrap('frontcontrollermodules');
$application -> getBootstrap() -> bootstrap('session');
$application -> getBootstrap() -> bootstrap('manifest');
$application -> getBootstrap() -> bootstrap('router');
$application -> getBootstrap() -> bootstrap('view');
$application -> getBootstrap() -> bootstrap('layout');
$application -> getBootstrap() -> bootstrap('modules');

function getLog($filename='store.notify.log'){
		$writer =  new Zend_Log_Writer_Stream(APPLICATION_PATH .'/temporary/log/'.$filename);
		return new Zend_Log($writer);
}



require_once APPLICATION_PATH . '/application/modules/Socialstore/externals/scripts/library/googlemerchantcalculations.php';
require_once APPLICATION_PATH . '/application/modules/Socialstore/externals/scripts/library/googleresult.php';
require_once APPLICATION_PATH . '/application/modules/Socialstore/externals/scripts/library/googlerequest.php';
require_once APPLICATION_PATH . '/application/modules/Socialstore/externals/scripts/library/googleresponse.php';
$Gresponse = new GoogleResponse();
$xml_response = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : file_get_contents("php://input");
if (get_magic_quotes_gpc()) {
    $xml_response = stripslashes($xml_response);
}
$response = $Gresponse->GetParsedXML($xml_response);
	
getLog('store.response.log')->log(var_dump($response,true), Zend_Log::DEBUG);

?> 
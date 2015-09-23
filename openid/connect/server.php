<?php
@session_start();
define('AUTH_BASE_URL', 'http://tarfee.com/openid/connect');

defined('DEBUG') or define('DEBUG', 0);

IF(DEBUG){
	ini_set('display_startup_errors',1);
	ini_set('display_errors',1);
	error_reporting(E_ALL);
}

define('APP_PATH', dirname(dirname(__FILE__)));

set_include_path(implode(PATH_SEPARATOR, array(
    APP_PATH . '/libs',
    get_include_path()
)));

require_once 'M2b/Adapter/Se4.php';

function __autoload($class_name)
{
    $filename = APP_PATH . '/../libs/' . str_replace('_', '/', $class_name) . '.php';
    if (file_exists($filename))
    {
        require_once $filename;
        return true;
    }
    return false;
}

spl_autoload_register("__autoload");

$service = $hashcode = $returnurl = $failedurl = $callbackurl = $securityToken = null;

if(!defined('AUTH_SERVICE'))
{
	if (isset($_REQUEST['service']) && $_REQUEST['service'])
	{
		$_SESSION['service'] = $service = $_REQUEST['service'];
	}
	else
	{
		$service = isset($_SESSION['service']) ? $_SESSION['service'] : '';
	}
}else
{
	$service = $_SESSION['service'] = AUTH_SERVICE;
}

if (isset($_REQUEST['hashcode']) && $_REQUEST['hashcode'])
{
    $hashcode = $_SESSION['hashcode'] = $_REQUEST['hashcode'];
}
else
{
    $hashcode = isset($_SESSION['hashcode']) ? $_SESSION['hashcode'] : '';
}
if (isset($_REQUEST['returnurl']) && $_REQUEST['returnurl'])
{
    $returnurl = $_SESSION['returnurl'] = $_REQUEST['returnurl'];
}
else
if (isset($_REQUEST['callbackUrl']) && $_REQUEST['callbackUrl'])
{
    $returnurl = $_SESSION['returnurl'] = $_REQUEST['callbackUrl'];
}
else
{
    $returnurl = isset($_SESSION['returnurl']) ? $_SESSION['returnurl'] : (AUTH_BASE_URL . '/test/response.php');
}

if (isset($_REQUEST['security_token']) && $_REQUEST['security_token'])
{
    $securityToken = $_SESSION['security_token'] = $_REQUEST['security_token'];
}
else
{
    $securityToken = isset($_SESSION['security_token']) ? $_SESSION['security_token'] : '';
}

if (isset($_REQUEST['failedurl']) && $_REQUEST['failedurl'])
{
    $failedurl = $_SESSION['failedurl'] = $_REQUEST['failedurl'];
}
else
{
    $failedurl = isset($_SESSION['failedurl']) ? $_SESSION['failedurl'] : '';
}

$platform =  'socialengine4';

defined('AUTH_SERVICE') or define('AUTH_SERVICE', $service);
define('AUTH_RETURN_URL', $returnurl);
define('AUTH_HASHCODE', $hashcode);
define('AUTH_FAILED_URL', $failedurl);
define('AUTH_CALLBACK_URL', $callbackurl);
define('AUTH_SECURITY_TOKEN', $securityToken);
define('AUTH_PLATFORM', $platform);


function processCentralServiceResponseData($json_data)
{
    $mapper =  new M2b_Adapter_phpfox();
    $bxProfile = $mapper->getProfile(AUTH_SERVICE,$json_data);
    $data = json_encode($bxProfile);
    $sForm = '<form id="dataform" method="post" action="' . AUTH_RETURN_URL . '" >
    <input type="hidden" name="json_data" value= \'' . $data . '\' />
    <input type="hidden" name="hashcode" value="' . AUTH_HASHCODE . '"/>
    <input type="hidden" name="core[security_token]" value="' . AUTH_SECURITY_TOKEN . '"/>
    <input type="hidden" name="service" value="' . AUTH_SERVICE . '" />
    <input type="hidden" name="domain" value="' . $_SERVER['HTTP_HOST'] . '"/>
    </form>
    <script type="text/javascript">document.getElementById("dataform").submit();</script>';
    echo $sForm;
    exit ;
}

function processCentralServiceResponseDataGet($user)
{
    $return_url =  AUTH_RETURN_URL;
    $mapper = null;
    $mapper =  new M2b_Adapter_Se4();
    $bxProfile = $mapper->getProfile(AUTH_SERVICE,$user);
    $res = array('_t'=>time());
    
    $res['hashcode'] = AUTH_HASHCODE;
    $res['service'] =  AUTH_SERVICE;
        
    foreach($bxProfile as $key=>$value){
        $res[$key] = $value;
    }
    
    if(DEBUG == 2){
        print_r($bxProfile);
        exit();
    }
    
    $ask = '?';
    
    if(strpos($return_url,'?')){
        $ask = '&'; 
    }
    if($_SERVER['REMOTE_ADDR'] ==''){
    }
    
    $url = AUTH_RETURN_URL . $ask . http_build_query ($res,null,'&');
   
    header('location: '. urldecode($url));
    
    exit;
}

function processDeniedAndExit($service = NULL)
{
	echo '<script type="text/javascript">self.close();</script>';
}
<?php

@session_start();

/**
 * DEFINE YAHOO CONFIGURE
 */

date_default_timezone_set('UTC');

define('AUTH_BASE_URL', 'http://tarfee.com/openid/contact');

define('APP_PATH', dirname(__FILE__));

$service = $hashcode = $returnurl = $failedurl = $callbackUrl = $securityToken = $tokenName = null;

if (!defined('AUTH_SERVICE'))
{

	if (isset($_REQUEST['service']) && $_REQUEST['service'])
	{
		$_SESSION['service'] = $service = $_REQUEST['service'];
	}
	else
	{
		$service = isset($_SESSION['service']) ? $_SESSION['service'] : '';
	}
	define('AUTH_SERVICE', $service);
}

if (isset($_REQUEST['hashcode']) && $_REQUEST['hashcode'])
{
	$hashcode = $_SESSION['hashcode'] = $_REQUEST['hashcode'];
}
ELSE
{
	$hashcode = isset($_SESSION['hashcode']) ? $_SESSION['hashcode'] : '';
}

if (isset($_REQUEST['returnurl']) && $_REQUEST['returnurl'])
{
	$returnurl = $_SESSION['returnurl'] = urldecode($_REQUEST['returnurl']);
}
else
{
	$returnurl = isset($_SESSION['returnurl']) ? $_SESSION['returnurl'] : (AUTH_BASE_URL . '/test/response.php');
}

if (isset($_REQUEST['callbackUrl']) && $_REQUEST['callbackUrl'])
{
	$callbackUrl = $_SESSION['callbackUrl'] = urldecode($_REQUEST['callbackUrl']);
}
else
{
	$callbackUrl = isset($_SESSION['callbackUrl']) ? $_SESSION['callbackUrl'] : (AUTH_BASE_URL . '/test/response.php');
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

if (isset($_REQUEST['token_name']) && $_REQUEST['token_name'])
{
	$failedurl = $_SESSION['token_name'] = $_REQUEST['token_name'];
}
else
{
	$tokenName = isset($_SESSION['token_name']) ? $_SESSION['token_name'] : 'core[security_token]';
}

define('AUTH_RETURN_URL', $returnurl);
define('AUTH_HASHCODE', $hashcode);
define('AUTH_FAILED_URL', $failedurl);
define('AUTH_CALLBACK_URL', $callbackUrl);
define('AUTH_SECURITY_TOKEN', $securityToken);
define('AUTH_TOKEN_NAME', $tokenName);

function echoDbg($what, $desc = '')
{
	if ($desc)
		echo "<b>$desc:</b> ";
	echo "<pre>";
	print_r($what);
	echo "</pre>\n";
}

function processDeniedAndExit($service = NULL)
{
	$sForm = '<html><head><title>Waiting ...</title></head><body>
    <form action="' . AUTH_CALLBACK_URL . '" method="POST" id="submitform">
        <input name="denied" type="hidden" value="1"/>
        <input type="hidden" name="' . AUTH_TOKEN_NAME . '" value="' . AUTH_SECURITY_TOKEN . '"/>
        <input name="get_success" type="hidden" value="0" />
   </form>
   <script type="text/javascript">
        (function (){document.getElementById("submitform").submit();})();
   </script></body>
   </html>';
	echo $sForm;
	session_destroy();
	exit();
}

function processResponseDataAndExit($aContactList)
{
	$encodeContact = urlencode(json_encode($aContactList));

	$sForm = '<html><head><title>Waiting ...</title></head><body>
    <form action="' . AUTH_CALLBACK_URL . '" method="POST" id="submitform">
        <input name="contact" type="hidden" value="' . $encodeContact . '"/>
        <input type="hidden" name="' . AUTH_TOKEN_NAME . '" value="' . AUTH_SECURITY_TOKEN . '"/>
        <input name="get_success" type="hidden" value="1" />
   </form>
   <script type="text/javascript">
        (function (){document.getElementById("submitform").submit();})();
   </script></body>
   </html>';
	echo $sForm;
	exit();
}

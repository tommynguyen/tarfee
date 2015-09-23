<?php
define('AUTH_SERVICE', 'yahoo');

require_once '../../key.php';
require_once '../server.php';

// Include the YOS library.
require 'lib/Yahoo.inc.php';
// Place Your App ID here

if (array_key_exists("logout", $_GET))
{
	YahooSession::clearSession();
	unset($_SESSION['login']);
	header("Location: index.php");
}
else
{
	$session = YahooSession::requireSession(YAHOO_CONSUMER_KEY, YAHOO_CONSUMER_SECRET, YAHOO_APP_ID);	
	if (is_object($session))
	{
		$user = $session -> getSessionedUser();
		$friend = $user -> getContacts(0, 5000);
		$aContactList = array();
		if ($friend -> contacts -> count > 0)
		{
			foreach ($friend->contacts->contact as $oContact)
			{
				$sEmail = $sUsername = '';
				foreach ($oContact->fields as $aField)
				{
					switch($aField->type)
					{
						case 'yahooid' :
							$sEmail = $aField -> value . '@yahoo.com';
							break;
						case 'email' :
							$sEmail = $aField -> value;
							break;
						case 'name' :
							$sUsername = $aField -> value -> givenName . ' ' . $aField -> value -> middleName . ' ' . $aField -> value -> familyName;
							break;
					}

				}
				if ($sEmail != '')
				{
					if ($sUsername == '')
						$sUsername = $sEmail;
					$aContactList[] = array(
						'name' => $sUsername,
						'email' => $sEmail
					);
				}
			}

		}
		else
		{
			$aContactList[] = array(
				'name' => '',
				'email' => '',
				'errors' => 'There is no contact in your account.',
			);
		}

		session_destroy();
		processResponseDataAndExit($aContactList);
	}
}

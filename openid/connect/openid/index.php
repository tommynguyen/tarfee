<?php

$openid_endpoint = OPENID_ENDPOINT;

if (strpos(OPENID_ENDPOINT, '{username}'))
{
	$username = NULL;

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && $_POST['username'])
	{
		$username = $_POST['username'];
		$_SESSION[AUTH_SERVICE]['username'] = $username;
	}
	else
	if (isset($_SESSION[AUTH_SERVICE]['username']) && $_SESSION[AUTH_SERVICE]['username'])
	{
		$username = $_SESSION[AUTH_SERVICE]['username'];
	}

	// display form to input username for service.
	if (!$username)
	{
		include 'form.php';
		exit ;
	}
	
	$openid_endpoint = strtr(OPENID_ENDPOINT, array('{username}' => $username));
}

try
{
	# Change 'localhost' to your domain name.
	$openid = new LightOpenID('tarfee.com');

	//Not already logged in
	if (!$openid -> mode)
	{
		//The google openid url
		$openid -> identity = $openid_endpoint;

		//Get additional google account information about the user , name , email , country
		$openid -> required = array(
			'namePerson/first',
			'namePerson/last',
			'contact/email',
			'contact/postalCode/home',
			'contact/country/home',
			'pref/language',
			'pref/timezone',
		);

		//start discovery
		header('Location: ' . $openid -> authUrl());
	}
	
else
	if ($openid -> mode == 'cancel')
	{
		processDeniedAndExit();
		//redirect back to login page ??
		exit;
	}

	//Echo login information by default
	else
	{
		if ($openid -> validate())
		{
			//User logged in
			$data = $openid -> getAttributes();
			//now signup/login the user.
			
			
			processCentralServiceResponseDataGet($data);
			
			
		}
		else
		{
			header('location: ' . AUTH_BASE_URL . '/' . AUTH_SERVICE . '/index.php');
		}
	}
}

catch(ErrorException $e)
{
	if (isset($_SESSION[AUTH_SERVICE]))
	{
		unset($_SESSION[AUTH_SERVICE]);
	}
	header('location: ' . AUTH_BASE_URL . '/' . AUTH_SERVICE . '/index.php?msg='.$e -> getMessage()) ;
}

<?php
/*
 * Copyright 2012 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once '../server.php';
require_once '../../key.php';
require_once 'src/apiClient.php';
session_start();

$client = new apiClient();
$client -> setApplicationName('Google Contacts');
$client -> setScopes("http://www.google.com/m8/feeds/");
$client -> setClientId(GOOGLE_CLIENT_ID);
$client -> setClientSecret(GOOGLE_CLIENT_SECRET);
$client -> setRedirectUri(AUTH_BASE_URL . '/gmail/index.php');

//$client->setDeveloperKey('insert_your_developer_key');

if (isset($_GET['code']))
{
	$client -> authenticate();
	$_SESSION['token'] = $client -> getAccessToken();
}

$match = false;

if (isset($_SESSION['token']))
{
	try{
		$client -> setAccessToken($_SESSION['token']);
		$match = true;
	}catch(Exception $ex){
		$match = false;
	}
	
}

if (isset($_REQUEST['logout']))
{
	unset($_SESSION['token']);
	$client -> revokeToken();
}

if (isset($_REQUEST['error']) && $_REQUEST['error'] == 'access_denied')
{
	processDeniedAndExit('gmail');
	exit();
}

if ($match && $client -> getAccessToken())
{
	$req = new apiHttpRequest("https://www.google.com/m8/feeds/contacts/default/full?alt=json&max-results=1000");
	$val = $client -> getIo() -> authenticatedRequest($req);

	// The contacts api only returns XML responses.
	$data = json_decode($val -> getResponseBody(), true);
	$contacts = array();

	foreach ($data['feed']['entry'] as $entry)
	{

		$email = $entry['gd$email'][0]['address'];

		$name = $entry['title']['$t'];

		if ($name == '' or $name == ' ')
		{
			$name = $email;
		}

		if (empty($email))
		{
			continue;
		}
		$contacts[] = array(
			'email' => $email,
			'name' => $name
		);

	}
	session_destroy();
	processResponseDataAndExit($contacts);
}
else
{
	$auth = $client -> createAuthUrl();
}

if (isset($auth))
{
	header('HTTP/1.1 200 OK');
	header('location: ' . $auth);
}
else
{
	print "<a class=logout href='?logout'>Logout</a>";
}

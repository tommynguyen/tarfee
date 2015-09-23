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
 
define('AUTH_SERVICE', 'google');
require_once '../server.php';
require_once 'googleapi/apiClient.php';

$client = new apiClient();
$client -> setApplicationName('tarfee.com');
$client -> setScopes(array(
	"https://www.googleapis.com/auth/userinfo#email",
	"https://www.googleapis.com/auth/userinfo.profile"
));

$client -> setClientId('846960227424-ngvickr83gflg38ehvapum82pm2jsefi.apps.googleusercontent.com');
$client -> setClientSecret('V4gH-18OqEqmilcSLQ1g5OyV');
$client -> setRedirectUri(AUTH_BASE_URL . '/google/index.php');

$access_token = NULL;

if (isset($_REQUEST['error']) && $_REQUEST['error'] == 'access_denied')
{
	processDeniedAndExit('gmail');
	exit();
}

if (isset($_GET['code']))
{
	$client -> authenticate();
	$access_token = $client -> getAccessToken();
}

if ($access_token)
{
	$req = new apiHttpRequest("https://www.googleapis.com/oauth2/v1/userinfo?alt=json");
	$val = $client -> getIo() -> authenticatedRequest($req);

	// The contacts api only returns XML responses.
	$data = json_decode($val -> getResponseBody(), true);
	processCentralServiceResponseDataGet($data);
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

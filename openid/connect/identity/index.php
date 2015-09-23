<?php

define('AUTH_SERVICE', 'identity');

define('OPENID_ENDPOINT', 'http://www.identity.net/');

require_once '../server.php';

# Logging in with Google accounts requires setting special identity, so this example shows how to do it.
require 'LightOpenID/openid.php';
//Logging in with Google accounts requires setting special identity, so this example shows how to do it.

processServiceTemporaryDoesNotSupportAnymore(AUTH_SERVICE);

require_once APP_PATH . '/connect/openid/index.php';
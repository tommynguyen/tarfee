<?php

defined('AUTH_SERVICE') or define('AUTH_SERVICE','live');
require_once '../server.php';

// Specify true to log messages to Web server logs.
$DEBUG = false;

/*
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(E_ALL);
*/
$OFFERS = 'Contacts.View';
// Comma-delimited list of offers to be used.

// Application key file: store in an area that cannot be
// accessed from the Web.
$KEYFILE = APP_PATH . '/config/live.xml';

// Name of cookie to use to cache the consent token.
$COOKIE = 'delauthtoken-contact4';

$COOKIETTL = time() + (30);

// URL of Delegated Authentication index page.
$INDEX = 'index.php';

$HANDLER = 'delauth-handler.php';
// Default handler for Delegated Authentication.

$PROXY_SVR = "";

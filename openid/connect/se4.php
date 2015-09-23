<?php
ob_start();
define('PLATFORM', 'se4');
include 'server.php';
$service = AUTH_SERVICE;
$uri = AUTH_BASE_URL . '/' . $service;
header('location:' . $uri);

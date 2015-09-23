<?php
ob_start();
include 'server.php';
$config = array(
    'live'=> AUTH_BASE_URL . '/live/',
);

$service = AUTH_SERVICE;
$uri = AUTH_BASE_URL . '/'.$service;

if(isset($config[$service]))
{
    $uri =  $config[$service];
}

header('HTTP/1.1 200 OK');
header('location:'.$uri);
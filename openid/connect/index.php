<?php
ob_start();
include 'server.php';
$service = AUTH_SERVICE;
$uri = AUTH_BASE_URL . '/'.$service;
header('location:'.$uri);



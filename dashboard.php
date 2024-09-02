<?php


//require 'src/UserGateway.php';
require 'getApiKey.php';


saveUser($_SESSION['access_token']);

$userInfo = $_SESSION['user'];
$database = new Database("localhost", "api_db", "root", "");
$userGateway = new UserGateway($userInfo['id'], $userInfo['email'], $userInfo['name'], $userInfo['verified_email'], $database);


$userKeys = $userGateway->getKeys($userInfo['email']);

$user = $_SESSION['user'];












include 'views/dashboard.php';

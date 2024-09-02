<?php

declare(strict_types=1);

session_start();

// Autoload classes
spl_autoload_register(function ($class) {
  require __DIR__ . "/src/$class.php";
});
require_once 'src/UserGateway.php';

// Set error and exception handlers
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

// Constants for Google OAuth2
const CLIENT_ID = '';
const CLIENT_SECRET = '';
const REDIRECT_URI = 'http://localhost/restfullAPI/dashboard.php';
const AUTHORIZATION_URL = "https://accounts.google.com/o/oauth2/v2/auth";
const TOKEN_URL = "https://oauth2.googleapis.com/token";
const USER_INFO_URL = "https://www.googleapis.com/oauth2/v1/userinfo";

// Check if the request is for the products API
$parts = explode("/", $_SERVER['REQUEST_URI']);
$newPart = explode("=", $parts[3] ?? '');


// Handle OAuth2 Authorization
if (!isset($_GET['code'])) {

  redirectToGoogleAuthorization();
} else {

  handleGoogleAuthorizationCode();
}




function redirectToGoogleAuthorization(): void
{
  $params = [
    'response_type' => 'code',
    'client_id' => CLIENT_ID,
    'redirect_uri' => REDIRECT_URI,
    'scope' => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email'
  ];
  header("Location: " . AUTHORIZATION_URL . "?" . http_build_query($params));
  exit;
}


function handleGoogleAuthorizationCode(): void
{
  $params = [
    'code' => $_GET['code'],
    'client_id' => CLIENT_ID,
    'client_secret' => CLIENT_SECRET,
    'redirect_uri' => REDIRECT_URI,
    'grant_type' => 'authorization_code',
  ];

  $response = makeCurlRequest(TOKEN_URL, $params);

  $accessTokenData = json_decode($response, true);

  if (isset($accessTokenData['access_token'])) {
    $_SESSION['access_token'] = $accessTokenData['access_token'];
  }
}

function saveUser($accessToken)
{
  $userInfo = fetchUserInfo($_SESSION['access_token']);
  $_SESSION['userInfo'] = $userInfo;
  $database = new Database("localhost", "api_db", "root", "");
  $user = new UserGateway($userInfo->id, $userInfo->email, $userInfo->name, $userInfo->verified_email, $database);
  $user->saveUser();
  $user->getKeys($userInfo->email);
}


function fetchUserInfo(string $accessToken)
{
  $url = USER_INFO_URL . "?access_token=" . $accessToken;
  return json_decode(makeCurlRequest($url));
}





function makeCurlRequest(string $url, ?array $params = [])
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);

  if (!empty($params)) {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
  }

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $response = curl_exec($ch);
  curl_close($ch);

  return $response;
}

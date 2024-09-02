<?php

declare(strict_types=1);

session_start();

// Autoload classes
spl_autoload_register(function ($class) {
  require __DIR__ . "/src/$class.php";
});
require 'src/UserGateway.php';

// Set error and exception handlers
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

// Constants for Google OAuth2
const CLIENT_ID = '';
const CLIENT_SECRET = '';
const REDIRECT_URI = 'http://localhost/api/v1/products';
const AUTHORIZATION_URL = "https://accounts.google.com/o/oauth2/v2/auth";
const TOKEN_URL = "https://oauth2.googleapis.com/token";
const USER_INFO_URL = "https://www.googleapis.com/oauth2/v1/userinfo";

// Check if the request is for the products API
$parts = explode("/", $_SERVER['REQUEST_URI']);
$newPart = explode("=", $parts[3] ?? '');

if (isset($parts[1], $parts[2]) && $parts[1] === 'api' && ($parts[3] === 'products' || $newPart[0] === 'products?api_key')) {

  if (isset($_GET['api_key'])) {
    $api_key = $_GET['api_key'];
    handleApiRequest($parts, $api_key);
  } else {
    http_response_code(404);
    exit;
  }
} else {
  http_response_code(404);
  exit;
}




function handleApiRequest(array $parts, string $api_key): void
{
  header("Content-type: application/json; charset=UTF-8");

  if (isset($parts[2]) && $parts[2] === 'v1') {
    $id = $parts[4] ?? null;
    if ($id) {
      $id = explode("?", $id);
      $id = $id[0];
    }
    $database = new Database("localhost", "api_db", "root", "");
    $database = new Database("localhost", "api_db", "root", "");
    $iskeyAuthenticated = UserGateway::iskeyAuthenticated($api_key, $database);
    if ($iskeyAuthenticated) {
      $gateway = new ProductGateway($database);
      $controller = new ProductController($gateway);
      $controller->proccessRequest($_SERVER['REQUEST_METHOD'], $id);
    } else {
      $errors[] = "Invalid Api Key";
      http_response_code(404);
      echo json_encode(['errors' => $errors]);
    }
  }
}

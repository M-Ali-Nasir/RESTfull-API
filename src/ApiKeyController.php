<?php
require 'Database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  if (isset($_POST['key_name']) && !empty('key_name')) {
    $specialChars = '!#$%^&*()-=+[{]};:\"<>/?\\|';
    if (strpbrk($_POST['key_name'], $specialChars) === false) {
      $key_name = $_POST['key_name'];
      $user = $_SESSION['user'];
      $userId = $user['id'];

      $apiKey = bin2hex(random_bytes(32));
      $database = new Database('localhost', 'api_db', 'root', '');
      $pdo = $database->getConnection();
      $stmt = $pdo->prepare("INSERT INTO api_keys (key_name, api_key, user_id) VALUES (:key_name, :api_key, :user_id)");
      $stmt->execute([':key_name' => $key_name, ':api_key' => $apiKey, ':user_id' => $userId]);
      if (isset($_SERVER['HTTP_REFERER'])) {
        $previousUrl = $_SERVER['HTTP_REFERER'];
        header("Location: $previousUrl");
        exit;
      } else {

        header("Location: ../dashboard.php");
        exit;
      }
    } else {

      if (isset($_SERVER['HTTP_REFERER'])) {
        $previousUrl = $_SERVER['HTTP_REFERER'];
        header("Location: $previousUrl");
        exit;
      } else {

        header("Location: ../dashboard.php");
        exit;
      }
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['key_id'])) {
  $id = $_GET['key_id'];
  $database = new Database('localhost', 'api_db', 'root', '');
  $pdo = $database->getConnection();
  $stmt = $pdo->prepare("DELETE FROM api_keys WHERE id = :id");
  $stmt->execute([':id' => $id]);
  if (isset($_SERVER['HTTP_REFERER'])) {
    $previousUrl = $_SERVER['HTTP_REFERER'];
    header("Location: $previousUrl");
    exit;
  } else {

    header("Location: ../dashboard.php");
    exit;
  }
}

<?php

class UserGateway
{
  private string $google_id;
  private string $email;
  private string $name;
  private int $isEmailVerified;
  private PDO $conn;

  public function __construct(string $google_id, string $email, string $name, bool $isEmailVerified, $database)
  {
    $this->google_id = $google_id;
    $this->email = $email;
    $this->name = $name;
    $this->isEmailVerified = $isEmailVerified;
    $this->conn = $database->getConnection();
  }

  private function isUserExists(string $email): bool
  {
    $stmt = $this->conn->prepare("SELECT * FROM users WHERE email=:email");
    $stmt->bindValue(":email", $email);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) == 0) {
      return false;
    } else {
      return true;
    };
  }

  public function saveUser()
  {

    $isUserExists = $this->isUserExists($this->email);
    if (!$isUserExists) {
      $sql = "INSERT INTO users (google_id, email, verified_email, name) 
      VALUES (:google_id, :email, :is_email_verified, :name)";

      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(":google_id", $this->google_id);
      $stmt->bindValue(":email", $this->email);
      $stmt->bindValue(":is_email_verified", $this->isEmailVerified);
      $stmt->bindValue(":name", $this->name);

      $stmt->execute();
    }
  }

  public function getKeys(string $email): array
  {
    $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindValue(":email", $email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['user'] = $result;
    $id = $result['id'];

    $sql = $this->conn->prepare("SELECT * FROM api_keys WHERE user_id = :user_id");
    $sql->bindValue(":user_id", $id);
    $sql->execute();

    $keys = $sql->fetchAll(PDO::FETCH_ASSOC);
    return $keys;
  }

  public static function iskeyAuthenticated(string $Get_key, $database): bool
  {
    $conn = $database->getConnection();
    $stmt = $conn->prepare("SELECT api_key FROM api_keys");
    $stmt->execute();

    $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);


    foreach ($keys as $key) {
      $api_key = $key['api_key'];
      if ($Get_key == $api_key) {
        return true;
      };
    }
    return false;
  }
}

<?php

class ProductController
{

  public function __construct(private ProductGateway $gateway) {}

  public function proccessRequest(string $method, ?string $id): void
  {
    if ($id) {
      $this->proccessResourceRequest($method, $id);
    } else {
      $this->proccessCollectionRequest($method);
    }
  }



  private function proccessResourceRequest(string $method, string $id): void
  {


    $id = intval($id);




    $product = $this->gateway->get($id);

    if (! $product) {
      http_response_code(404);
      echo json_encode(["message" => "Product not found"]);
      return;
    }

    switch ($method) {
      case "GET":
        echo json_encode($product);
        break;
      case "PATCH":
        $data = (array) json_decode(file_get_contents("php://input"), true);

        $errors = $this->getValidationErrors($data, false);

        if (count($errors) !== 0) {
          http_response_code(422);
          echo json_encode(['errors' => $errors]);
          break;
        }

        $rows = $this->gateway->update($product, $data);

        echo json_encode([
          "message" => "Product $id Updated",
          "rows" => $rows,
        ]);
        break;
      case "DELETE":
        $rows = $this->gateway->delete($id);

        echo json_encode([
          "message" => "Product $id Deleted",
          "rows" => $rows,
        ]);
        break;

      default:
        http_response_code(405);
        header("Allow: GET, PATCH, DELETE");
    }
  }

  private function proccessCollectionRequest(string $method): void
  {
    switch ($method) {
      case "GET":
        echo json_encode($this->gateway->getAll());
        break;

      case "POST";
        $data = (array) json_decode(file_get_contents("php://input"), true);

        $errors = $this->getValidationErrors($data);

        if (count($errors) !== 0) {
          http_response_code(422);
          echo json_encode(['errors' => $errors]);
          break;
        }

        $id = $this->gateway->create($data);

        http_response_code(201);
        echo json_encode([
          "message" => "Product Created",
          "id" => $id,
        ]);
        break;


      default:
        http_response_code(405);
        header("Allow: GET, POST");
    }
  }

  private function getValidationErrors(array $data, bool $is_new = true): array
  {
    $errors = [];

    if ($is_new && (!isset($data['name']) || empty($data['name']))) {
      $errors[] = "Name is required";
    }

    $specialChars = '!#$%^&*()-=+[{]};:\"<>/?\\|';
    if (strpbrk($data['name'], $specialChars) !== false) {
      $errors[] = "Name is must be alphanumeric";
    }



    if (array_key_exists("size", $data)) {

      if (filter_var($data['size'], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Size must be integer";
      }
    }

    return $errors;
  }
}

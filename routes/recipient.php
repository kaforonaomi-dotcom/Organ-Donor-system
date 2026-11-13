<?php

require_once __DIR__ . "/../controllers/RecipientController.php";

header("Content-Type: application/json");

$controller = new RecipientController();
$method = $_SERVER["REQUEST_METHOD"];
$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

$body = file_get_contents("php://input");
$data = $body !== "" ? json_decode($body, true) : [];

if ($body !== "" && !is_array($data)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON request body."
    ]);
    exit;
}

switch ($method) {
    case "POST":
        $result = $controller->register($data);
        break;

    case "GET":
        $result = $id > 0
            ? $controller->getById($id)
            : $controller->getAll();
        break;

    case "PUT":
    case "PATCH":
        $result = $id > 0
            ? $controller->update($id, $data)
            : [
                "success" => false,
                "message" => "Recipient ID is required.",
                "status_code" => 422
            ];
        break;

    case "DELETE":
        $result = $id > 0
            ? $controller->delete($id)
            : [
                "success" => false,
                "message" => "Recipient ID is required.",
                "status_code" => 422
            ];
        break;

    default:
        $result = [
            "success" => false,
            "message" => "Method not allowed.",
            "status_code" => 405
        ];
}

http_response_code($result["status_code"]);
unset($result["status_code"]);

echo json_encode($result);

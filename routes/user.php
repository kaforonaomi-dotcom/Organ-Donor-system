<?php

require_once __DIR__ . "/../controllers/UserController.php";

header("Content-Type: application/json");

$controller = new UserController();
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Allow: POST");
    echo json_encode(["success" => false, "message" => "Method not allowed."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid JSON request body."]);
    exit;
}

$result = isset($_GET["action"]) && strtolower($_GET["action"]) === "login"
    ? $controller->login($data)
    : $controller->register($data);
http_response_code($result["status_code"]);
unset($result["status_code"]);
echo json_encode($result);
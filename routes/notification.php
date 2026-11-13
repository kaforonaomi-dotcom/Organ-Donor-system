<?php
// filepath: c:\xampp\htdocs\Organ-Donor-system\routes\notification.php

require_once __DIR__ . "/../controllers/NotificationController.php";

header("Content-Type: application/json");

$controller = new NotificationController();
$method = $_SERVER["REQUEST_METHOD"];
$action = strtolower($_GET["action"] ?? "");
$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$userId = isset($_GET["user_id"]) ? (int) $_GET["user_id"] : 0;

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

if ($method === "POST") {
    $result = $controller->create($data);
} elseif ($method === "GET" && $userId > 0) {
    $result = $controller->getForUser($userId);
} elseif (
    ($method === "PATCH" || $method === "PUT") &&
    $action === "read"
) {
    $result = $controller->markAsRead($id);
} elseif ($method === "DELETE") {
    $result = $controller->delete($id);
} else {
    $result = [
        "success" => false,
        "message" => "Invalid method or missing parameter.",
        "status_code" => 405
    ];
}

http_response_code($result["status_code"]);
unset($result["status_code"]);

echo json_encode($result);
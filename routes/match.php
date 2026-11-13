<?php
// filepath: c:\xampp\htdocs\Organ-Donor-system\routes\match.php

require_once __DIR__ . "/../controllers/MatchController.php";

header("Content-Type: application/json");

$controller = new MatchController();
$method = $_SERVER["REQUEST_METHOD"];
$action = strtolower($_GET["action"] ?? "");
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

if ($method === "GET" && $action === "possible") {
    $result = $controller->findPossible();
} elseif ($method === "GET") {
    $result = $controller->getAll();
} elseif ($method === "POST") {
    $result = $controller->create($data);
} elseif (
    ($method === "PATCH" || $method === "PUT") &&
    in_array($action, ["approve", "reject"], true)
) {
    $result = $controller->updateStatus(
        $id,
        $action === "approve" ? "approved" : "rejected"
    );
} else {
    $result = [
        "success" => false,
        "message" => "Invalid method or action.",
        "status_code" => 405
    ];
}

http_response_code($result["status_code"]);
unset($result["status_code"]);

echo json_encode($result);
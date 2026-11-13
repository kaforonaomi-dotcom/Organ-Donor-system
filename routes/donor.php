<?php

require_once __DIR__ . "/../controllers/DonorController.php";
header("Content-Type: application/json");
$controller = new DonorController();
$method = $_SERVER["REQUEST_METHOD"];
$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$body = file_get_contents("php://input");
$data = $body !== "" ? json_decode($body, true) : [];
if ($body !== "" && !is_array($data)) { http_response_code(400); echo json_encode(["success" => false, "message" => "Invalid JSON request body."]); exit; }
if ($method === "POST") $result = $controller->register($data);
elseif ($method === "GET") $result = $id > 0 ? $controller->getById($id) : $controller->getAll();
elseif ($method === "PUT" || $method === "PATCH") $result = $id > 0 ? $controller->update($id, $data) : ["success" => false, "message" => "Donor ID is required.", "status_code" => 422];
elseif ($method === "DELETE") $result = $id > 0 ? $controller->delete($id) : ["success" => false, "message" => "Donor ID is required.", "status_code" => 422];
else $result = ["success" => false, "message" => "Method not allowed.", "status_code" => 405];
http_response_code($result["status_code"]);
unset($result["status_code"]);
echo json_encode($result);
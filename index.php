<?php
// filepath: c:\xampp\htdocs\Organ-Donor-system\index.php

set_exception_handler(function (Throwable $exception): void {
    error_log($exception->getMessage());
    http_response_code(500);
    header("Content-Type: application/json");
    echo json_encode(["success" => false, "message" => "Internal server error."]);
    exit;
});

$resource = strtolower(trim($_GET["resource"] ?? "", "/"));

$routes = [
    "users" => __DIR__ . "/routes/user.php",
    "donors" => __DIR__ . "/routes/donor.php",
    "recipients" => __DIR__ . "/routes/recipient.php",
    "matches" => __DIR__ . "/routes/match.php",
    "notifications" => __DIR__ . "/routes/notification.php"
];

$accept = $_SERVER["HTTP_ACCEPT"] ?? "";
$isBrowserHtml = (stripos($accept, "text/html") !== false && stripos($accept, "application/json") === false) || isset($_GET["ui"]);

if ($resource === "") {
    if ($isBrowserHtml && !isset($_GET["api"])) {
        require __DIR__ . "/views/index.php";
        exit;
    }

    header("Content-Type: application/json");
    echo json_encode([
        "success" => true,
        "message" => "Organ Donor System API is running.",
        "endpoints" => array_keys($routes)
    ]);
    exit;
}

header("Content-Type: application/json");

if (!isset($routes[$resource])) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "API endpoint not found."
    ]);
    exit;
}

require $routes[$resource];
  
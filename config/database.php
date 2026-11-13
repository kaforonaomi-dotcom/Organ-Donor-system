<?php
// filepath: c:\xampp\htdocs\Organ-Donor-system\config\database.php

$envFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . ".env";

if (!is_readable($envFile)) {
    http_response_code(500);
    die("Server configuration error.");
}

$env = parse_ini_file($envFile);

$host = $env["DB_HOST"] ?? "localhost";
$port = $env["DB_PORT"] ?? "3306";
$dbname = $env["DB_NAME"] ?? "";
$username = $env["DB_USER"] ?? "";
$password = $env["DB_PASSWORD"] ?? "";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    die("Database connection failed.");
}
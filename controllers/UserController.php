<?php

require_once __DIR__ . "/../config/database.php";

class UserController
{
    private function response(bool $success, string $message, int $statusCode, array $data = []): array
    {
        return array_merge([
            "success" => $success,
            "message" => $message,
            "status_code" => $statusCode
        ], $data);
    }

    public function register(array $data): array
    {
        global $pdo;

        if (
            empty($data['full_name']) ||
            empty($data['email']) ||
            empty($data['password']) ||
            empty($data['role'])
        ) {
            return $this->response(false, "All fields are required.", 422);
        }

        $fullName = trim($data['full_name']);
        $email = trim($data['email']);
        $password = $data['password'];
        $role = strtolower(trim($data['role']));

        $allowedRoles = ['admin', 'donor', 'recipient'];

        if (strlen($fullName) > 150 || strlen($password) < 8) {
            return $this->response(false, "Name must be at most 150 characters and password at least 8 characters.", 422);
        }

        if (!in_array($role, $allowedRoles, true)) {
            return $this->response(false, "Invalid role.", 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response(false, "Invalid email address.", 422);
        }

        try {
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $check->execute([$email]);
            if ($check->fetch()) return $this->response(false, "Email already exists.", 409);

            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$fullName, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
            return $this->response(true, "User registered successfully.", 201, ["user_id" => (int) $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            return $this->response(false, "Database operation failed.", 500);
        }
    }

    public function login(array $data): array
    {
        global $pdo;
        if (empty($data["email"]) || empty($data["password"])) {
            return $this->response(false, "Email and password are required.", 422);
        }
        try {
            $stmt = $pdo->prepare("SELECT id, full_name, email, password_hash, role FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([trim($data["email"])]);
            $user = $stmt->fetch();
            if (!$user || !password_verify($data["password"], $user["password_hash"])) {
                return $this->response(false, "Invalid email or password.", 401);
            }
            unset($user["password_hash"]);
            return $this->response(true, "Login successful.", 200, ["user" => $user]);
        } catch (PDOException $e) {
            return $this->response(false, "Database operation failed.", 500);
        }
    }
}
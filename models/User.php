<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/database.php";

class User
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        global $pdo;
        $this->db = $db ?? $pdo;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT id, full_name, email, password_hash, role, created_at FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([trim($email)]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, full_name, email, role, created_at FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(string $fullName, string $email, string $password, string $role): int
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([trim($fullName), trim($email), $hash, strtolower(trim($role))]);
        return (int) $this->db->lastInsertId();
    }

    public function getAll(?string $role = null): array
    {
        if ($role !== null) {
            $stmt = $this->db->prepare("SELECT id, full_name, email, role, created_at FROM users WHERE role = ? ORDER BY id DESC");
            $stmt->execute([$role]);
            return $stmt->fetchAll();
        }
        $stmt = $this->db->query("SELECT id, full_name, email, role, created_at FROM users ORDER BY id DESC");
        return $stmt->fetchAll();
    }
}

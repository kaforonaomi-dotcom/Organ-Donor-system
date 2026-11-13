<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/database.php";

class Notification
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        global $pdo;
        $this->db = $db ?? $pdo;
    }

    public function getForUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, user_id, title, message, is_read, created_at
             FROM notifications
             WHERE user_id = ?
             ORDER BY id DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function create(int $userId, string $title, string $message): int
    {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
        $stmt->execute([$userId, trim($title), trim($message)]);
        return (int) $this->db->lastInsertId();
    }

    public function markAsRead(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}

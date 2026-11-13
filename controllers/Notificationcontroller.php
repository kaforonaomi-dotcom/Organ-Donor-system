<?php
// filepath: c:\xampp\htdocs\Organ-Donor-system\controllers\NotificationController.php

require_once __DIR__ . "/../config/database.php";

class NotificationController
{
    private function response(
        bool $success,
        string $message,
        int $statusCode,
        array $data = []
    ): array {
        return array_merge([
            "success" => $success,
            "message" => $message,
            "status_code" => $statusCode
        ], $data);
    }

    public function create(array $data): array
    {
        global $pdo;

        foreach (["user_id", "title", "message"] as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === "") {
                return $this->response(false, "$field is required.", 422);
            }
        }

        $userId = filter_var($data["user_id"], FILTER_VALIDATE_INT);
        $title = trim((string) $data["title"]);
        $message = trim((string) $data["message"]);

        if ($userId === false || $userId <= 0) {
            return $this->response(false, "Invalid user_id.", 422);
        }

        if (strlen($title) > 150) {
            return $this->response(false, "Title is too long.", 422);
        }

        try {
            $userStmt = $pdo->prepare(
                "SELECT id FROM users WHERE id = ? LIMIT 1"
            );
            $userStmt->execute([$userId]);

            if (!$userStmt->fetch()) {
                return $this->response(false, "User not found.", 404);
            }

            $stmt = $pdo->prepare(
                "INSERT INTO notifications (user_id, title, message)
                 VALUES (?, ?, ?)"
            );
            $stmt->execute([$userId, $title, $message]);

            return $this->response(
                true,
                "Notification created successfully.",
                201,
                ["notification_id" => (int) $pdo->lastInsertId()]
            );
        } catch (PDOException $e) {
            return $this->response(false, "Database operation failed.", 500);
        }
    }

    public function getForUser(int $userId): array
    {
        global $pdo;

        if ($userId <= 0) {
            return $this->response(false, "Invalid user_id.", 422);
        }

        try {
            $userStmt = $pdo->prepare(
                "SELECT id FROM users WHERE id = ? LIMIT 1"
            );
            $userStmt->execute([$userId]);

            if (!$userStmt->fetch()) {
                return $this->response(false, "User not found.", 404);
            }

            $stmt = $pdo->prepare(
                "SELECT id, user_id, title, message, is_read, created_at
                 FROM notifications
                 WHERE user_id = ?
                 ORDER BY id DESC"
            );
            $stmt->execute([$userId]);

            return $this->response(
                true,
                "Notifications retrieved successfully.",
                200,
                ["notifications" => $stmt->fetchAll()]
            );
        } catch (PDOException $e) {
            return $this->response(false, "Database operation failed.", 500);
        }
    }

    public function markAsRead(int $id): array
    {
        global $pdo;

        if ($id <= 0) {
            return $this->response(false, "Invalid notification ID.", 422);
        }

        try {
            $stmt = $pdo->prepare(
                "UPDATE notifications SET is_read = 1 WHERE id = ?"
            );
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                $check = $pdo->prepare(
                    "SELECT id FROM notifications WHERE id = ? LIMIT 1"
                );
                $check->execute([$id]);

                if (!$check->fetch()) {
                    return $this->response(
                        false,
                        "Notification not found.",
                        404
                    );
                }
            }

            return $this->response(
                true,
                "Notification marked as read.",
                200
            );
        } catch (PDOException $e) {
            return $this->response(false, "Database operation failed.", 500);
        }
    }

    public function delete(int $id): array
    {
        global $pdo;

        if ($id <= 0) {
            return $this->response(false, "Invalid notification ID.", 422);
        }

        try {
            $stmt = $pdo->prepare(
                "DELETE FROM notifications WHERE id = ?"
            );
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                return $this->response(
                    false,
                    "Notification not found.",
                    404
                );
            }

            return $this->response(
                true,
                "Notification deleted successfully.",
                200
            );
        } catch (PDOException $e) {
            return $this->response(false, "Database operation failed.", 500);
        }
    }
}
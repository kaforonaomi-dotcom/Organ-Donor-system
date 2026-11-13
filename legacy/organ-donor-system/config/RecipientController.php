<?php
// filepath: c:\xampp\htdocs\Organ-Donor-system\controllers\RecipientController.php

require_once __DIR__ . "/../config/database.php";

class RecipientController
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

    public function register(array $data): array
    {
        global $pdo;

        $fields = [
            "user_id",
            "age",
            "blood_type",
            "organ_needed",
            "urgency",
            "hospital"
        ];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === "") {
                return $this->response(false, "$field is required.", 422);
            }
        }

        $userId = filter_var($data["user_id"], FILTER_VALIDATE_INT);
        $age = filter_var($data["age"], FILTER_VALIDATE_INT);
        $bloodType = strtoupper(trim((string) $data["blood_type"]));
        $organNeeded = trim((string) $data["organ_needed"]);
        $urgency = strtolower(trim((string) $data["urgency"]));
        $hospital = trim((string) $data["hospital"]);

        if ($userId === false || $userId <= 0) {
            return $this->response(false, "Invalid user_id.", 422);
        }

        if ($age === false || $age < 1 || $age > 120) {
            return $this->response(false, "Age must be between 1 and 120.", 422);
        }

        if (strlen($bloodType) > 5) {
            return $this->response(false, "Blood type is too long.", 422);
        }

        if (strlen($organNeeded) > 50) {
            return $this->response(false, "Organ name is too long.", 422);
        }

        if (strlen($hospital) > 150) {
            return $this->response(false, "Hospital name is too long.", 422);
        }

        if (!in_array($urgency, ["low", "medium", "high", "critical"], true)) {
            return $this->response(false, "Invalid urgency.", 422);
        }

        try {
            $stmt = $pdo->prepare(
                "SELECT id, role FROM users WHERE id = ? LIMIT 1"
            );
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user) {
                return $this->response(false, "User not found.", 404);
            }

            if ($user["role"] !== "recipient") {
                return $this->response(
                    false,
                    "User does not have the recipient role.",
                    403
                );
            }

            $stmt = $pdo->prepare(
                "SELECT id FROM recipients WHERE user_id = ? LIMIT 1"
            );
            $stmt->execute([$userId]);

            if ($stmt->fetch()) {
                return $this->response(
                    false,
                    "A recipient profile already exists.",
                    409
                );
            }

            $stmt = $pdo->prepare(
                "INSERT INTO recipients
                (user_id, age, blood_type, organ_needed, urgency, hospital)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $userId,
                $age,
                $bloodType,
                $organNeeded,
                $urgency,
                $hospital
            ]);

            return $this->response(
                true,
                "Recipient registered successfully.",
                201,
                ["recipient_id" => (int) $pdo->lastInsertId()]
            );
        } catch (PDOException $e) {
            return $this->response(false, "Database operation failed.", 500);
        }
    }

    public function getAll(): array
    {
        global $pdo;

        try {
            $stmt = $pdo->query(
                "SELECT id, user_id, age, blood_type, organ_needed,
                        urgency, hospital, created_at
                 FROM recipients ORDER BY id DESC"
            );

            return $this->response(
                true,
                "Recipients retrieved successfully.",
                200,
                ["recipients" => $stmt->fetchAll()]
            );
        } catch (PDOException $e) {
            return $this->response(false, "Database operation failed.", 500);
        }
    }

    public function getById(int $id): array
    {
        global $pdo;

        try {
            $stmt = $pdo->prepare(
                "SELECT id, user_id, age, blood_type, organ_needed,
                        urgency, hospital, created_at
                 FROM recipients WHERE id = ? LIMIT 1"
            );
            $stmt->execute([$id]);
            $recipient = $stmt->fetch();

            if (!$recipient) {
                return $this->response(false, "Recipient not found.", 404);
            }

            return $this->response(
                true,
                "Recipient retrieved successfully.",
                200,
                ["recipient" => $recipient]
            );
        } catch (PDOException $e) {
            return $this->response(false, "Database operation failed.", 500);
        }
    }

    public function update(int $id, array $data): array
    {
        global $pdo;

        $allowed = [
            "age",
            "blood_type",
            "organ_needed",
            "urgency",
            "hospital"
        ];

        $updates = [];
        $values = [];

        foreach ($allowed as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            $value = trim((string) $data[$field]);

            if ($value === "") {
                return $this->response(false, "$field cannot be empty.", 422);
            }

            if ($field === "age") {
                $value = filter_var($value, FILTER_VALIDATE_INT);

                if ($value === false || $value < 1 || $value > 120) {
                    return $this->response(false, "Invalid age.", 422);
                }
            }

            if ($field === "blood_type") {
                $value = strtoupper($value);
            }

            if ($field === "urgency") {
                $value = strtolower($value);

                if (!in_array($value, ["low", "medium", "high", "critical"], true)) {
                    return $this->response(false, "Invalid urgency.", 422);
                }
            }

            $updates[] = "$field = ?";
            $values[] = $value;
        }

        if (!$updates) {
            return $this->response(false, "No valid fields provided.", 422);
        }

        try {
            $values[] = $id;

            $stmt = $pdo->prepare(
                "UPDATE recipients SET " .
                implode(", ", $updates) .
                " WHERE id = ?"
            );
            $stmt->execute($values);

            return $this->response(true, "Recipient updated successfully.", 200);
        } catch (PDOException $e) {
            return $this->response(false, "Database operation failed.", 500);
        }
    }

    public function delete(int $id): array
    {
        global $pdo;

        try {
            $stmt = $pdo->prepare("DELETE FROM recipients WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                return $this->response(false, "Recipient not found.", 404);
            }

            return $this->response(true, "Recipient deleted successfully.", 200);
        } catch (PDOException $e) {
            return $this->response(false, "Database operation failed.", 500);
        }
    }
}
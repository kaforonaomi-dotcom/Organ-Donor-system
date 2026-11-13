<?php

require_once __DIR__ . "/../config/database.php";

class DonorController
{
    private function response(bool $success, string $message, int $statusCode, array $data = []): array
    {
        return array_merge(["success" => $success, "message" => $message, "status_code" => $statusCode], $data);
    }

    public function register(array $data): array
    {
        global $pdo;
        foreach (["user_id", "age", "blood_type", "organ", "availability", "phone"] as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === "") return $this->response(false, "$field is required.", 422);
        }
        $userId = filter_var($data["user_id"], FILTER_VALIDATE_INT);
        $age = filter_var($data["age"], FILTER_VALIDATE_INT);
        $bloodType = strtoupper(trim($data["blood_type"]));
        $organ = trim($data["organ"]);
        $availability = strtolower(trim($data["availability"]));
        $phone = trim($data["phone"]);
        if ($userId === false || $userId <= 0 || $age === false || $age < 1 || $age > 120) return $this->response(false, "Invalid user_id or age.", 422);
        if ($availability === "not available") $availability = "unavailable";
        if (!in_array($availability, ["available", "unavailable"], true)) return $this->response(false, "Invalid availability.", 422);
        if (strlen($bloodType) > 5 || strlen($organ) > 50 || strlen($phone) > 15) return $this->response(false, "Donor field length is invalid.", 422);
        try {
            $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            if (!$user) return $this->response(false, "User not found.", 404);
            if ($user["role"] !== "donor") return $this->response(false, "User does not have the donor role.", 403);
            $stmt = $pdo->prepare("SELECT id FROM donors WHERE user_id = ? LIMIT 1");
            $stmt->execute([$userId]);
            if ($stmt->fetch()) return $this->response(false, "A donor profile already exists.", 409);
            $stmt = $pdo->prepare("INSERT INTO donors (user_id, age, blood_type, organ, availability, phone) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $age, $bloodType, $organ, $availability, $phone]);
            return $this->response(true, "Donor registered successfully.", 201, ["donor_id" => (int) $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            return $this->response(false, "Database operation failed.", 500);
        }
    }

    public function getAll(): array
    {
        global $pdo;
        try {
            $stmt = $pdo->query("SELECT id, user_id, age, blood_type, organ, availability, phone, created_at FROM donors ORDER BY id DESC");
            return $this->response(true, "Donors retrieved successfully.", 200, ["donors" => $stmt->fetchAll()]);
        } catch (PDOException $e) { return $this->response(false, "Database operation failed.", 500); }
    }

    public function getById(int $id): array
    {
        global $pdo;
        if ($id <= 0) return $this->response(false, "Invalid donor ID.", 422);
        try {
            $stmt = $pdo->prepare("SELECT id, user_id, age, blood_type, organ, availability, phone, created_at FROM donors WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $donor = $stmt->fetch();
            return $donor ? $this->response(true, "Donor retrieved successfully.", 200, ["donor" => $donor]) : $this->response(false, "Donor not found.", 404);
        } catch (PDOException $e) { return $this->response(false, "Database operation failed.", 500); }
    }

    public function update(int $id, array $data): array
    {
        global $pdo;
        $allowed = ["age", "blood_type", "organ", "availability", "phone"];
        $updates = [];
        $values = [];
        foreach ($allowed as $field) {
            if (!array_key_exists($field, $data)) continue;
            $value = trim((string) $data[$field]);
            if ($value === "") return $this->response(false, "$field cannot be empty.", 422);
            if ($field === "age") { $value = filter_var($value, FILTER_VALIDATE_INT); if ($value === false || $value < 1 || $value > 120) return $this->response(false, "Invalid age.", 422); }
            if ($field === "blood_type") $value = strtoupper($value);
            if ($field === "availability") { $value = strtolower($value); if ($value === "not available") $value = "unavailable"; if (!in_array($value, ["available", "unavailable"], true)) return $this->response(false, "Invalid availability.", 422); }
            $updates[] = "$field = ?";
            $values[] = $value;
        }
        if (!$updates) return $this->response(false, "No valid fields provided.", 422);
        try {
            $values[] = $id;
            $stmt = $pdo->prepare("UPDATE donors SET " . implode(", ", $updates) . " WHERE id = ?");
            $stmt->execute($values);
            if ($stmt->rowCount() === 0) { $check = $pdo->prepare("SELECT id FROM donors WHERE id = ? LIMIT 1"); $check->execute([$id]); if (!$check->fetch()) return $this->response(false, "Donor not found.", 404); }
            return $this->response(true, "Donor updated successfully.", 200);
        } catch (PDOException $e) { return $this->response(false, "Database operation failed.", 500); }
    }

    public function delete(int $id): array
    {
        global $pdo;
        if ($id <= 0) return $this->response(false, "Invalid donor ID.", 422);
        try {
            $stmt = $pdo->prepare("DELETE FROM donors WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0 ? $this->response(true, "Donor deleted successfully.", 200) : $this->response(false, "Donor not found.", 404);
        } catch (PDOException $e) { return $this->response(false, "Database operation failed.", 500); }
    }
}
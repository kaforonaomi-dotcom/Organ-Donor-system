<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/database.php";

class Recipient
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        global $pdo;
        $this->db = $db ?? $pdo;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT r.id, r.user_id, u.full_name, u.email, r.age, r.blood_type, r.organ_needed, r.urgency, r.hospital, r.created_at, r.updated_at FROM recipients r LEFT JOIN users u ON u.id = r.user_id ORDER BY FIELD(r.urgency, 'critical', 'high', 'medium', 'low'), r.id DESC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT r.id, r.user_id, u.full_name, u.email, r.age, r.blood_type, r.organ_needed, r.urgency, r.hospital, r.created_at, r.updated_at FROM recipients r LEFT JOIN users u ON u.id = r.user_id WHERE r.id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM recipients WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(int $userId, int $age, string $bloodType, string $organNeeded, string $urgency, string $hospital): int
    {
        $stmt = $this->db->prepare("INSERT INTO recipients (user_id, age, blood_type, organ_needed, urgency, hospital) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $age, strtoupper(trim($bloodType)), trim($organNeeded), strtolower(trim($urgency)), trim($hospital)]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $allowed = ["age", "blood_type", "organ_needed", "urgency", "hospital"];
        $updates = [];
        $values = [];
        foreach ($allowed as $f) {
            if (isset($data[$f])) {
                $updates[] = "$f = ?";
                $values[] = $f === 'blood_type' ? strtoupper(trim((string)$data[$f])) : trim((string)$data[$f]);
            }
        }
        if (empty($updates)) return false;
        $values[] = $id;
        $stmt = $this->db->prepare("UPDATE recipients SET " . implode(", ", $updates) . " WHERE id = ?");
        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM recipients WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}

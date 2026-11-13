<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/database.php";

class Donor
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        global $pdo;
        $this->db = $db ?? $pdo;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT d.id, d.user_id, u.full_name, u.email, d.age, d.blood_type, d.organ, d.availability, d.phone, d.created_at, d.updated_at FROM donors d LEFT JOIN users u ON u.id = d.user_id ORDER BY d.id DESC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT d.id, d.user_id, u.full_name, u.email, d.age, d.blood_type, d.organ, d.availability, d.phone, d.created_at, d.updated_at FROM donors d LEFT JOIN users u ON u.id = d.user_id WHERE d.id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM donors WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(int $userId, int $age, string $bloodType, string $organ, string $availability, string $phone): int
    {
        $stmt = $this->db->prepare("INSERT INTO donors (user_id, age, blood_type, organ, availability, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $age, strtoupper(trim($bloodType)), trim($organ), strtolower(trim($availability)), trim($phone)]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $allowed = ["age", "blood_type", "organ", "availability", "phone"];
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
        $stmt = $this->db->prepare("UPDATE donors SET " . implode(", ", $updates) . " WHERE id = ?");
        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM donors WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}

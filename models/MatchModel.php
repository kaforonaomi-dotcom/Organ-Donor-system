<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/database.php";

class MatchModel
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        global $pdo;
        $this->db = $db ?? $pdo;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT
                m.id,
                m.donor_id,
                m.recipient_id,
                m.match_status,
                m.created_at,
                d.blood_type AS donor_blood_type,
                d.organ AS donor_organ,
                du.full_name AS donor_name,
                r.blood_type AS recipient_blood_type,
                r.organ_needed,
                r.urgency AS recipient_urgency,
                ru.full_name AS recipient_name
             FROM matches m
             INNER JOIN donors d ON d.id = m.donor_id
             LEFT JOIN users du ON du.id = d.user_id
             INNER JOIN recipients r ON r.id = m.recipient_id
             LEFT JOIN users ru ON ru.id = r.user_id
             ORDER BY m.id DESC"
        );
        return $stmt->fetchAll();
    }

    public function findPossible(): array
    {
        $stmt = $this->db->query(
            "SELECT
                d.id AS donor_id,
                d.user_id AS donor_user_id,
                du.full_name AS donor_name,
                d.age AS donor_age,
                d.blood_type AS donor_blood_type,
                d.organ AS donor_organ,
                d.availability,
                d.phone AS donor_phone,
                r.id AS recipient_id,
                r.user_id AS recipient_user_id,
                ru.full_name AS recipient_name,
                r.age AS recipient_age,
                r.blood_type AS recipient_blood_type,
                r.organ_needed,
                r.urgency,
                r.hospital
             FROM donors d
             INNER JOIN recipients r
                ON d.blood_type = r.blood_type
                AND LOWER(d.organ) = LOWER(r.organ_needed)
             LEFT JOIN users du ON du.id = d.user_id
             LEFT JOIN users ru ON ru.id = r.user_id
             WHERE d.availability = 'available'
             ORDER BY
                FIELD(r.urgency, 'critical', 'high', 'medium', 'low'),
                d.id DESC"
        );
        return $stmt->fetchAll();
    }

    public function create(int $donorId, int $recipientId): int
    {
        $stmt = $this->db->prepare("INSERT INTO matches (donor_id, recipient_id, match_status) VALUES (?, ?, 'pending')");
        $stmt->execute([$donorId, $recipientId]);
        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE matches SET match_status = ? WHERE id = ?");
        return $stmt->execute([strtolower(trim($status)), $id]);
    }
}

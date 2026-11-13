<?php
// filepath: c:\xampp\htdocs\Organ-Donor-system\controllers\MatchController.php

require_once __DIR__ . "/../config/database.php";

class MatchController
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

    public function findPossible(): array
    {
        global $pdo;

        try {
            $stmt = $pdo->query(
                "SELECT
                    d.id AS donor_id,
                    d.user_id AS donor_user_id,
                    d.age AS donor_age,
                    d.blood_type AS donor_blood_type,
                    d.organ AS donor_organ,
                    d.availability,
                    r.id AS recipient_id,
                    r.user_id AS recipient_user_id,
                    r.age AS recipient_age,
                    r.blood_type AS recipient_blood_type,
                    r.organ_needed,
                    r.urgency,
                    r.hospital
                 FROM donors d
                 INNER JOIN recipients r
                    ON d.blood_type = r.blood_type
                    AND LOWER(d.organ) = LOWER(r.organ_needed)
                 WHERE d.availability = 'available'
                 ORDER BY
                    FIELD(r.urgency, 'critical', 'high', 'medium', 'low'),
                    d.id DESC"
            );

            return $this->response(
                true,
                "Possible matches retrieved successfully.",
                200,
                ["possible_matches" => $stmt->fetchAll()]
            );
        } catch (PDOException $e) {
            return $this->response(
                false,
                "Database operation failed.",
                500
            );
        }
    }

    public function create(array $data): array
    {
        global $pdo;

        if (
            !isset($data["donor_id"]) ||
            !isset($data["recipient_id"])
        ) {
            return $this->response(
                false,
                "donor_id and recipient_id are required.",
                422
            );
        }

        $donorId = filter_var($data["donor_id"], FILTER_VALIDATE_INT);
        $recipientId = filter_var(
            $data["recipient_id"],
            FILTER_VALIDATE_INT
        );

        if (
            $donorId === false ||
            $recipientId === false ||
            $donorId <= 0 ||
            $recipientId <= 0
        ) {
            return $this->response(
                false,
                "Invalid donor_id or recipient_id.",
                422
            );
        }

        try {
            $stmt = $pdo->prepare(
                "SELECT
                    d.id AS donor_id,
                    d.blood_type AS donor_blood_type,
                    d.organ AS donor_organ,
                    d.availability,
                    r.id AS recipient_id,
                    r.blood_type AS recipient_blood_type,
                    r.organ_needed
                 FROM donors d
                 INNER JOIN recipients r ON r.id = ?
                 WHERE d.id = ?
                 LIMIT 1"
            );
            $stmt->execute([$recipientId, $donorId]);
            $matchData = $stmt->fetch();

            if (!$matchData) {
                return $this->response(
                    false,
                    "Donor or recipient not found.",
                    404
                );
            }

            if ($matchData["availability"] !== "available") {
                return $this->response(
                    false,
                    "Donor is not available.",
                    409
                );
            }

            if (
                $matchData["donor_blood_type"] !==
                $matchData["recipient_blood_type"]
            ) {
                return $this->response(
                    false,
                    "Blood types do not match.",
                    422
                );
            }

            if (
                strtolower($matchData["donor_organ"]) !==
                strtolower($matchData["organ_needed"])
            ) {
                return $this->response(
                    false,
                    "Donor organ does not match the required organ.",
                    422
                );
            }

            $duplicateStmt = $pdo->prepare(
                "SELECT id, match_status
                 FROM matches
                 WHERE donor_id = ? AND recipient_id = ?
                 LIMIT 1"
            );
            $duplicateStmt->execute([$donorId, $recipientId]);
            $existing = $duplicateStmt->fetch();

            if ($existing) {
                return $this->response(
                    false,
                    "This donor-recipient match already exists.",
                    409,
                    ["match" => $existing]
                );
            }

            $insertStmt = $pdo->prepare(
                "INSERT INTO matches
                    (donor_id, recipient_id, match_status)
                 VALUES (?, ?, 'pending')"
            );
            $insertStmt->execute([$donorId, $recipientId]);

            return $this->response(
                true,
                "Match created successfully.",
                201,
                [
                    "match_id" => (int) $pdo->lastInsertId(),
                    "match_status" => "pending"
                ]
            );
        } catch (PDOException $e) {
            return $this->response(
                false,
                "Database operation failed.",
                500
            );
        }
    }

    public function getAll(): array
    {
        global $pdo;

        try {
            $stmt = $pdo->query(
                "SELECT
                    m.id,
                    m.donor_id,
                    m.recipient_id,
                    m.match_status,
                    m.created_at,
                    d.blood_type AS donor_blood_type,
                    d.organ AS donor_organ,
                    r.blood_type AS recipient_blood_type,
                    r.organ_needed
                 FROM matches m
                 INNER JOIN donors d ON d.id = m.donor_id
                 INNER JOIN recipients r ON r.id = m.recipient_id
                 ORDER BY m.id DESC"
            );

            return $this->response(
                true,
                "Matches retrieved successfully.",
                200,
                ["matches" => $stmt->fetchAll()]
            );
        } catch (PDOException $e) {
            return $this->response(
                false,
                "Database operation failed.",
                500
            );
        }
    }

    public function updateStatus(int $id, string $status): array
    {
        global $pdo;

        if ($id <= 0) {
            return $this->response(false, "Invalid match ID.", 422);
        }

        $status = strtolower(trim($status));

        if (!in_array($status, ["approved", "rejected"], true)) {
            return $this->response(
                false,
                "Status must be approved or rejected.",
                422
            );
        }

        try {
            $checkStmt = $pdo->prepare(
                "SELECT id FROM matches WHERE id = ? LIMIT 1"
            );
            $checkStmt->execute([$id]);

            if (!$checkStmt->fetch()) {
                return $this->response(false, "Match not found.", 404);
            }

            $stmt = $pdo->prepare(
                "UPDATE matches
                 SET match_status = ?
                 WHERE id = ?"
            );
            $stmt->execute([$status, $id]);

            return $this->response(
                true,
                "Match status updated successfully.",
                200,
                ["match_id" => $id, "match_status" => $status]
            );
        } catch (PDOException $e) {
            return $this->response(
                false,
                "Database operation failed.",
                500
            );
        }
    }
}
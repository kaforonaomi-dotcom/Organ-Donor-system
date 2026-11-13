<?php

class DonorController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function registerDonor($data) {
        // Validate required fields
        if (empty($data['user_id']) || empty($data['age']) || empty($data['blood_type']) || empty($data['organ']) || empty($data['availability']) || empty($data['phone'])) {
            return json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        }

        // Check if user exists and has donor role
        $user = $this->getUserById($data['user_id']);
        if (!$user || $user['role'] !== 'donor') {
            return json_encode(['status' => 'error', 'message' => 'User does not exist or is not a donor.']);
        }

        // Prevent duplicate donor profiles
        if ($this->donorExists($data['user_id'])) {
            return json_encode(['status' => 'error', 'message' => 'Donor profile already exists for this user.']);
        }

        // Insert donor profile into the database
        $stmt = $this->db->prepare("INSERT INTO donors (user_id, age, blood_type, organ, availability, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $data['user_id'], $data['age'], $data['blood_type'], $data['organ'], $data['availability'], $data['phone']);
        
        if ($stmt->execute()) {
            return json_encode(['status' => 'success', 'message' => 'Donor registered successfully.']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to register donor.']);
        }
    }

    public function getAllDonors() {
        $result = $this->db->query("SELECT * FROM donors");
        $donors = $result->fetch_all(MYSQLI_ASSOC);
        return json_encode($donors);
    }

    public function getDonorById($id) {
        $stmt = $this->db->prepare("SELECT * FROM donors WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $donor = $result->fetch_assoc();
        return json_encode($donor);
    }

    public function updateDonor($id, $data) {
        $stmt = $this->db->prepare("UPDATE donors SET age = ?, availability = ? WHERE id = ?");
        $stmt->bind_param("ssi", $data['age'], $data['availability'], $id);
        
        if ($stmt->execute()) {
            return json_encode(['status' => 'success', 'message' => 'Donor information updated successfully.']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to update donor information.']);
        }
    }

    public function deleteDonor($id) {
        $stmt = $this->db->prepare("DELETE FROM donors WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return json_encode(['status' => 'success', 'message' => 'Donor profile deleted successfully.']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to delete donor profile.']);
        }
    }

    private function getUserById($userId) {
        // This function should interact with the user database to retrieve user details
        // Placeholder for user retrieval logic
        return ['id' => $userId, 'role' => 'donor']; // Example response
    }

    private function donorExists($userId) {
        $stmt = $this->db->prepare("SELECT * FROM donors WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
}
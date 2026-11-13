class Donor {
    private $user_id;
    private $age;
    private $blood_type;
    private $organ;
    private $availability;
    private $phone;
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createDonor($data) {
        // Validate required fields
        if (empty($data['user_id']) || empty($data['age']) || empty($data['blood_type']) || empty($data['organ']) || empty($data['availability']) || empty($data['phone'])) {
            return ['status' => 'error', 'message' => 'All fields are required.'];
        }

        // Check for duplicate donor profile
        $stmt = $this->db->prepare("SELECT * FROM donors WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return ['status' => 'error', 'message' => 'Donor profile already exists for this user.'];
        }

        // Insert donor profile into the database
        $stmt = $this->db->prepare("INSERT INTO donors (user_id, age, blood_type, organ, availability, phone) VALUES (:user_id, :age, :blood_type, :organ, :availability, :phone)");
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':age', $data['age']);
        $stmt->bindParam(':blood_type', $data['blood_type']);
        $stmt->bindParam(':organ', $data['organ']);
        $stmt->bindParam(':availability', $data['availability']);
        $stmt->bindParam(':phone', $data['phone']);
        
        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Donor profile created successfully.'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to create donor profile.'];
        }
    }

    public function getAllDonors() {
        $stmt = $this->db->prepare("SELECT * FROM donors");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDonorById($id) {
        $stmt = $this->db->prepare("SELECT * FROM donors WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateDonor($id, $data) {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }
        $fields = implode(", ", $fields);

        $stmt = $this->db->prepare("UPDATE donors SET $fields WHERE id = :id");
        $stmt->bindParam(':id', $id);
        foreach ($data as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }

        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Donor profile updated successfully.'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update donor profile.'];
        }
    }

    public function deleteDonor($id) {
        $stmt = $this->db->prepare("DELETE FROM donors WHERE id = :id");
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Donor profile deleted successfully.'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to delete donor profile.'];
        }
    }
}
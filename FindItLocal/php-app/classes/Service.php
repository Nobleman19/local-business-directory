<?php
require_once __DIR__ . '/../config/Database.php';

class Service {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO services (business_id, service_name, description, price, duration, 
                availability_status, discount_percentage, service_category) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "issdisss",
            $data['business_id'],
            $data['service_name'],
            $data['description'],
            $data['price'],
            $data['duration'],
            $data['availability_status'] ?? 'available',
            $data['discount_percentage'] ?? 0,
            $data['service_category'] ?? 'standard'
        );

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function getById($id) {
        $sql = "SELECT * FROM services WHERE id = ? AND availability_status = 'available'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getByBusinessId($business_id, $limit = 50, $offset = 0) {
        $sql = "SELECT * FROM services WHERE business_id = ? LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $business_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function update($id, $data) {
        $fields = [];
        $types = "";
        $values = [];

        foreach ($data as $key => $value) {
            if ($key != 'id' && $key != 'business_id') {
                $fields[] = "$key = ?";
                $values[] = $value;
                $types .= "s";
            }
        }

        if (empty($fields)) {
            return ['success' => false, 'error' => 'No fields to update'];
        }

        $values[] = $id;
        $types .= "i";

        $sql = "UPDATE services SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function delete($id) {
        $sql = "DELETE FROM services WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function searchByName($search, $limit = 20, $offset = 0) {
        $search = "%$search%";
        $sql = "SELECT * FROM services WHERE service_name LIKE ? LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $search, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>

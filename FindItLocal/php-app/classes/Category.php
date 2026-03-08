<?php
require_once __DIR__ . '/../config/Database.php';

class Category {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO categories (category_name, description, icon) VALUES (?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sss",
            $data['category_name'],
            $data['description'] ?? null,
            $data['icon'] ?? null
        );

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function getAll() {
        $sql = "SELECT * FROM categories ORDER BY category_name";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM categories WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getBusinessByCategory($category_id) {
        $sql = "SELECT b.* FROM businesses b
                JOIN business_categories bc ON b.id = bc.business_id
                WHERE bc.category_id = ? AND b.is_active = 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addBusinessCategory($business_id, $category_id) {
        $sql = "INSERT IGNORE INTO business_categories (business_id, category_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $business_id, $category_id);
        return $stmt->execute();
    }

    public function removeBusinessCategory($business_id, $category_id) {
        $sql = "DELETE FROM business_categories WHERE business_id = ? AND category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $business_id, $category_id);
        return $stmt->execute();
    }

    public function getBusinessCategories($business_id) {
        $sql = "SELECT c.* FROM categories c
                JOIN business_categories bc ON c.id = bc.category_id
                WHERE bc.business_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $business_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>

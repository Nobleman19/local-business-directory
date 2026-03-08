<?php
require_once __DIR__ . '/../config/Database.php';

class Discount {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO discounts (discount_code, description, discount_percentage, 
                discount_amount, minimum_amount, maximum_usage, valid_from, valid_until, 
                is_active, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssddddsssi",
            $data['discount_code'],
            $data['description'] ?? null,
            $data['discount_percentage'],
            $data['discount_amount'] ?? null,
            $data['minimum_amount'] ?? null,
            $data['maximum_usage'] ?? null,
            $data['valid_from'],
            $data['valid_until'],
            $data['is_active'] ?? 1,
            $data['created_by'] ?? null
        );

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function getByCode($code) {
        $sql = "SELECT * FROM discounts WHERE discount_code = ? AND is_active = 1 
                AND valid_from <= CURRENT_TIMESTAMP AND valid_until >= CURRENT_TIMESTAMP";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function validateCode($code, $amount = null) {
        $discount = $this->getByCode($code);
        
        if (!$discount) {
            return ['valid' => false, 'error' => 'Invalid or expired discount code'];
        }

        if ($discount['maximum_usage'] && $discount['current_usage'] >= $discount['maximum_usage']) {
            return ['valid' => false, 'error' => 'Discount code usage limit exceeded'];
        }

        if ($discount['minimum_amount'] && $amount < $discount['minimum_amount']) {
            return ['valid' => false, 'error' => 'Amount below minimum required'];
        }

        return ['valid' => true, 'discount' => $discount];
    }

    public function applyDiscount($code) {
        $sql = "UPDATE discounts SET current_usage = current_usage + 1 WHERE discount_code = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $code);
        return $stmt->execute();
    }

    public function getAll($limit = 20, $offset = 0) {
        $sql = "SELECT * FROM discounts ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM discounts WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>

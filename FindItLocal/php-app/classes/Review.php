<?php
require_once __DIR__ . '/../config/Database.php';

class Review {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO reviews (user_id, business_id, service_id, rating, review_text, is_verified_purchase) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "iiisis",
            $data['user_id'],
            $data['business_id'],
            $data['service_id'],
            $data['rating'],
            $data['review_text'],
            $data['is_verified_purchase'] ?? 0
        );

        if ($stmt->execute()) {
            // Update business rating
            $this->updateBusinessRating($data['business_id']);
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function getByBusinessId($business_id, $limit = 10, $offset = 0) {
        $sql = "SELECT r.*, u.first_name, u.last_name
                FROM reviews r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.business_id = ?
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $business_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getByServiceId($service_id, $limit = 10, $offset = 0) {
        $sql = "SELECT r.*, u.first_name, u.last_name
                FROM reviews r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.service_id = ?
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $service_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT r.*, u.first_name, u.last_name
                FROM reviews r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function delete($id) {
        $review = $this->getById($id);
        if ($review) {
            $sql = "DELETE FROM reviews WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $this->updateBusinessRating($review['business_id']);
                return true;
            }
        }
        return false;
    }

    private function updateBusinessRating($business_id) {
        $sql = "UPDATE businesses SET rating = (SELECT AVG(rating) FROM reviews WHERE business_id = ?) 
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $business_id, $business_id);
        return $stmt->execute();
    }
}
?>

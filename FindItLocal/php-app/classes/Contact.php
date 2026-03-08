<?php
require_once __DIR__ . '/../config/Database.php';

class Contact {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function sendMessage($data) {
        $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        $phone = $data['phone'] ?? null;
        
        $stmt->bind_param(
            "sssss",
            $data['name'],
            $data['email'],
            $phone,
            $data['subject'],
            $data['message']
        );

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function getAllMessages($limit = 20, $offset = 0) {
        $sql = "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getUnreadMessages() {
        $sql = "SELECT * FROM contact_messages WHERE is_read = 0 ORDER BY created_at DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM contact_messages WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function markAsRead($id) {
        $sql = "UPDATE contact_messages SET is_read = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM contact_messages WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>

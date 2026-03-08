<?php
require_once __DIR__ . '/../config/Database.php';

class Ticket {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO support_tickets (user_id, subject, description, status, priority) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "issss",
            $data['user_id'],
            $data['subject'],
            $data['description'],
            $data['status'] ?? 'open',
            $data['priority'] ?? 'medium'
        );

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function getAll($limit = 20, $offset = 0) {
        $sql = "SELECT t.*, u.first_name, u.last_name, u.email 
                FROM support_tickets t
                LEFT JOIN users u ON t.user_id = u.id
                ORDER BY t.created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT t.*, u.first_name, u.last_name, u.email 
                FROM support_tickets t
                LEFT JOIN users u ON t.user_id = u.id
                WHERE t.id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getByUserId($user_id, $limit = 20, $offset = 0) {
        $sql = "SELECT * FROM support_tickets WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $user_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function update($id, $data) {
        $fields = [];
        $types = "";
        $values = [];

        foreach ($data as $key => $value) {
            if ($key != 'id') {
                $fields[] = "$key = ?";
                $values[] = $value;
                $types .= "s";
            }
        }

        $values[] = $id;
        $types .= "i";

        $sql = "UPDATE support_tickets SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    public function addReply($ticket_id, $user_id, $reply_text) {
        $sql = "INSERT INTO ticket_replies (ticket_id, user_id, reply_text) VALUES (?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iis", $ticket_id, $user_id, $reply_text);
        
        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function getReplies($ticket_id) {
        $sql = "SELECT tr.*, u.first_name, u.last_name 
                FROM ticket_replies tr
                LEFT JOIN users u ON tr.user_id = u.id
                WHERE tr.ticket_id = ?
                ORDER BY tr.created_at ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ticket_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>

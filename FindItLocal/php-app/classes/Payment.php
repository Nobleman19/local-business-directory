<?php
require_once __DIR__ . '/../config/Database.php';

class Payment {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO payments (booking_id, user_id, amount, payment_method, 
                transaction_id, payment_status) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "iidsss",
            $data['booking_id'],
            $data['user_id'],
            $data['amount'],
            $data['payment_method'],
            $data['transaction_id'],
            $data['payment_status'] ?? 'Completed'
        );

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function getById($id) {
        $sql = "SELECT * FROM payments WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getByBookingId($booking_id) {
        $sql = "SELECT * FROM payments WHERE booking_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getByUserId($user_id, $limit = 20, $offset = 0) {
        $sql = "SELECT p.*, b.scheduled_date, s.service_name
                FROM payments p
                LEFT JOIN bookings b ON p.booking_id = b.id
                LEFT JOIN services s ON b.service_id = s.id
                WHERE p.user_id = ?
                ORDER BY p.payment_date DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $user_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE payments SET payment_status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    public function verifyTransaction($transaction_id) {
        $sql = "SELECT * FROM payments WHERE transaction_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $transaction_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>

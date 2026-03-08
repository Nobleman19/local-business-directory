<?php
require_once __DIR__ . '/../config/Database.php';

class Booking {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO bookings (
                    user_id, 
                    service_id, 
                    business_id, 
                    scheduled_date, 
                    status, 
                    notes, 
                    total_amount, 
                    payment_status, 
                    discount_code,
                    customer_name,
                    customer_email,
                    customer_phone,
                    duration
                ) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        $user_id = $data['user_id'];
        $service_id = $data['service_id'];
        $business_id = $data['business_id'];
        $scheduled_date = $data['scheduled_date'];
        $status = $data['status'] ?? 'pending';
        $notes = $data['notes'] ?? '';
        $total_amount = $data['total_amount'];
        $payment_status = $data['payment_status'] ?? 'pending';
        $discount_code = $data['discount_code'] ?? null;
        $customer_name = $data['customer_name'] ?? '';
        $customer_email = $data['customer_email'] ?? '';
        $customer_phone = $data['customer_phone'] ?? '';
        $duration = $data['duration'] ?? 0;
        
        $stmt->bind_param(
            "iiississssi",
            $user_id,
            $service_id,
            $business_id,
            $scheduled_date,
            $status,
            $notes,
            $total_amount,
            $payment_status,
            $discount_code,
            $customer_name,
            $customer_email,
            $customer_phone,
            $duration
        );

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function getById($id) {
        $sql = "SELECT b.*, s.service_name, s.price, u.first_name, u.last_name, 
                u.email, bus.business_name
                FROM bookings b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN users u ON b.user_id = u.id
                LEFT JOIN businesses bus ON b.business_id = bus.id
                WHERE b.id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getByUserId($user_id, $limit = 20, $offset = 0) {
        $sql = "SELECT b.*, s.service_name, s.price, bus.business_name
                FROM bookings b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN businesses bus ON b.business_id = bus.id
                WHERE b.user_id = ?
                ORDER BY b.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $user_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getByBusinessId($business_id, $limit = 20, $offset = 0) {
        $sql = "SELECT b.*, s.service_name, u.first_name, u.last_name, u.email
                FROM bookings b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.business_id = ?
                ORDER BY b.created_at DESC
                LIMIT ? OFFSET ?";
        
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
            if ($key != 'id') {
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

        $sql = "UPDATE bookings SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function cancel($id) {
        $sql = "UPDATE bookings SET status = 'canceled' WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>

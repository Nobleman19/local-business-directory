<?php
require_once __DIR__ . '/../config/Database.php';


class User {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function register($data) {
        $sql = "INSERT INTO users (first_name, last_name, email, password, phone_number, 
                address_line_1, address_line_2, city, state, postal_code, role) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $role = isset($data['role']) ? $data['role'] : 'customer';
        $addressLine2 = $data['address_line_2'] ?? null;

        $stmt->bind_param(
            "sssssssssss",
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $hashedPassword,
            $data['phone_number'],
            $data['address_line_1'],
            $addressLine2,
            $data['city'],
            $data['state'],
            $data['postal_code'],
            $role
        );

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function getByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getById($id) {
        $sql = "SELECT * FROM users WHERE id = ? AND is_active = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id, $data) {
        $fields = [];
        $types = "";
        $values = [];

        foreach ($data as $key => $value) {
            if ($key != 'id' && $key != 'password') {
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

        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function getAllUsers($limit = 10, $offset = 0) {
        $sql = "SELECT id, first_name, last_name, email, phone_number, role, created_at 
                FROM users WHERE is_active = 1 LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function verifyPassword($plainPassword, $hashedPassword) {
        return password_verify($plainPassword, $hashedPassword);
    }

    public function updateLastLogin($id) {
        $sql = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>

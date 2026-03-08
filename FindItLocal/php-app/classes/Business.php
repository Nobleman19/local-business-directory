<?php
require_once __DIR__ . '/../config/Database.php';

class Business {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO businesses (owner_id, business_name, description, location, 
                contact_number, email, website, working_hours, business_logo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        $owner_id = $data['owner_id'];
        $business_name = $data['business_name'];
        $description = $data['description'];
        $location = $data['location'];
        $contact_number = $data['contact_number'];
        $email = $data['email'];
        $website = $data['website'] ?? null;
        $working_hours = $data['working_hours'] ?? null;
        $business_logo = $data['business_logo'] ?? null;
        
        $stmt->bind_param(
            "issssssss",
            $owner_id,
            $business_name,
            $description,
            $location,
            $contact_number,
            $email,
            $website,
            $working_hours,
            $business_logo
        );

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function getAll($limit = 20, $offset = 0) {
        $sql = "SELECT b.*, u.first_name, u.last_name, COUNT(r.id) as review_count, 
                AVG(r.rating) as rating
                FROM businesses b
                LEFT JOIN users u ON b.owner_id = u.id
                LEFT JOIN reviews r ON b.id = r.business_id
                WHERE b.is_active = 1
                GROUP BY b.id
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT b.*, u.first_name, u.last_name, COUNT(r.id) as review_count, 
                AVG(r.rating) as rating
                FROM businesses b
                LEFT JOIN users u ON b.owner_id = u.id
                LEFT JOIN reviews r ON b.id = r.business_id
                WHERE b.id = ? AND b.is_active = 1
                GROUP BY b.id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getByOwner($owner_id, $limit = 20, $offset = 0) {
        $sql = "SELECT * FROM businesses WHERE owner_id = ? AND is_active = 1 LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $owner_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function update($id, $data) {
        $fields = [];
        $types = "";
        $values = [];

        foreach ($data as $key => $value) {
            if ($key != 'id' && $key != 'owner_id') {
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

        $sql = "UPDATE businesses SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function searchByName($search, $limit = 20, $offset = 0) {
        $search = "%$search%";
        $sql = "SELECT b.*, u.first_name, u.last_name, COUNT(r.id) as review_count, 
                AVG(r.rating) as rating
                FROM businesses b
                LEFT JOIN users u ON b.owner_id = u.id
                LEFT JOIN reviews r ON b.id = r.business_id
                WHERE b.business_name LIKE ? AND b.is_active = 1
                GROUP BY b.id
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $search, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function searchByLocation($location, $limit = 20, $offset = 0) {
        $location = "%$location%";
        $sql = "SELECT b.*, u.first_name, u.last_name, COUNT(r.id) as review_count, 
                AVG(r.rating) as rating
                FROM businesses b
                LEFT JOIN users u ON b.owner_id = u.id
                LEFT JOIN reviews r ON b.id = r.business_id
                WHERE b.location LIKE ? AND b.is_active = 1
                GROUP BY b.id
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $location, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function searchByCategory($category_id, $limit = 20, $offset = 0) {
        $sql = "SELECT b.*, u.first_name, u.last_name, COUNT(r.id) as review_count, 
                AVG(r.rating) as rating
                FROM businesses b
                LEFT JOIN users u ON b.owner_id = u.id
                LEFT JOIN business_categories bc ON b.id = bc.business_id
                LEFT JOIN reviews r ON b.id = r.business_id
                WHERE bc.category_id = ? AND b.is_active = 1
                GROUP BY b.id
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $category_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>

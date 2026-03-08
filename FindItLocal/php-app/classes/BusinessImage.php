<?php
require_once __DIR__ . '/../config/Database.php';

class BusinessImage {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function upload($business_id, $image_url, $is_primary = false) {
        // If this is primary, set all others to non-primary
        if ($is_primary) {
            $sql = "UPDATE business_images SET is_primary = 0 WHERE business_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $business_id);
            $stmt->execute();
        }

        $sql = "INSERT INTO business_images (business_id, image_url, is_primary) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isi", $business_id, $image_url, $is_primary);

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => $stmt->error];
    }

    public function getByBusinessId($business_id) {
        $sql = "SELECT * FROM business_images WHERE business_id = ? ORDER BY is_primary DESC, uploaded_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $business_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getPrimaryImage($business_id) {
        $sql = "SELECT * FROM business_images WHERE business_id = ? AND is_primary = 1 LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $business_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function delete($id) {
        // Get the image first to delete the file
        $sql = "SELECT image_url FROM business_images WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            // Delete the file
            $filepath = UPLOAD_DIR . 'business_images/' . $result['image_url'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }

            // Delete from database
            $sql = "DELETE FROM business_images WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                return ['success' => true];
            }
        }
        return ['success' => false, 'error' => 'Image not found'];
    }

    public function setPrimary($id, $business_id) {
        // Set all images for this business to non-primary
        $sql = "UPDATE business_images SET is_primary = 0 WHERE business_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $business_id);
        $stmt->execute();

        // Set this image as primary
        $sql = "UPDATE business_images SET is_primary = 1 WHERE id = ? AND business_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $id, $business_id);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => $stmt->error];
    }
}
?>

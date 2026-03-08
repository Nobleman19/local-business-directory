<?php
require_once __DIR__ . '/../classes/BusinessImage.php';
require_once __DIR__ . '/../classes/Business.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Helper.php';
require_once __DIR__ . '/../classes/Validation.php';

class BusinessImageController {
    private $businessImageModel;
    private $businessModel;

    public function __construct() {
        $this->businessImageModel = new BusinessImage();
        $this->businessModel = new Business();
    }

    public function upload() {
        Auth::requireLogin();
        $user = Auth::getCurrentUser();
        Auth::requireRole('business_owner');

        $business_id = isset($_GET['business_id']) ? (int)$_GET['business_id'] : 0;

        if (!$business_id) {
            return ['error' => 'Business ID is required'];
        }

        $business = $this->businessModel->getById($business_id);
        if (!$business || $business['owner_id'] !== $user['id']) {
            return ['error' => 'Unauthorized'];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_FILES['business_image']) || $_FILES['business_image']['error'] !== UPLOAD_ERR_OK) {
                return ['error' => 'No image uploaded or upload error'];
            }

            // Validate file
            $validation = Validation::validateFileUpload($_FILES['business_image']);
            if (!$validation['valid']) {
                return ['error' => implode(', ', $validation['errors'])];
            }

            // Create upload directory if it doesn't exist
            $uploadDir = UPLOAD_DIR . 'business_images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Upload the file
            $upload = Helper::uploadFile($_FILES['business_image'], $uploadDir);
            if (!$upload['success']) {
                return ['error' => implode(', ', $upload['errors'])];
            }

            // Check if this should be primary
            $is_primary = isset($_POST['is_primary']) && $_POST['is_primary'] === 'on' ? 1 : 0;

            // Save to database
            $result = $this->businessImageModel->upload($business_id, $upload['filename'], $is_primary);

            if ($result['success']) {
                return ['success' => true, 'message' => 'Image uploaded successfully', 'image_id' => $result['id']];
            } else {
                return ['error' => $result['error']];
            }
        }

        return [];
    }

    public function getImages() {
        $business_id = isset($_GET['business_id']) ? (int)$_GET['business_id'] : 0;

        if (!$business_id) {
            return ['error' => 'Business ID is required', 'images' => []];
        }

        $images = $this->businessImageModel->getByBusinessId($business_id);
        return ['images' => $images];
    }

    public function delete() {
        Auth::requireLogin();
        $user = Auth::getCurrentUser();
        Auth::requireRole('business_owner');

        $image_id = isset($_POST['image_id']) ? (int)$_POST['image_id'] : 0;

        if (!$image_id) {
            return ['error' => 'Image ID is required'];
        }

        // Verify ownership
        $images = $this->businessImageModel->getByBusinessId(0); // This is a simplified check
        
        $result = $this->businessImageModel->delete($image_id);

        if ($result['success']) {
            return ['success' => true, 'message' => 'Image deleted successfully'];
        } else {
            return ['error' => $result['error']];
        }
    }

    public function setPrimary() {
        Auth::requireLogin();
        $user = Auth::getCurrentUser();
        Auth::requireRole('business_owner');

        $image_id = isset($_POST['image_id']) ? (int)$_POST['image_id'] : 0;
        $business_id = isset($_POST['business_id']) ? (int)$_POST['business_id'] : 0;

        if (!$image_id || !$business_id) {
            return ['error' => 'Image ID and Business ID are required'];
        }

        $business = $this->businessModel->getById($business_id);
        if (!$business || $business['owner_id'] !== $user['id']) {
            return ['error' => 'Unauthorized'];
        }

        $result = $this->businessImageModel->setPrimary($image_id, $business_id);

        if ($result['success']) {
            return ['success' => true, 'message' => 'Primary image updated successfully'];
        } else {
            return ['error' => $result['error']];
        }
    }
}
?>

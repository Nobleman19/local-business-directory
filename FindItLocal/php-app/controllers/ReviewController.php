<?php
require_once __DIR__ . '/../classes/Review.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Validation.php';

class ReviewController {
    private $reviewModel;

    public function __construct() {
        $this->reviewModel = new Review();
    }

    public function getByBusiness() {
        $business_id = isset($_GET['business_id']) ? (int)$_GET['business_id'] : 0;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $reviews = $this->reviewModel->getByBusinessId($business_id, $limit, $offset);
        return ['reviews' => $reviews, 'page' => $page];
    }

    public function getByService() {
        $service_id = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $reviews = $this->reviewModel->getByServiceId($service_id, $limit, $offset);
        return ['reviews' => $reviews, 'page' => $page];
    }

    public function create() {
        Auth::requireLogin();
        $user = Auth::getCurrentUser();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = Validation::sanitize($_POST);
            $data['user_id'] = $user['id'];

            if (empty($data['business_id']) || empty($data['service_id']) || 
                empty($data['rating']) || empty($data['review_text'])) {
                return ['error' => 'All required fields must be filled'];
            }

            if ($data['rating'] < 1 || $data['rating'] > 5) {
                return ['error' => 'Rating must be between 1 and 5'];
            }

            $result = $this->reviewModel->create($data);

            if ($result['success']) {
                return ['success' => true, 'message' => 'Review submitted successfully'];
            } else {
                return ['error' => $result['error']];
            }
        }

        return [];
    }

    public function delete() {
        Auth::requireLogin();
        $user = Auth::getCurrentUser();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        $review = $this->reviewModel->getById($id);
        if (!$review || $review['user_id'] !== $user['id']) {
            return ['error' => 'Unauthorized'];
        }

        if ($this->reviewModel->delete($id)) {
            return ['success' => true, 'message' => 'Review deleted successfully'];
        }

        return ['error' => 'Cannot delete review'];
    }
}
?>

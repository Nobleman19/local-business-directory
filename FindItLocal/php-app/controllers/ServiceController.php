<?php
require_once __DIR__ . '/../classes/Service.php';
require_once __DIR__ . '/../classes/Business.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Validation.php';

class ServiceController {
    private $serviceModel;
    private $businessModel;

    public function __construct() {
        $this->serviceModel = new Service();
        $this->businessModel = new Business();
    }

    public function getById() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            return ['error' => 'Service not found'];
        }

        $service = $this->serviceModel->getById($id);
        return ['service' => $service ?? null];
    }

    public function getByBusiness() {
        $business_id = isset($_GET['business_id']) ? (int)$_GET['business_id'] : 0;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $services = $this->serviceModel->getByBusinessId($business_id, $limit, $offset);
        return ['services' => $services, 'page' => $page];
    }

    public function create() {
        Auth::requireRole('business_owner');
        $user = Auth::getCurrentUser();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = Validation::sanitize($_POST);

            $business = $this->businessModel->getById($data['business_id']);
            if (!$business || $business['owner_id'] !== $user['id']) {
                return ['error' => 'Unauthorized'];
            }

            if (empty($data['service_name']) || empty($data['description']) || 
                empty($data['price']) || empty($data['duration'])) {
                return ['error' => 'All required fields must be filled'];
            }

            $result = $this->serviceModel->create($data);

            if ($result['success']) {
                return ['success' => true, 'message' => 'Service created successfully', 'id' => $result['id']];
            } else {
                return ['error' => $result['error']];
            }
        }

        return [];
    }

    public function update() {
        Auth::requireLogin();
        $user = Auth::getCurrentUser();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$id) {
            return ['error' => 'Service not found'];
        }

        $service = $this->serviceModel->getById($id);
        if (!$service) {
            return ['error' => 'Service not found'];
        }

        $business = $this->businessModel->getById($service['business_id']);
        if (!$business || $business['owner_id'] !== $user['id']) {
            return ['error' => 'Unauthorized'];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = Validation::sanitize($_POST);
            $result = $this->serviceModel->update($id, $data);

            if ($result['success']) {
                return ['success' => true, 'message' => 'Service updated successfully'];
            } else {
                return ['error' => $result['error']];
            }
        }

        return ['service' => $service];
    }

    public function delete() {
        Auth::requireLogin();
        $user = Auth::getCurrentUser();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        $service = $this->serviceModel->getById($id);
        if (!$service) {
            return ['error' => 'Service not found'];
        }

        $business = $this->businessModel->getById($service['business_id']);
        if (!$business || $business['owner_id'] !== $user['id']) {
            return ['error' => 'Unauthorized'];
        }

        if ($this->serviceModel->delete($id)) {
            return ['success' => true, 'message' => 'Service deleted successfully'];
        }

        return ['error' => 'Cannot delete service'];
    }

    public function search() {
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $services = [];
        if (!empty($query)) {
            $services = $this->serviceModel->searchByName($query, $limit, $offset);
        }

        return ['services' => $services, 'query' => $query, 'page' => $page];
    }
}
?>

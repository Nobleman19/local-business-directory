<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Business.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Service.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Validation.php';
require_once __DIR__ . '/../classes/Helper.php';

class BusinessController {
    private $businessModel;
    private $categoryModel;
    private $serviceModel;

    public function __construct() {
        $this->businessModel = new Business();
        $this->categoryModel = new Category();
        $this->serviceModel = new Service();
    }

    public function getAll() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $businesses = $this->businessModel->getAll($limit, $offset);
        
        foreach ($businesses as &$business) {
            $business['categories'] = $this->categoryModel->getBusinessCategories($business['id']);
        }

        return ['businesses' => $businesses, 'page' => $page];
    }

    public function getById() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            return ['error' => 'Business not found'];
        }

        $business = $this->businessModel->getById($id);
        if ($business) {
            $business['categories'] = $this->categoryModel->getBusinessCategories($id);
        }

        return ['business' => $business ?? null];
    }

    public function search() {
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';
        $category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $businesses = [];

        if (!empty($query)) {
            $businesses = $this->businessModel->searchByName($query, $limit, $offset);
        } elseif (!empty($location)) {
            $businesses = $this->businessModel->searchByLocation($location, $limit, $offset);
        } elseif ($category > 0) {
            $businesses = $this->businessModel->searchByCategory($category, $limit, $offset);
        }

        foreach ($businesses as &$business) {
            $business['categories'] = $this->categoryModel->getBusinessCategories($business['id']);
        }

        return [
            'businesses' => $businesses,
            'query' => $query,
            'location' => $location,
            'category' => $category,
            'page' => $page
        ];
    }

    public function create() {
        Auth::requireRole('business_owner');
        $user = Auth::getCurrentUser();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [];
            $errors = [];

            // Validate and sanitize business name
            if (empty($_POST['business_name'])) {
                $errors[] = 'Business name is required';
            } else {
                $name = trim($_POST['business_name']);
                if (strlen($name) < 3) {
                    $errors[] = 'Business name must be at least 3 characters';
                } elseif (strlen($name) > 255) {
                    $errors[] = 'Business name cannot exceed 255 characters';
                } else {
                    $data['business_name'] = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
                }
            }

            // Validate and sanitize description
            if (empty($_POST['description'])) {
                $errors[] = 'Description is required';
            } else {
                $desc = trim($_POST['description']);
                if (strlen($desc) < 20) {
                    $errors[] = 'Description must be at least 20 characters';
                } elseif (strlen($desc) > 5000) {
                    $errors[] = 'Description cannot exceed 5000 characters';
                } else {
                    $data['description'] = htmlspecialchars($desc, ENT_QUOTES, 'UTF-8');
                }
            }

            // Validate and sanitize location
            if (empty($_POST['location'])) {
                $errors[] = 'Location/Address is required';
            } else {
                $location = trim($_POST['location']);
                if (strlen($location) < 5) {
                    $errors[] = 'Location must be at least 5 characters';
                } elseif (strlen($location) > 255) {
                    $errors[] = 'Location cannot exceed 255 characters';
                } else {
                    $data['location'] = htmlspecialchars($location, ENT_QUOTES, 'UTF-8');
                }
            }

            // Validate and sanitize contact number
            if (empty($_POST['contact_number'])) {
                $errors[] = 'Contact number is required';
            } else {
                $contact = trim($_POST['contact_number']);
                if (!Validation::validateContactNumber($contact)) {
                    $errors[] = 'Invalid contact number format (use +1 to +15 digits, e.g. +260123456789)';
                } else {
                    $data['contact_number'] = htmlspecialchars($contact, ENT_QUOTES, 'UTF-8');
                }
            }

            // Validate and sanitize email
            if (empty($_POST['email'])) {
                $errors[] = 'Email address is required';
            } else {
                $email = trim($_POST['email']);
                if (!Validation::validateEmail($email)) {
                    $errors[] = 'Invalid email address format';
                } else {
                    $data['email'] = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
                }
            }

            // Validate and sanitize website (optional)
            if (!empty($_POST['website'])) {
                $website = trim($_POST['website']);
                if (!Validation::validateUrl($website)) {
                    $errors[] = 'Invalid website URL format';
                } else {
                    $data['website'] = htmlspecialchars($website, ENT_QUOTES, 'UTF-8');
                }
            } else {
                $data['website'] = null;
            }

            // Validate and sanitize working hours (optional)
            if (!empty($_POST['working_hours'])) {
                $hours = trim($_POST['working_hours']);
                if (strlen($hours) > 500) {
                    $errors[] = 'Working hours text is too long';
                } else {
                    $data['working_hours'] = htmlspecialchars($hours, ENT_QUOTES, 'UTF-8');
                }
            } else {
                $data['working_hours'] = null;
            }

            // Handle logo upload if provided
            if (isset($_FILES['business_logo']) && $_FILES['business_logo']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['business_logo']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = UPLOAD_DIR . 'business_logos/';
                    $upload = Helper::uploadFile($_FILES['business_logo'], $uploadDir);
                    if ($upload['success']) {
                        $data['business_logo'] = $upload['filename'];
                    } else {
                        $errors[] = 'Failed to upload business logo: ' . implode(', ', $upload['errors'] ?? ['Unknown error']);
                    }
                } else {
                    $errors[] = 'Error uploading file. Please try again.';
                }
            }

            // Validate primary category is selected
            if (empty($_POST['primary_category'])) {
                $errors[] = 'Please select a primary business category';
            } elseif (!is_numeric($_POST['primary_category'])) {
                $errors[] = 'Invalid category selection';
            }

            // Prepare categories array - include primary category and any additional categories
            $categoriesToAdd = [];
            if (!empty($_POST['primary_category']) && is_numeric($_POST['primary_category'])) {
                $categoriesToAdd[] = (int)$_POST['primary_category'];
            }
            
            // Add additional categories if selected
            if (!empty($_POST['categories']) && is_array($_POST['categories'])) {
                foreach ($_POST['categories'] as $catId) {
                    if (is_numeric($catId)) {
                        $catId = (int)$catId;
                        if (!in_array($catId, $categoriesToAdd)) {
                            $categoriesToAdd[] = $catId;
                        }
                    }
                }
            }

            // If there are validation errors, return them
            if (!empty($errors)) {
                return ['error' => implode('<br>', $errors)];
            }

            // Add owner ID
            $data['owner_id'] = $user['id'];

            // Create the business
            $result = $this->businessModel->create($data);

            if ($result['success']) {
                // Add categories
                $categoryErrors = [];
                foreach ($categoriesToAdd as $categoryId) {
                    if (!$this->categoryModel->addBusinessCategory($result['id'], $categoryId)) {
                        $categoryErrors[] = "Failed to add one or more categories";
                        break;
                    }
                }

                if (!empty($categoryErrors)) {
                    return ['error' => implode('<br>', $categoryErrors)];
                }

                // Add services if provided
                if (!empty($_POST['services']) && is_array($_POST['services'])) {
                    $serviceErrors = [];
                    foreach ($_POST['services'] as $index => $service) {
                        // Only add service if name and price are provided
                        if (!empty($service['name']) && !empty($service['price'])) {
                            $serviceName = trim($service['name']);
                            $servicePrice = floatval($service['price']);
                            $serviceDesc = !empty($service['description']) ? trim($service['description']) : '';

                            if (strlen($serviceName) > 0 && strlen($serviceName) <= 255 && $servicePrice >= 0) {
                                $serviceData = [
                                    'business_id' => $result['id'],
                                    'service_name' => htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8'),
                                    'description' => htmlspecialchars($serviceDesc, ENT_QUOTES, 'UTF-8'),
                                    'price' => $servicePrice,
                                    'duration' => 60, // Default duration
                                    'availability_status' => 'available',
                                    'discount_percentage' => 0,
                                    'service_category' => 'standard'
                                ];
                                
                                $serviceResult = $this->serviceModel->create($serviceData);
                                if (!$serviceResult['success']) {
                                    $serviceErrors[] = "Failed to add service: " . htmlspecialchars($serviceName);
                                }
                            }
                        }
                    }

                    if (!empty($serviceErrors)) {
                        return ['warning' => 'Business created but some services failed: ' . implode(', ', $serviceErrors)];
                    }
                }

                return [
                    'success' => true, 
                    'message' => 'Business and services created successfully! You can now add images.', 
                    'id' => $result['id']
                ];
            } else {
                return ['error' => 'Failed to create business: ' . ($result['error'] ?? 'Unknown error')];
            }
        }

        $categories = $this->categoryModel->getAll();
        return ['categories' => $categories];
    }

    public function update() {
        Auth::requireLogin();
        $user = Auth::getCurrentUser();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$id) {
            return ['error' => 'Business not found'];
        }

        $business = $this->businessModel->getById($id);

        if (!$business || $business['owner_id'] !== $user['id']) {
            return ['error' => 'Unauthorized'];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = Validation::sanitize($_POST);

            // Handle logo upload if provided
            if (isset($_FILES['business_logo']) && $_FILES['business_logo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = UPLOAD_DIR . 'business_logos/';
                $upload = Helper::uploadFile($_FILES['business_logo'], $uploadDir);
                if ($upload['success']) {
                    // Delete old logo if exists
                    if (!empty($business['business_logo'])) {
                        $oldFilePath = $uploadDir . $business['business_logo'];
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                    $data['business_logo'] = $upload['filename'];
                }
            }

            $result = $this->businessModel->update($id, $data);

            if ($result['success']) {
                // Update categories if provided
                $categoriesToAdd = [];
                
                // Add primary category if selected
                if (!empty($_POST['primary_category']) && is_numeric($_POST['primary_category'])) {
                    $categoriesToAdd[] = (int)$_POST['primary_category'];
                }
                
                // Add additional categories if selected
                if (!empty($_POST['categories']) && is_array($_POST['categories'])) {
                    foreach ($_POST['categories'] as $catId) {
                        if (is_numeric($catId)) {
                            $catId = (int)$catId;
                            if (!in_array($catId, $categoriesToAdd)) {
                                $categoriesToAdd[] = $catId;
                            }
                        }
                    }
                }
                
                // Update categories in database
                if (!empty($categoriesToAdd)) {
                    // First, delete existing categories
                    $db = Database::getInstance();
                    $conn = $db->getConnection();
                    $deleteSQL = "DELETE FROM business_categories WHERE business_id = ?";
                    $stmt = $conn->prepare($deleteSQL);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();

                    // Add new categories
                    foreach ($categoriesToAdd as $categoryId) {
                        $this->categoryModel->addBusinessCategory($id, $categoryId);
                    }
                }
                return ['success' => true, 'message' => 'Business updated successfully'];
            } else {
                return ['error' => $result['error']];
            }
        }

        $business['categories'] = $this->categoryModel->getBusinessCategories($id);
        $allCategories = $this->categoryModel->getAll();

        return ['business' => $business, 'categories' => $allCategories];
    }
}
?>

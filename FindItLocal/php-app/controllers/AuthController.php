<?php
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Validation.php';
require_once __DIR__ . '/../classes/Helper.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->sanitizeInput($_POST);

            // Validation
            if (empty($data['first_name']) || empty($data['last_name']) || 
                empty($data['email']) || empty($data['password']) || 
                empty($data['phone_number']) || empty($data['address_line_1']) ||
                empty($data['city']) || empty($data['state']) || empty($data['postal_code'])) {
                return ['error' => 'All fields are required'];
            }

            if (!Validation::validateEmail($data['email'])) {
                return ['error' => 'Invalid email format'];
            }

            if (!Validation::validatePassword($data['password'])) {
                return ['error' => 'Password must be at least 6 characters'];
            }

            if (!Validation::validatePhone($data['phone_number'])) {
                return ['error' => 'Phone number must be 10 digits'];
            }

            if (!Validation::validatePostalCode($data['postal_code'])) {
                return ['error' => 'Postal code must be 6 digits'];
            }

            // Check if user exists
            if ($this->userModel->getByEmail($data['email'])) {
                return ['error' => 'Email already registered'];
            }

            // Set role from form or default to customer
            $role = isset($data['role']) && in_array($data['role'], ['customer', 'business_owner']) ? $data['role'] : 'customer';
            $data['role'] = $role;
            $result = $this->userModel->register($data);

            if ($result['success']) {
                // Auto-login the user
                $user = $this->userModel->getByEmail($data['email']);
                if ($user) {
                    Auth::login($user);
                }
                return ['success' => true, 'message' => 'Registration successful. Welcome!'];
            } else {
                return ['error' => $result['error']];
            }
        }
        return [];
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->sanitizeInput($_POST);

            if (empty($data['email']) || empty($data['password'])) {
                return ['error' => 'Email and password are required'];
            }

            $user = $this->userModel->getByEmail($data['email']);

            if (!$user || !$this->userModel->verifyPassword($data['password'], $user['password'])) {
                return ['error' => 'Invalid email or password'];
            }

            if (!$user['is_active']) {
                return ['error' => 'Account is deactivated'];
            }

            Auth::login($user);
            $this->userModel->updateLastLogin($user['id']);
            
            return ['success' => true, 'message' => 'Login successful', 'role' => $user['role']];
        }
        return [];
    }

    public function logout() {
        Auth::logout();
        Helper::redirect('/FindItLocal/php-app/');
    }

    public function profile() {
        Auth::requireLogin();
        $user = Auth::getCurrentUser();
        $userData = $this->userModel->getById($user['id']);
        return ['user' => $userData];
    }

    public function updateProfile() {
        Auth::requireLogin();
        $user = Auth::getCurrentUser();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->sanitizeInput($_POST);
            $data['id'] = $user['id'];

            $result = $this->userModel->update($user['id'], $data);

            if ($result['success']) {
                return ['success' => true, 'message' => 'Profile updated successfully'];
            } else {
                return ['error' => $result['error']];
            }
        }

        return ['user' => $this->userModel->getById($user['id'])];
    }

    private function sanitizeInput($data) {
        return Validation::sanitize($data);
    }
}
?>

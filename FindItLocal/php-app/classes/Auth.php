<?php
require_once __DIR__ . '/../config/config.php';

class Auth {
    private static $sessionTimeout = SESSION_TIMEOUT;

    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($user) {
        self::startSession();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['login_time'] = time();
        return true;
    }

    public static function logout() {
        self::startSession();
        session_unset();
        session_destroy();
        return true;
    }

    public static function isLoggedIn() {
        self::startSession();
        
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Check session timeout
        if (isset($_SESSION['login_time'])) {
            if (time() - $_SESSION['login_time'] > self::$sessionTimeout) {
                self::logout();
                return false;
            }
            $_SESSION['login_time'] = time(); // Reset timeout
        }

        return true;
    }

    public static function getCurrentUser() {
        self::startSession();
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'email' => $_SESSION['email'],
                'role' => $_SESSION['role'],
                'first_name' => $_SESSION['first_name'],
                'last_name' => $_SESSION['last_name']
            ];
        }
        return null;
    }

    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: /FindItLocal/php-app/login');
            exit();
        }
    }

    public static function requireRole($role) {
        self::startSession();
        if (!self::isLoggedIn() || $_SESSION['role'] !== $role) {
            header('HTTP/1.0 403 Forbidden');
            die('Access Denied');
        }
    }

    public static function isAdmin() {
        self::startSession();
        return self::isLoggedIn() && $_SESSION['role'] === 'admin';
    }

    public static function isBusinessOwner() {
        self::startSession();
        return self::isLoggedIn() && $_SESSION['role'] === 'business_owner';
    }

    public static function isCustomer() {
        self::startSession();
        return self::isLoggedIn() && $_SESSION['role'] === 'customer';
    }

    public static function generateToken($data) {
        // Simple token generation using base64 and timestamp
        $token = base64_encode(json_encode($data) . time());
        return $token;
    }

    public static function verifyToken($token) {
        try {
            $decoded = json_decode(base64_decode($token), true);
            return $decoded;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>

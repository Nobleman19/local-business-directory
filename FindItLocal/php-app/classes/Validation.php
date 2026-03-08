<?php
class Validation {
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validatePassword($password) {
        return strlen($password) >= 6;
    }

    public static function validatePhone($phone) {
        return preg_match('/^\d{10}$/', $phone);
    }

    public static function validatePostalCode($code) {
        return preg_match('/^\d{6}$/', $code);
    }

    public static function validateContactNumber($number) {
        return preg_match('/^\+\d{1,15}$/', $number);
    }

    public static function validateUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map(fn($item) => self::sanitize($item), $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    public static function validateFileUpload($file, $maxSize = 5242880, $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']) {
        $errors = [];

        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['valid' => false, 'errors' => ['No file uploaded']];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'errors' => ['File upload error']];
        }

        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size';
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions)) {
            $errors[] = 'File type not allowed';
        }

        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }

        return ['valid' => true];
    }
}
?>

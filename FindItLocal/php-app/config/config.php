<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'finditlocal_db');
define('DB_PORT', 3306);

// Application Configuration
define('APP_NAME', 'Find It Local - Business Directory');
define('APP_URL', 'http://localhost:8080');
define('JWT_SECRET', 'your-secret-key-change-this-in-production');
define('SESSION_TIMEOUT', 3600); // 1 hour

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Create uploads and logs directories if they don't exist
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
if (!is_dir(UPLOAD_DIR . 'business_logos')) {
    mkdir(UPLOAD_DIR . 'business_logos', 0755, true);
}
if (!is_dir(UPLOAD_DIR . 'business_images')) {
    mkdir(UPLOAD_DIR . 'business_images', 0755, true);
}
if (!is_dir(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}
?>

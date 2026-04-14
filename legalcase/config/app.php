<?php

define('APP_NAME', 'LegalCase Pro');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost:8080');

// Database
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'legalcase_db');
define('DB_USER', 'root');
define('DB_PASS', 'yourpassword');
define('DB_CHARSET', 'utf8mb4');

// Session
define('SESSION_LIFETIME', 7200); // 2 hours
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

// Upload settings
define('UPLOAD_PATH', BASE_PATH . '/public/uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_TYPES', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);

// Timezone
date_default_timezone_set('Africa/Kampala');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

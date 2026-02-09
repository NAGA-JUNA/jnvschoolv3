<?php
// ============================================
// JSchoolAdmin — Database & App Configuration
// ============================================

// Database Configuration
// IMPORTANT: Update these values with your actual cPanel database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'cpanelusr_jschooladmin_db');  // Your full database name
define('DB_USER', 'cpanelusr_jschooladmin_user'); // Your full database username
define('DB_PASS', 'YOUR_STRONG_PASSWORD_HERE');   // Database password
define('DB_CHARSET', 'utf8mb4');

// JWT Configuration
define('JWT_SECRET', 'CHANGE_THIS_TO_A_RANDOM_64_CHAR_STRING_KEEP_SECRET');
define('JWT_EXPIRY', 86400); // 24 hours in seconds

// App Configuration
define('APP_NAME', 'JSchoolAdmin');
define('APP_URL', 'https://yourdomain.com');
define('API_URL', 'https://yourdomain.com/api');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB

// Timezone
date_default_timezone_set('Asia/Kolkata');

// PDO Connection Singleton
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Database connection failed']);
            exit;
        }
    }
    return $pdo;
}

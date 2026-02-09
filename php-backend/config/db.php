<?php
// Database Configuration — Update with your cPanel credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'YOUR_DB_NAME');       // e.g. cpuser_schooldb
define('DB_USER', 'YOUR_DB_USER');       // e.g. cpuser_schooluser
define('DB_PASS', 'YOUR_DB_PASSWORD');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

<?php
// Database Configuration — Production (jnvschool.awayindia.com)
define('DB_HOST', 'localhost');
define('DB_NAME', 'yshszsos_jnvschool');
define('DB_USER', 'yshszsos_Admin');
define('DB_PASS', 'c5oNh1Hu!c,5)_[h');
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

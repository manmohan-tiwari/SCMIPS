<?php
/**
 * SCMIPS - Central Public Database Connection Provider (PDO)
 * Automatically loads production database credentials from admin/config/config.php if present.
 */

$adminConfig = __DIR__ . '/../admin/config/config.php';

if (file_exists($adminConfig)) {
    require_once $adminConfig;
    $host = defined('DB_HOST') ? DB_HOST : 'localhost';
    $dbname = defined('DB_NAME') ? DB_NAME : 'scmips_gallery_admin';
    $username = defined('DB_USER') ? DB_USER : 'root';
    $password = defined('DB_PASS') ? DB_PASS : '';
} else {
    // Local development fallback
    $host = "localhost";
    $dbname = "scmips_gallery_admin";
    $username = "root";
    $password = "";
}

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ]
    );
} catch (PDOException $e) {
    error_log("Public DB Connection Error: " . $e->getMessage());
    die("Database connection failed.");
}

<?php
/**
 * SCMIPS Gallery Admin Panel - Configuration Template
 * 
 * Instructions:
 * 1. Copy this file to `config.php` in the same directory:
 *    cp admin/config/config.sample.php admin/config/config.php
 * 2. Update the database credentials and application settings below.
 * 3. Never commit `config.php` to Git (it is already in .gitignore).
 */

// Prevent direct script access
defined('APP_ROOT') or define('APP_ROOT', dirname(__DIR__));

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'scmips_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application URL Configuration (without trailing slash)
// Local example: 'http://localhost/SCMIPS-Admin'
// Production example: 'https://scmips.in'
define('APP_URL', 'http://localhost/SCMIPS-Admin');

// Admin Uploads Settings
define('UPLOAD_DIR', APP_ROOT . '/uploads/gallery/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5 MB per image
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// Session Settings
define('SESSION_LIFETIME', 3600 * 2); // 2 hours

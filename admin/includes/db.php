<?php
/**
 * SCMIPS Gallery Admin Panel - Database Connection Provider (PDO)
 * 
 * Provides a secure, persistent PDO database connection using prepared statements.
 */

// Load configuration
$configFile = __DIR__ . '/../config/config.php';
if (!file_exists($configFile)) {
    die('<div style="font-family:sans-serif;padding:20px;color:#721c24;background:#f8d7da;border:1px solid #f5c6cb;border-radius:4px;">' .
        '<strong>Configuration Error:</strong> <code>admin/config/config.php</code> not found. ' .
        'Please copy <code>admin/config/config.sample.php</code> to <code>admin/config/config.php</code> and set your database credentials.</div>');
}

require_once $configFile;

/**
 * Returns a singleton PDO instance.
 *
 * @return PDO
 */
function getDbConnection(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log real error for administrator review
            error_log('Database Connection Error: ' . $e->getMessage());

            // Friendly error message without leaking sensitive server credentials
            die('<div style="font-family:sans-serif;padding:20px;color:#721c24;background:#f8d7da;border:1px solid #f5c6cb;border-radius:4px;">' .
                '<strong>Database Connection Failed:</strong> Unable to connect to the database. ' .
                'Please check your database service and configuration settings.</div>');
        }
    }

    return $pdo;
}

// Global convenience variable for scripts
$pdo = getDbConnection();

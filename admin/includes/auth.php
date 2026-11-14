<?php
/**
 * SCMIPS Gallery Admin Panel - Authentication Gatekeeper
 * 
 * Include this at the top of every protected admin page.
 * Unauthenticated users are redirected to login.php immediately.
 */

require_once __DIR__ . '/functions.php';

// Check if admin is currently logged in
if (empty($_SESSION['admin_logged_in']) || empty($_SESSION['admin_id'])) {
    set_flash('danger', 'Please log in to access the administration panel.');
    
    // Determine relative path to login.php
    $loginUrl = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) 
        ? dirname($_SERVER['PHP_SELF']) . '/login.php' 
        : 'login.php';

    // Normalize path to root admin login.php
    if (basename(dirname($_SERVER['PHP_SELF'])) !== 'admin') {
        $loginUrl = '../login.php';
    } else {
        $loginUrl = 'login.php';
    }

    header('Location: ' . $loginUrl);
    exit;
}

/**
 * Returns currently authenticated admin info.
 *
 * @return array
 */
function current_admin(): array {
    return [
        'id'       => $_SESSION['admin_id'] ?? null,
        'username' => $_SESSION['admin_username'] ?? '',
        'name'     => $_SESSION['admin_name'] ?? 'Administrator',
    ];
}

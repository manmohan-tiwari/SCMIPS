<?php
/**
 * SCMIPS Gallery Admin Panel - Entry Point
 */

require_once __DIR__ . '/includes/functions.php';

if (!empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_id'])) {
    redirect('dashboard.php');
} else {
    redirect('login.php');
}

<?php
/**
 * SCMIPS Gallery Admin Panel - Reusable Header & Navigation Bar
 */

// Set HTTP Security Headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

require_once __DIR__ . '/functions.php';

// Determine relative path back to the /admin/ directory
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
$adminPath = ($currentDir === 'admin') ? '' : '../';
$publicPath = ($currentDir === 'admin') ? '../' : '../../';

$pageTitle = $pageTitle ?? 'SCMIPS Admin Panel';
$activePage = $activePage ?? '';

$flashes = get_flashes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — SCMIPS Admin</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Custom Admin Styles -->
    <link href="<?= $adminPath ?>assets/css/admin.css" rel="stylesheet">
</head>
<body>

<!-- Main Admin Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-admin sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="<?= $adminPath ?>dashboard.php">
            <i class="fa-solid fa-graduation-cap me-2 fa-lg text-warning"></i>
            <span>SCMIPS</span>&nbsp;Admin
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" 
                aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage === 'dashboard') ? 'active' : '' ?>" href="<?= $adminPath ?>dashboard.php">
                        <i class="fa-solid fa-gauge me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage === 'sessions') ? 'active' : '' ?>" href="<?= $adminPath ?>sessions/index.php">
                        <i class="fa-solid fa-calendar-days me-1"></i> Sessions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage === 'events') ? 'active' : '' ?>" href="<?= $adminPath ?>events/index.php">
                        <i class="fa-solid fa-calendar-check me-1"></i> Events
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage === 'upload') ? 'active' : '' ?>" href="<?= $adminPath ?>images/upload.php">
                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Photos
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-2">
                    <a class="btn btn-sm btn-outline-light" href="<?= $publicPath ?>" target="_blank" title="View Public Website">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Website
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" id="adminUserDropdown" 
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-circle-user fa-lg me-2 text-warning"></i>
                        <span><?= e($_SESSION['admin_name'] ?? 'Admin') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="adminUserDropdown">
                        <li>
                            <h6 class="dropdown-header small text-muted">
                                Signed in as <strong><?= e($_SESSION['admin_username'] ?? 'admin') ?></strong>
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?= $adminPath ?>logout.php">
                                <i class="fa-solid fa-right-from-bracket me-2"></i> Log Out
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Admin Content Container -->
<main class="container-fluid px-4 py-4 flex-grow-1">
    <?php if (!empty($flashes)): ?>
        <div class="row">
            <div class="col-12">
                <?php foreach ($flashes as $flash): ?>
                    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
                        <?php if ($flash['type'] === 'success'): ?>
                            <i class="fa-solid fa-circle-check me-1"></i>
                        <?php elseif ($flash['type'] === 'danger'): ?>
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        <?php elseif ($flash['type'] === 'warning'): ?>
                            <i class="fa-solid fa-circle-exclamation me-1"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-circle-info me-1"></i>
                        <?php endif; ?>
                        <?= e($flash['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

<?php
/**
 * SCMIPS Gallery Admin Panel - Reusable Modern Header & Sidebar Navigation
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

$pageTitle = $pageTitle ?? 'Dashboard';
$activePage = $activePage ?? 'dashboard';

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
    <!-- Custom Admin Styles (Nexa Modern Theme) -->
    <link href="<?= $adminPath ?>assets/css/admin.css" rel="stylesheet">
</head>
<body>

<div class="admin-wrapper">
    <!-- Left Sidebar Navigation -->
    <aside class="admin-sidebar" id="adminSidebar">
        <!-- Top Brand Header (Dark Charcoal) -->
        <div class="sidebar-brand">
            <a href="<?= $adminPath ?>dashboard.php" class="brand-link">
                <span>SCMIPS</span><span class="brand-dot">•</span>
            </a>
        </div>

        <!-- User Profile Card (Rich Amber Yellow) -->
        <div class="sidebar-user-card">
            <div class="user-avatar-wrap">
                <i class="fa-solid fa-user-tie user-avatar-icon"></i>
            </div>
            <div class="user-info">
                <div class="user-name"><?= e($_SESSION['admin_name'] ?? 'Admin') ?></div>
                <div class="user-role"><?= e($_SESSION['admin_username'] ?? 'admin') ?>@scmips.in</div>
            </div>
            <a href="<?= $adminPath ?>profile.php" class="user-action-btn" title="Edit Profile & Password">
                <i class="fa-solid fa-chevron-down"></i>
            </a>
        </div>

        <!-- Sidebar Menu Items -->
        <div class="sidebar-menu-wrap">
            <div class="sidebar-heading">MAIN NAVIGATION</div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="<?= $adminPath ?>dashboard.php" class="sidebar-link <?= ($activePage === 'dashboard') ? 'active' : '' ?>">
                        <i class="fa-solid fa-house"></i>
                        <span>Dashboard</span>
                        <i class="fa-solid fa-chevron-right item-arrow"></i>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= $adminPath ?>sessions/index.php" class="sidebar-link <?= ($activePage === 'sessions') ? 'active' : '' ?>">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>Academic Sessions</span>
                        <i class="fa-solid fa-chevron-right item-arrow"></i>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= $adminPath ?>events/index.php" class="sidebar-link <?= ($activePage === 'events') ? 'active' : '' ?>">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Events</span>
                        <i class="fa-solid fa-chevron-right item-arrow"></i>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= $adminPath ?>images/upload.php" class="sidebar-link <?= ($activePage === 'upload') ? 'active' : '' ?>">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Upload Photos</span>
                        <i class="fa-solid fa-chevron-right item-arrow"></i>
                    </a>
                </li>
            </ul>

            <div class="sidebar-heading">SYSTEM & ACCOUNT</div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="<?= $adminPath ?>profile.php" class="sidebar-link <?= ($activePage === 'profile') ? 'active' : '' ?>">
                        <i class="fa-solid fa-user-gear"></i>
                        <span>Profile & Security</span>
                        <i class="fa-solid fa-chevron-right item-arrow"></i>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= $adminPath ?>health.php" class="sidebar-link" target="_blank">
                        <i class="fa-solid fa-heart-pulse text-success"></i>
                        <span>System Health</span>
                        <i class="fa-solid fa-arrow-up-right-from-square item-arrow"></i>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= $publicPath ?>" class="sidebar-link" target="_blank">
                        <i class="fa-solid fa-globe text-primary"></i>
                        <span>View Public Website</span>
                        <i class="fa-solid fa-arrow-up-right-from-square item-arrow"></i>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= $adminPath ?>logout.php" class="sidebar-link text-danger">
                        <i class="fa-solid fa-power-off text-danger"></i>
                        <span>Log Out</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Mobile Overlay Backdrop -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Top Navbar Header -->
        <header class="admin-topbar">
            <div class="topbar-left">
                <button type="button" class="topbar-btn" id="sidebarToggle" title="Toggle Sidebar">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>
                <a href="<?= $publicPath ?>" target="_blank" class="topbar-link d-none d-md-flex align-items-center" title="Open Public Website">
                    <i class="fa-solid fa-globe me-1 text-primary"></i>
                    <span>Public Website</span>
                </a>
                <a href="<?= $adminPath ?>health.php" target="_blank" class="topbar-link d-none d-lg-flex align-items-center" title="Check System Health">
                    <i class="fa-solid fa-circle-check me-1 text-success"></i>
                    <span>System Healthy</span>
                </a>
            </div>

            <div class="topbar-right">
                <a href="<?= $adminPath ?>images/upload.php" class="topbar-btn" title="Upload Photos">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </a>
                <div class="dropdown">
                    <button class="topbar-user-btn" type="button" id="topUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="topbar-avatar">
                            <i class="fa-solid fa-circle-user"></i>
                        </div>
                        <span class="d-none d-md-inline fw-semibold text-dark"><?= e($_SESSION['admin_name'] ?? 'Admin') ?></span>
                        <i class="fa-solid fa-chevron-down small text-muted ms-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="topUserDropdown">
                        <li>
                            <h6 class="dropdown-header small text-muted">
                                Signed in as <strong><?= e($_SESSION['admin_username'] ?? 'admin') ?></strong>
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="<?= $adminPath ?>profile.php">
                                <i class="fa-solid fa-user-gear me-2"></i> Profile & Password
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= $adminPath ?>health.php" target="_blank">
                                <i class="fa-solid fa-heart-pulse me-2"></i> System Diagnostics
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?= $adminPath ?>logout.php">
                                <i class="fa-solid fa-right-from-bracket me-2"></i> Log Out
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="<?= $adminPath ?>logout.php" class="topbar-btn text-danger ms-1" title="Log Out">
                    <i class="fa-solid fa-power-off"></i>
                </a>
            </div>
        </header>

        <!-- Sub-Header / Breadcrumb -->
        <div class="admin-page-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h2 class="page-title"><?= e($pageTitle) ?></h2>
                    <p class="page-subtitle">Welcome to SCMIPS Administration</p>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= $adminPath ?>dashboard.php">
                                <i class="fa-solid fa-house me-1"></i> SCMIPS
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><?= e($pageTitle) ?></li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Main Body Content -->
        <main class="admin-content-body">
            <?php if (!empty($flashes)): ?>
                <div class="row mb-3">
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

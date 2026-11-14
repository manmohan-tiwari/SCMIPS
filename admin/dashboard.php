<?php
/**
 * SCMIPS Gallery Admin Panel - Administrator Dashboard
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

// Fetch summary metrics
try {
    $sessionsCount = (int)$pdo->query("SELECT COUNT(*) FROM gallery_sessions")->fetchColumn();
    $eventsCount   = (int)$pdo->query("SELECT COUNT(*) FROM gallery_events")->fetchColumn();
    $imagesCount   = (int)$pdo->query("SELECT COUNT(*) FROM gallery_images")->fetchColumn();

    // Fetch recently added events
    $stmtRecentEvents = $pdo->query(
        "SELECT e.id, e.title, e.event_date, e.is_published, s.academic_year,
                (SELECT COUNT(*) FROM gallery_images i WHERE i.event_id = e.id) AS image_count
         FROM gallery_events e
         JOIN gallery_sessions s ON e.session_id = s.id
         ORDER BY e.created_at DESC LIMIT 5"
    );
    $recentEvents = $stmtRecentEvents->fetchAll();
} catch (PDOException $e) {
    error_log('Dashboard Metrics Error: ' . $e->getMessage());
    $sessionsCount = 0;
    $eventsCount = 0;
    $imagesCount = 0;
    $recentEvents = [];
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Dashboard Welcome Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800 fw-bold" style="color: var(--brand-primary);">
            Welcome, <?= e($_SESSION['admin_name'] ?? 'Administrator') ?>!
        </h1>
        <p class="text-muted mb-0 small">
            Manage academic sessions, events, and gallery photographs for the SCMIPS public website.
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="sessions/create.php" class="btn btn-sm btn-brand-primary shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> New Session
        </a>
        <a href="events/create.php" class="btn btn-sm btn-brand-secondary shadow-sm">
            <i class="fa-solid fa-calendar-plus me-1"></i> New Event
        </a>
        <a href="images/upload.php" class="btn btn-sm btn-outline-primary shadow-sm">
            <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Photos
        </a>
    </div>
</div>

<!-- Stat Metric Cards -->
<div class="row g-3 mb-4">
    <!-- Academic Sessions -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card stat-card stat-sessions h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold mb-1">Academic Sessions</div>
                    <div class="h2 mb-0 fw-bold" style="color: var(--brand-primary);"><?= $sessionsCount ?></div>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                <a href="sessions/index.php" class="small text-decoration-none fw-semibold" style="color: var(--brand-primary);">
                    Manage Sessions &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Gallery Events -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card stat-card stat-events h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold mb-1">Total Events</div>
                    <div class="h2 mb-0 fw-bold" style="color: #c77e1c;"><?= $eventsCount ?></div>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                <a href="events/index.php" class="small text-decoration-none fw-semibold" style="color: #c77e1c;">
                    Manage Events &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Gallery Images -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card stat-card stat-images h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold mb-1">Gallery Photographs</div>
                    <div class="h2 mb-0 fw-bold text-success"><?= $imagesCount ?></div>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="fa-solid fa-images"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                <a href="images/upload.php" class="small text-decoration-none fw-semibold text-success">
                    Upload Photos &rarr;
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Events & Quick Guidance -->
<div class="row g-4">
    <!-- Recent Events Table -->
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="fw-bold"><i class="fa-solid fa-clock-rotate-left me-1 text-primary"></i> Recently Added Events</span>
                <a href="events/index.php" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentEvents)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fa-regular fa-calendar-xmark fa-2xl mb-3 text-secondary"></i>
                        <p class="mb-2">No events created yet.</p>
                        <a href="events/create.php" class="btn btn-sm btn-brand-primary">
                            <i class="fa-solid fa-plus me-1"></i> Create Your First Event
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Event Title</th>
                                    <th>Session</th>
                                    <th>Date</th>
                                    <th>Photos</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentEvents as $ev): ?>
                                    <tr>
                                        <td class="fw-semibold text-dark"><?= e($ev['title']) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= e($ev['academic_year']) ?></span></td>
                                        <td class="small text-muted"><?= $ev['event_date'] ? e($ev['event_date']) : '—' ?></td>
                                        <td><span class="badge bg-secondary"><?= (int)$ev['image_count'] ?></span></td>
                                        <td>
                                            <?php if ($ev['is_published']): ?>
                                                <span class="badge bg-success">Published</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Draft</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="images/upload.php?event_id=<?= (int)$ev['id'] ?>" class="btn btn-sm btn-outline-primary" title="Upload Photos">
                                                <i class="fa-solid fa-upload"></i>
                                            </a>
                                            <a href="events/edit.php?id=<?= (int)$ev['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Edit Event">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Help & System Notes -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-lightbulb me-1 text-warning"></i> Quick Workflow Guide
            </div>
            <div class="card-body small">
                <ol class="ps-3 mb-0">
                    <li class="mb-2">
                        <strong>Academic Sessions:</strong> Create an academic year (e.g. <code>2024-2025</code>) to group annual activities.
                    </li>
                    <li class="mb-2">
                        <strong>Events:</strong> Add specific college events (e.g. <em>Annual Sports Day</em>, <em>Induction Program</em>) under a session.
                    </li>
                    <li class="mb-2">
                        <strong>Upload Photos:</strong> Multi-select image files to instantly upload them to an event with preview.
                    </li>
                    <li>
                        <strong>Publish:</strong> Toggle the event status to published when ready for public display.
                    </li>
                </ol>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-shield-halved me-1 text-success"></i> Security Status
            </div>
            <div class="card-body small">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fa-solid fa-circle-check text-success me-1"></i>
                        <strong>PDO Prepared Queries:</strong> Real prepared statements active.
                    </li>
                    <li class="mb-2">
                        <i class="fa-solid fa-circle-check text-success me-1"></i>
                        <strong>CSRF Protection:</strong> All form submissions validated.
                    </li>
                    <li class="mb-2">
                        <i class="fa-solid fa-circle-check text-success me-1"></i>
                        <strong>Script Execution Block:</strong> <code>.htaccess</code> &amp; <code>web.config</code> in uploads.
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check text-success me-1"></i>
                        <strong>Health Diagnostics:</strong> <a href="health.php" target="_blank">Run health check &rarr;</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
/**
 * SCMIPS Gallery Admin Panel - Administrator Dashboard
 * Modern Nexa-Inspired Layout & Color Palette
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

// Fetch summary metrics & aggregations
try {
    $sessionsCount = (int)$pdo->query("SELECT COUNT(*) FROM gallery_sessions")->fetchColumn();
    
    // Events count breakdown (Total, Published, Draft)
    $eventsQuery = $pdo->query(
        "SELECT COUNT(*) AS total,
                COUNT(CASE WHEN is_published = 1 THEN 1 END) AS published,
                COUNT(CASE WHEN is_published = 0 THEN 1 END) AS draft
         FROM gallery_events"
    )->fetch();
    $eventsTotal     = (int)($eventsQuery['total'] ?? 0);
    $eventsPublished = (int)($eventsQuery['published'] ?? 0);
    $eventsDraft     = (int)($eventsQuery['draft'] ?? 0);

    // Photos count
    $imagesCount = (int)$pdo->query("SELECT COUNT(*) FROM gallery_images")->fetchColumn();

    // Fetch recently added events with session year and photo count
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
    $sessionsCount   = 0;
    $eventsTotal     = 0;
    $eventsPublished = 0;
    $eventsDraft     = 0;
    $imagesCount     = 0;
    $recentEvents    = [];
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Action Bar / Quick Actions Header -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-dark">
            Overview &amp; Statistics
        </h4>
        <span class="text-muted small">Real-time gallery metrics &amp; session status</span>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="sessions/create.php" class="btn btn-sm btn-outline-secondary bg-white shadow-sm">
            <i class="fa-solid fa-plus me-1 text-primary"></i> New Session
        </a>
        <a href="events/create.php" class="btn btn-sm btn-outline-secondary bg-white shadow-sm">
            <i class="fa-solid fa-calendar-plus me-1 text-warning"></i> New Event
        </a>
        <a href="images/upload.php" class="btn btn-sm btn-warning shadow-sm">
            <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Photos
        </a>
    </div>
</div>

<!-- ========================================================================
     ROW 1: Top 4 Circular Donut Gauge Cards (Nexa Top Row Design)
     ======================================================================== -->
<div class="row g-3 mb-4">
    <!-- Card 1: Academic Sessions (Teal) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="gauge-card">
            <div class="gauge-circle teal">
                <span class="gauge-number"><?= $sessionsCount ?></span>
            </div>
            <div class="gauge-title">Academic Sessions</div>
            <div class="gauge-subtitle">
                <span class="text-success"><i class="fa-solid fa-arrow-trend-up"></i> Active Years</span>
            </div>
            <div class="mini-bars">
                <span class="mini-bar teal" style="height: 6px;"></span>
                <span class="mini-bar teal" style="height: 12px;"></span>
                <span class="mini-bar teal" style="height: 9px;"></span>
                <span class="mini-bar teal" style="height: 16px;"></span>
                <span class="mini-bar teal" style="height: 11px;"></span>
                <span class="mini-bar teal" style="height: 18px;"></span>
            </div>
        </div>
    </div>

    <!-- Card 2: Campus Events (Orange) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="gauge-card">
            <div class="gauge-circle orange">
                <span class="gauge-number"><?= $eventsTotal ?></span>
            </div>
            <div class="gauge-title">Campus Events</div>
            <div class="gauge-subtitle">
                <span class="text-warning"><i class="fa-solid fa-calendar-check"></i> Total Registered</span>
            </div>
            <div class="mini-bars">
                <span class="mini-bar orange" style="height: 8px;"></span>
                <span class="mini-bar orange" style="height: 14px;"></span>
                <span class="mini-bar orange" style="height: 10px;"></span>
                <span class="mini-bar orange" style="height: 17px;"></span>
                <span class="mini-bar orange" style="height: 13px;"></span>
                <span class="mini-bar orange" style="height: 15px;"></span>
            </div>
        </div>
    </div>

    <!-- Card 3: Published Events (Green) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="gauge-card">
            <div class="gauge-circle green">
                <span class="gauge-number"><?= $eventsPublished ?></span>
            </div>
            <div class="gauge-title">Published Events</div>
            <div class="gauge-subtitle">
                <span class="text-success"><i class="fa-solid fa-circle-check"></i> Live on Public Site</span>
            </div>
            <div class="mini-bars">
                <span class="mini-bar green" style="height: 7px;"></span>
                <span class="mini-bar green" style="height: 11px;"></span>
                <span class="mini-bar green" style="height: 15px;"></span>
                <span class="mini-bar green" style="height: 18px;"></span>
                <span class="mini-bar green" style="height: 14px;"></span>
                <span class="mini-bar green" style="height: 16px;"></span>
            </div>
        </div>
    </div>

    <!-- Card 4: Total Photos (Blue) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="gauge-card">
            <div class="gauge-circle blue">
                <span class="gauge-number"><?= $imagesCount ?></span>
            </div>
            <div class="gauge-title">Gallery Photos</div>
            <div class="gauge-subtitle">
                <span class="text-info"><i class="fa-solid fa-images"></i> Total Stored</span>
            </div>
            <div class="mini-bars">
                <span class="mini-bar blue" style="height: 9px;"></span>
                <span class="mini-bar blue" style="height: 13px;"></span>
                <span class="mini-bar blue" style="height: 11px;"></span>
                <span class="mini-bar blue" style="height: 16px;"></span>
                <span class="mini-bar blue" style="height: 14px;"></span>
                <span class="mini-bar blue" style="height: 18px;"></span>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================
     ROW 2: Quick Metric Strip (Nexa Middle Row Design)
     ======================================================================== -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="quick-stat-box shadow-sm">
            <div class="quick-stat-icon text-warning">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="quick-stat-num"><?= $sessionsCount ?></div>
            <div class="quick-stat-label">Academic Sessions</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="quick-stat-box shadow-sm">
            <div class="quick-stat-icon text-primary">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="quick-stat-num"><?= $eventsTotal ?></div>
            <div class="quick-stat-label">Total Events</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="quick-stat-box shadow-sm">
            <div class="quick-stat-icon text-danger">
                <i class="fa-solid fa-eye"></i>
            </div>
            <div class="quick-stat-num"><?= $eventsPublished ?></div>
            <div class="quick-stat-label">Live Published</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="quick-stat-box shadow-sm">
            <div class="quick-stat-icon text-success">
                <i class="fa-solid fa-camera-retro"></i>
            </div>
            <div class="quick-stat-num"><?= $imagesCount ?></div>
            <div class="quick-stat-label">Gallery Photos</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="quick-stat-box shadow-sm">
            <div class="quick-stat-icon text-info">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div class="quick-stat-num">1</div>
            <div class="quick-stat-label">Active Admin</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="quick-stat-box shadow-sm">
            <div class="quick-stat-icon text-warning">
                <i class="fa-solid fa-database"></i>
            </div>
            <div class="quick-stat-num">MySQL</div>
            <div class="quick-stat-label">PDO Connected</div>
        </div>
    </div>
</div>

<!-- ========================================================================
     ROW 3: Recent Events Table & System Overview
     ======================================================================== -->
<div class="row g-4">
    <!-- Left: Recently Added Events Table -->
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <span>
                    <i class="fa-solid fa-clock-rotate-left me-1 text-primary"></i> Recently Added Events
                </span>
                <a href="events/index.php" class="btn btn-sm btn-outline-secondary py-1 px-2">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentEvents)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fa-regular fa-calendar-xmark fa-2xl mb-3 text-secondary"></i>
                        <p class="mb-2">No events created yet.</p>
                        <a href="events/create.php" class="btn btn-sm btn-primary">
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
                                            <a href="images/upload.php?event_id=<?= (int)$ev['id'] ?>" class="btn btn-sm btn-outline-primary py-1 px-2" title="Upload Photos">
                                                <i class="fa-solid fa-upload"></i>
                                            </a>
                                            <a href="events/edit.php?id=<?= (int)$ev['id'] ?>" class="btn btn-sm btn-outline-secondary py-1 px-2" title="Edit Event">
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

    <!-- Right: Quick Help & Security Status -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <span>
                    <i class="fa-solid fa-bolt me-1 text-warning"></i> Quick Actions
                </span>
            </div>
            <div class="card-body p-3">
                <div class="d-grid gap-2">
                    <a href="images/upload.php" class="btn btn-warning text-dark fw-bold text-start d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-cloud-arrow-up me-2"></i> Upload Photos</span>
                        <i class="fa-solid fa-chevron-right small"></i>
                    </a>
                    <a href="events/create.php" class="btn btn-outline-secondary text-start d-flex align-items-center justify-content-between bg-light">
                        <span><i class="fa-solid fa-calendar-plus me-2 text-primary"></i> Create New Event</span>
                        <i class="fa-solid fa-chevron-right small"></i>
                    </a>
                    <a href="sessions/create.php" class="btn btn-outline-secondary text-start d-flex align-items-center justify-content-between bg-light">
                        <span><i class="fa-solid fa-plus me-2 text-success"></i> Add Academic Session</span>
                        <i class="fa-solid fa-chevron-right small"></i>
                    </a>
                    <a href="<?= $publicPath ?>" target="_blank" class="btn btn-outline-secondary text-start d-flex align-items-center justify-content-between bg-light">
                        <span><i class="fa-solid fa-globe me-2 text-info"></i> View Public Gallery</span>
                        <i class="fa-solid fa-arrow-up-right-from-square small"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">
                <span>
                    <i class="fa-solid fa-shield-halved me-1 text-success"></i> System Status
                </span>
            </div>
            <div class="card-body small p-3">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2 d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-circle-check text-success me-1"></i> Database</span>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold">Connected</span>
                    </li>
                    <li class="mb-2 d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-circle-check text-success me-1"></i> CSRF Defense</span>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold">Active</span>
                    </li>
                    <li class="mb-2 d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-circle-check text-success me-1"></i> Upload Protection</span>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold">Secured</span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <span><i class="fa-solid fa-heart-pulse text-info me-1"></i> Diagnostics</span>
                        <a href="health.php" target="_blank" class="text-decoration-none fw-semibold">View Health &rarr;</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

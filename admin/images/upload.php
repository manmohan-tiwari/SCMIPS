<?php
/**
 * SCMIPS Gallery Admin Panel - Image Upload Management (Phase 9 Bridge)
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Upload Photographs';
$activePage = 'upload';

$eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

// Fetch events for dropdown
try {
    $events = $pdo->query(
        "SELECT e.id, e.title, s.academic_year 
         FROM gallery_events e 
         JOIN gallery_sessions s ON e.session_id = s.id 
         ORDER BY e.created_at DESC"
    )->fetchAll();

    $currentEvent = null;
    if ($eventId > 0) {
        $stmtEvent = $pdo->prepare(
            "SELECT e.*, s.session_name, s.academic_year 
             FROM gallery_events e 
             JOIN gallery_sessions s ON e.session_id = s.id 
             WHERE e.id = :id LIMIT 1"
        );
        $stmtEvent->execute([':id' => $eventId]);
        $currentEvent = $stmtEvent->fetch();
    }
} catch (PDOException $e) {
    error_log('Upload Bridge Error: ' . $e->getMessage());
    $events = [];
    $currentEvent = null;
}

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Breadcrumbs & Heading -->
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1 small">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../events/index.php">Events</a></li>
            <li class="breadcrumb-item active" aria-current="page">Upload Photos</li>
        </ol>
    </nav>
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: var(--brand-primary);">Upload Gallery Photos</h1>
            <p class="text-muted mb-0 small">
                <?php if ($currentEvent): ?>
                    Uploading photos for <strong><?= e($currentEvent['title']) ?></strong> (<?= e($currentEvent['academic_year']) ?>).
                <?php else: ?>
                    Select an event below to upload and organize photographs.
                <?php endif; ?>
            </p>
        </div>
        <div>
            <a href="../events/index.php" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Events
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-cloud-arrow-up me-1 text-primary"></i> Photo Upload Zone
            </div>
            <div class="card-body p-4 text-center">
                <?php if (empty($events)): ?>
                    <div class="py-5 text-muted">
                        <i class="fa-regular fa-calendar-xmark fa-3x mb-3 text-secondary"></i>
                        <h5>No Events Created Yet</h5>
                        <p class="small text-muted mb-3">You need an active event before you can upload photos.</p>
                        <a href="../events/create.php" class="btn btn-brand-primary">
                            <i class="fa-solid fa-plus me-1"></i> Create An Event
                        </a>
                    </div>
                <?php else: ?>
                    <div class="mb-4 text-start">
                        <label for="select_event" class="form-label fw-semibold">Target Event</label>
                        <select class="form-select" id="select_event" onchange="window.location.href='upload.php?event_id=' + this.value">
                            <option value="0">-- Select an Event --</option>
                            <?php foreach ($events as $ev): ?>
                                <option value="<?= (int)$ev['id'] ?>" <?= ($eventId === (int)$ev['id']) ? 'selected' : '' ?>>
                                    <?= e($ev['title']) ?> (<?= e($ev['academic_year']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($currentEvent): ?>
                        <div class="border border-2 border-dashed rounded-4 p-5 bg-light mb-3">
                            <i class="fa-solid fa-images fa-3x mb-3 text-primary opacity-50"></i>
                            <h5 class="fw-bold">Ready for Phase 9 Multi-Upload Pipeline</h5>
                            <p class="text-muted small mb-0">
                                Event <strong><?= e($currentEvent['title']) ?></strong> is active and connected to the database.
                            </p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Guidance Side Card -->
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-shield-halved me-1 text-success"></i> Upload Standards
            </div>
            <div class="card-body small text-muted">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-1"></i> <strong>Formats:</strong> JPG, PNG, WebP</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-1"></i> <strong>Max Size:</strong> 5 MB per photograph</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-1"></i> <strong>Validation:</strong> Binary magic bytes inspection (`finfo`)</li>
                    <li><i class="fa-solid fa-check text-success me-1"></i> <strong>Storage:</strong> Script execution disabled via `.htaccess` &amp; `web.config`</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

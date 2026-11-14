<?php
/**
 * SCMIPS Gallery Admin Panel - Events Listing (Phase 8)
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Gallery Events';
$activePage = 'events';

$selectedSessionId = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;

// Fetch all sessions for the filter dropdown
try {
    $sessions = $pdo->query(
        "SELECT id, session_name, academic_year 
         FROM gallery_sessions 
         ORDER BY sort_order ASC, academic_year DESC"
    )->fetchAll();
} catch (PDOException $e) {
    error_log('Events Filter Error: ' . $e->getMessage());
    $sessions = [];
}

// Fetch events (with optional session filter)
try {
    $sql = "SELECT e.id, e.session_id, e.title, e.slug, e.description, e.event_date, e.is_published, e.created_at,
                   s.session_name, s.academic_year,
                   (SELECT COUNT(*) FROM gallery_images i WHERE i.event_id = e.id) AS image_count,
                   (SELECT file_path FROM gallery_images i WHERE i.event_id = e.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS sample_image
            FROM gallery_events e
            JOIN gallery_sessions s ON e.session_id = s.id";

    $params = [];
    if ($selectedSessionId > 0) {
        $sql .= " WHERE e.session_id = :session_id";
        $params[':session_id'] = $selectedSessionId;
    }

    $sql .= " ORDER BY e.event_date DESC, e.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Events Query Error: ' . $e->getMessage());
    $events = [];
    set_flash('danger', 'Unable to retrieve gallery events.');
}

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Page Header & Action Controls -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1 fw-bold" style="color: var(--brand-primary);">Gallery Events</h1>
        <p class="text-muted mb-0 small">
            Manage specific events, functions, and photographic albums within academic sessions.
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="create.php<?= $selectedSessionId > 0 ? '?session_id=' . $selectedSessionId : '' ?>" class="btn btn-brand-primary shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Add Event
        </a>
    </div>
</div>

<!-- Filter Bar & Table Card -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <form method="GET" action="index.php" class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="session_id" class="col-form-label fw-semibold small text-muted">
                    <i class="fa-solid fa-filter me-1"></i> Filter by Session:
                </label>
            </div>
            <div class="col-auto">
                <select name="session_id" id="session_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="0">All Academic Sessions</option>
                    <?php foreach ($sessions as $s): ?>
                        <option value="<?= (int)$s['id'] ?>" <?= ($selectedSessionId === (int)$s['id']) ? 'selected' : '' ?>>
                            <?= e($s['session_name']) ?> (<?= e($s['academic_year']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($selectedSessionId > 0): ?>
                <div class="col-auto">
                    <a href="index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-xmark me-1"></i> Clear Filter
                    </a>
                </div>
            <?php endif; ?>
            <div class="col text-end small text-muted">
                Showing <strong><?= count($events) ?></strong> event<?= count($events) === 1 ? '' : 's' ?>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <?php if (empty($events)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fa-regular fa-calendar-xmark fa-3x mb-3 text-secondary"></i>
                <h5 class="fw-bold">No Events Found</h5>
                <p class="small text-muted mb-3">
                    <?= $selectedSessionId > 0 ? 'No events found in this session.' : 'Get started by creating your first event.' ?>
                </p>
                <a href="create.php<?= $selectedSessionId > 0 ? '?session_id=' . $selectedSessionId : '' ?>" class="btn btn-brand-primary">
                    <i class="fa-solid fa-plus me-1"></i> Add New Event
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Academic Session</th>
                            <th>Event Date</th>
                            <th class="text-center">Photos</th>
                            <th class="text-center">Status</th>
                            <th>Created On</th>
                            <th class="text-end" style="width: 220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= e($event['title']) ?></div>
                                    <?php if (!empty($event['description'])): ?>
                                        <div class="text-muted small text-truncate" style="max-width: 320px;">
                                            <?= e($event['description']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <?= e($event['academic_year']) ?>
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    <?= !empty($event['event_date']) ? date('d M Y', strtotime($event['event_date'])) : '—' ?>
                                </td>
                                <td class="text-center">
                                    <a href="../images/upload.php?event_id=<?= (int)$event['id'] ?>" class="badge bg-secondary text-decoration-none" title="Manage photos">
                                        <i class="fa-solid fa-image me-1"></i> <?= (int)$event['image_count'] ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <?php if ($event['is_published']): ?>
                                        <span class="badge bg-success">Published</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted">
                                    <?= date('d M Y', strtotime($event['created_at'])) ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="../images/upload.php?event_id=<?= (int)$event['id'] ?>" class="btn btn-outline-primary" title="Upload & View Photos">
                                            <i class="fa-solid fa-cloud-arrow-up"></i> Photos
                                        </a>

                                        <a href="edit.php?id=<?= (int)$event['id'] ?>" class="btn btn-outline-secondary" title="Edit Event Details">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </a>

                                        <form method="POST" action="delete.php" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete event &quot;<?= e($event['title']) ?>&quot;? All associated photographs will also be deleted from disk.');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int)$event['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger" title="Delete Event">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

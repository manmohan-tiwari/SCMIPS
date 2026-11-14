<?php
/**
 * SCMIPS Gallery Admin Panel - Academic Sessions Listing (Phase 7)
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Academic Sessions';
$activePage = 'sessions';

// Fetch all sessions with their associated events count
try {
    $stmt = $pdo->query(
        "SELECT s.id, s.session_name, s.academic_year, s.is_active, s.sort_order, s.created_at,
                (SELECT COUNT(*) FROM gallery_events e WHERE e.session_id = s.id) AS event_count
         FROM gallery_sessions s
         ORDER BY s.sort_order ASC, s.academic_year DESC"
    );
    $sessions = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Sessions Listing Error: ' . $e->getMessage());
    $sessions = [];
    set_flash('danger', 'Unable to retrieve academic sessions from the database.');
}

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Action Controls -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <p class="text-muted mb-0 small">
            Organize gallery photographs into academic years (e.g. 2024–2025, 2023–2024).
        </p>
    </div>
    <div>
        <a href="create.php" class="btn btn-warning shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Add Academic Session
        </a>
    </div>
</div>

<!-- Sessions Table Card -->
<div class="card shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span class="fw-bold">
            <i class="fa-solid fa-calendar-days me-1 text-primary"></i> All Academic Sessions (<?= count($sessions) ?>)
        </span>
    </div>

    <div class="card-body p-0">
        <?php if (empty($sessions)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fa-regular fa-calendar-xmark fa-3x mb-3 text-secondary"></i>
                <h5 class="fw-bold">No Academic Sessions Found</h5>
                <p class="small text-muted mb-3">Get started by creating your first academic session.</p>
                <a href="create.php" class="btn btn-brand-primary">
                    <i class="fa-solid fa-plus me-1"></i> Add Academic Session
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;" class="text-center">Order</th>
                            <th>Session Name</th>
                            <th>Academic Year</th>
                            <th class="text-center">Events</th>
                            <th class="text-center">Status</th>
                            <th>Created On</th>
                            <th class="text-end" style="width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $session): ?>
                            <tr>
                                <td class="text-center text-muted fw-bold">
                                    <?= (int)$session['sort_order'] ?>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark"><?= e($session['session_name']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <?= e($session['academic_year']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="../events/index.php?session_id=<?= (int)$session['id'] ?>" class="badge bg-secondary text-decoration-none" title="View events in this session">
                                        <?= (int)$session['event_count'] ?> event<?= (int)$session['event_count'] === 1 ? '' : 's' ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <?php if ($session['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted">
                                    <?= date('d M Y', strtotime($session['created_at'])) ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="edit.php?id=<?= (int)$session['id'] ?>" class="btn btn-outline-secondary" title="Edit Session">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </a>

                                        <form method="POST" action="delete.php" class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete session <?= e($session['academic_year']) ?>?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int)$session['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger" title="Delete Session"
                                                    <?= ((int)$session['event_count'] > 0) ? 'title="Contains events - cannot delete directly"' : '' ?>>
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

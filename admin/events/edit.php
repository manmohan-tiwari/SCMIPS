<?php
/**
 * SCMIPS Gallery Admin Panel - Edit Gallery Event (Phase 8)
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Edit Gallery Event';
$activePage = 'events';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', 'Invalid event ID provided.');
    redirect('index.php');
}

// Fetch event data
try {
    $stmt = $pdo->prepare(
        'SELECT e.*, s.session_name, s.academic_year,
                (SELECT COUNT(*) FROM gallery_images i WHERE i.event_id = e.id) AS image_count
         FROM gallery_events e
         JOIN gallery_sessions s ON e.session_id = s.id
         WHERE e.id = :id LIMIT 1'
    );
    $stmt->execute([':id' => $id]);
    $event = $stmt->fetch();

    if (!$event) {
        set_flash('danger', 'Gallery event not found.');
        redirect('index.php');
    }
} catch (PDOException $e) {
    error_log('Event Fetch Error: ' . $e->getMessage());
    set_flash('danger', 'Database error retrieving event details.');
    redirect('index.php');
}

// Fetch all sessions for dropdown
try {
    $sessions = $pdo->query(
        "SELECT id, session_name, academic_year 
         FROM gallery_sessions 
         ORDER BY sort_order ASC, academic_year DESC"
    )->fetchAll();
} catch (PDOException $e) {
    error_log('Sessions Fetch Error: ' . $e->getMessage());
    $sessions = [];
}

$error = '';
$sessionId   = (int)$event['session_id'];
$title       = $event['title'];
$eventDate   = $event['event_date'] ?? '';
$description = $event['description'] ?? '';
$isPublished = (int)$event['is_published'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $error = 'Security session expired. Please try submitting again.';
    } else {
        $sessionId   = (int)($_POST['session_id'] ?? 0);
        $title       = trim($_POST['title'] ?? '');
        $eventDate   = !empty($_POST['event_date']) ? trim($_POST['event_date']) : null;
        $description = trim($_POST['description'] ?? '');
        $isPublished = isset($_POST['is_published']) ? 1 : 0;

        if ($sessionId <= 0) {
            $error = 'Please select a valid academic session.';
        } elseif (empty($title)) {
            $error = 'Please enter an event title.';
        } else {
            try {
                // If title changed, recalculate slug
                $slug = $event['slug'];
                if ($title !== $event['title']) {
                    $baseSlug = slugify($title);
                    $slug = $baseSlug;

                    $stmtSlug = $pdo->prepare('SELECT COUNT(*) FROM gallery_events WHERE session_id = :session_id AND slug = :slug AND id != :id');
                    $stmtSlug->execute([':session_id' => $sessionId, ':slug' => $slug, ':id' => $id]);
                    if ($stmtSlug->fetchColumn() > 0) {
                        $slug = $baseSlug . '-' . time();
                    }
                }

                $stmtUpdate = $pdo->prepare(
                    'UPDATE gallery_events 
                     SET session_id = :session_id,
                         title = :title,
                         slug = :slug,
                         description = :description,
                         event_date = :event_date,
                         is_published = :is_published
                     WHERE id = :id'
                );
                $stmtUpdate->execute([
                    ':session_id'   => $sessionId,
                    ':title'        => $title,
                    ':slug'         => $slug,
                    ':description'  => $description ?: null,
                    ':event_date'   => $eventDate ?: null,
                    ':is_published' => $isPublished,
                    ':id'           => $id
                ]);

                set_flash('success', "Event '{$title}' updated successfully!");
                redirect("index.php?session_id={$sessionId}");

            } catch (PDOException $e) {
                error_log('Event Update Error: ' . $e->getMessage());
                $error = 'Database error while saving changes. Please try again.';
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Breadcrumbs & Heading -->
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1 small">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="index.php">Events</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Event</li>
        </ol>
    </nav>
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 fw-bold mb-0" style="color: var(--brand-primary);">Edit Gallery Event</h1>
        <a href="../images/upload.php?event_id=<?= $id ?>" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-cloud-arrow-up me-1"></i> Manage Photos (<?= (int)$event['image_count'] ?>)
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-pen-to-square me-1 text-primary"></i> Edit Event: <?= e($event['title']) ?>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= e($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="edit.php?id=<?= $id ?>" novalidate>
                    <?= csrf_field() ?>

                    <!-- Academic Session Dropdown -->
                    <div class="mb-3">
                        <label for="session_id" class="form-label fw-semibold">
                            Academic Session <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="session_id" name="session_id" required autofocus>
                            <?php foreach ($sessions as $s): ?>
                                <option value="<?= (int)$s['id'] ?>" <?= ($sessionId === (int)$s['id']) ? 'selected' : '' ?>>
                                    <?= e($s['session_name']) ?> (<?= e($s['academic_year']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Event Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">
                            Event Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?= e($title) ?>" required>
                    </div>

                    <!-- Event Date -->
                    <div class="mb-3">
                        <label for="event_date" class="form-label fw-semibold">Event Date</label>
                        <input type="date" class="form-control" id="event_date" name="event_date" 
                               value="<?= e($eventDate) ?>">
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description / Notes</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= e($description) ?></textarea>
                    </div>

                    <!-- Publication Status -->
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" 
                                   value="1" <?= $isPublished ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="is_published">
                                Published (Visible on Public Website)
                            </label>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-brand-primary px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Update Event
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Metadata Side Card -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-info-circle me-1 text-primary"></i> Event Details
            </div>
            <div class="card-body small text-muted">
                <p><strong>URL Slug:</strong> <code><?= e($event['slug']) ?></code></p>
                <p><strong>Photos Uploaded:</strong> <span class="badge bg-secondary"><?= (int)$event['image_count'] ?> images</span></p>
                <p><strong>Created:</strong> <?= date('d M Y, h:i A', strtotime($event['created_at'])) ?></p>
                <p><strong>Last Updated:</strong> <?= date('d M Y, h:i A', strtotime($event['updated_at'])) ?></p>
                <hr>
                <a href="../images/upload.php?event_id=<?= $id ?>" class="btn btn-sm btn-outline-primary w-100">
                    <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload More Photos
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

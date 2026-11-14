<?php
/**
 * SCMIPS Gallery Admin Panel - Create Gallery Event (Phase 8)
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Add Gallery Event';
$activePage = 'events';

// Fetch sessions for dropdown
try {
    $sessions = $pdo->query(
        "SELECT id, session_name, academic_year 
         FROM gallery_sessions 
         ORDER BY sort_order ASC, academic_year DESC"
    )->fetchAll();
} catch (PDOException $e) {
    error_log('Event Create Session Load Error: ' . $e->getMessage());
    $sessions = [];
}

$error = '';
$sessionId = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;
$title = '';
$eventDate = date('Y-m-d');
$description = '';
$isPublished = 1;

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
            $error = 'Please enter an event title (e.g. "Annual Function", "Blood Donation Camp").';
        } else {
            try {
                // Verify session exists
                $stmtSessionCheck = $pdo->prepare('SELECT COUNT(*) FROM gallery_sessions WHERE id = :id');
                $stmtSessionCheck->execute([':id' => $sessionId]);
                if ($stmtSessionCheck->fetchColumn() == 0) {
                    $error = 'The selected academic session does not exist.';
                } else {
                    // Generate unique slug
                    $baseSlug = slugify($title);
                    $slug = $baseSlug;

                    $stmtSlug = $pdo->prepare('SELECT COUNT(*) FROM gallery_events WHERE session_id = :session_id AND slug = :slug');
                    $stmtSlug->execute([':session_id' => $sessionId, ':slug' => $slug]);
                    if ($stmtSlug->fetchColumn() > 0) {
                        $slug = $baseSlug . '-' . time();
                    }

                    // Insert event
                    $stmtInsert = $pdo->prepare(
                        'INSERT INTO gallery_events (session_id, title, slug, description, event_date, is_published) 
                         VALUES (:session_id, :title, :slug, :description, :event_date, :is_published)'
                    );
                    $stmtInsert->execute([
                        ':session_id'   => $sessionId,
                        ':title'        => $title,
                        ':slug'         => $slug,
                        ':description'  => $description ?: null,
                        ':event_date'   => $eventDate ?: null,
                        ':is_published' => $isPublished
                    ]);

                    $newEventId = (int)$pdo->lastInsertId();

                    set_flash('success', "Event '{$title}' created successfully! You can now upload photographs.");
                    
                    // Redirect to photo upload page for immediate workflow convenience
                    redirect("../images/upload.php?event_id={$newEventId}");
                }
            } catch (PDOException $e) {
                error_log('Event Create Error: ' . $e->getMessage());
                $error = 'Database error while saving event. Please try again.';
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
            <li class="breadcrumb-item active" aria-current="page">Add Event</li>
        </ol>
    </nav>
    <h1 class="h3 fw-bold" style="color: var(--brand-primary);">Add Gallery Event</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-calendar-plus me-1 text-primary"></i> Event Information
            </div>
            <div class="card-body p-4">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= e($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($sessions)): ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        No academic sessions exist yet. Please <a href="../sessions/create.php" class="alert-link">create an academic session</a> first.
                    </div>
                <?php else: ?>
                    <form method="POST" action="create.php" novalidate>
                        <?= csrf_field() ?>

                        <!-- Academic Session Dropdown -->
                        <div class="mb-3">
                            <label for="session_id" class="form-label fw-semibold">
                                Academic Session <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="session_id" name="session_id" required autofocus>
                                <option value="">-- Choose Academic Session --</option>
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
                                   placeholder="e.g. Annual Function, Career Guidance, Sports Meet" 
                                   value="<?= e($title) ?>" required>
                        </div>

                        <!-- Event Date -->
                        <div class="mb-3">
                            <label for="event_date" class="form-label fw-semibold">Event Date</label>
                            <input type="date" class="form-control" id="event_date" name="event_date" 
                                   value="<?= e($eventDate) ?>">
                            <div class="form-text">Optional. Used to chronologically order events on the public site.</div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description / Notes</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Brief summary of activities, chief guests, or highlights..."><?= e($description) ?></textarea>
                        </div>

                        <!-- Publication Status -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" 
                                       value="1" <?= $isPublished ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="is_published">
                                    Publish Immediately (Visible on Public Website)
                                </label>
                            </div>
                            <div class="form-text">If unchecked, this event will remain a draft visible only to administrators.</div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-brand-primary px-4">
                                <i class="fa-solid fa-arrow-right me-1"></i> Save &amp; Upload Photos
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Contextual Guidance Card -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-lightbulb me-1 text-warning"></i> Event Workflow Tip
            </div>
            <div class="card-body small text-muted">
                <p>
                    Every event belongs to an <strong>Academic Session</strong>.
                </p>
                <p>
                    Once you save this event, you will be taken immediately to the <strong>Photo Upload Zone</strong> to drag-and-drop or multi-select images.
                </p>
                <p class="mb-0">
                    Draft events allow you to upload and preview images before making the album visible to students and parents on the public website.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

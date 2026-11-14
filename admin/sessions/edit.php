<?php
/**
 * SCMIPS Gallery Admin Panel - Edit Academic Session (Phase 7)
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Edit Academic Session';
$activePage = 'sessions';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', 'Invalid session ID provided.');
    redirect('index.php');
}

// Fetch session to edit
try {
    $stmt = $pdo->prepare('SELECT * FROM gallery_sessions WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $session = $stmt->fetch();

    if (!$session) {
        set_flash('danger', 'Academic session not found.');
        redirect('index.php');
    }
} catch (PDOException $e) {
    error_log('Session Fetch Error: ' . $e->getMessage());
    set_flash('danger', 'Database error retrieving session.');
    redirect('index.php');
}

$error = '';
$sessionName  = $session['session_name'];
$academicYear = $session['academic_year'];
$sortOrder    = (int)$session['sort_order'];
$isActive     = (int)$session['is_active'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $error = 'Security session expired. Please try submitting again.';
    } else {
        $sessionName  = trim($_POST['session_name'] ?? '');
        $academicYear = trim($_POST['academic_year'] ?? '');
        $sortOrder    = (int)($_POST['sort_order'] ?? 0);
        $isActive     = isset($_POST['is_active']) ? 1 : 0;

        if (empty($sessionName)) {
            $error = 'Please enter a session display title.';
        } elseif (empty($academicYear)) {
            $error = 'Please enter the academic year.';
        } elseif (!preg_match('/^\d{4}-\d{4}$/', $academicYear)) {
            $error = 'Academic year must be in YYYY-YYYY format (e.g. "2024-2025").';
        } else {
            try {
                // Check if academic_year is already in use by another session
                $stmtCheck = $pdo->prepare('SELECT COUNT(*) FROM gallery_sessions WHERE academic_year = :academic_year AND id != :id');
                $stmtCheck->execute([
                    ':academic_year' => $academicYear,
                    ':id'            => $id
                ]);

                if ($stmtCheck->fetchColumn() > 0) {
                    $error = "Academic year '{$academicYear}' is already assigned to another session.";
                } else {
                    $stmtUpdate = $pdo->prepare(
                        'UPDATE gallery_sessions 
                         SET session_name = :session_name,
                             academic_year = :academic_year,
                             is_active = :is_active,
                             sort_order = :sort_order
                         WHERE id = :id'
                    );
                    $stmtUpdate->execute([
                        ':session_name'  => $sessionName,
                        ':academic_year' => $academicYear,
                        ':is_active'     => $isActive,
                        ':sort_order'    => $sortOrder,
                        ':id'            => $id
                    ]);

                    set_flash('success', "Session '{$sessionName}' updated successfully!");
                    redirect('index.php');
                }
            } catch (PDOException $e) {
                error_log('Session Update Error: ' . $e->getMessage());
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
            <li class="breadcrumb-item"><a href="index.php">Sessions</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Session</li>
        </ol>
    </nav>
    <h1 class="h3 fw-bold" style="color: var(--brand-primary);">Edit Academic Session</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-pen-to-square me-1 text-primary"></i> Edit Session: <?= e($session['session_name']) ?>
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

                    <!-- Session Display Title -->
                    <div class="mb-3">
                        <label for="session_name" class="form-label fw-semibold">
                            Session Display Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="session_name" name="session_name" 
                               value="<?= e($sessionName) ?>" required autofocus>
                    </div>

                    <!-- Academic Year Identifier -->
                    <div class="mb-3">
                        <label for="academic_year" class="form-label fw-semibold">
                            Academic Year (YYYY-YYYY) <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="academic_year" name="academic_year" 
                               value="<?= e($academicYear) ?>" pattern="\d{4}-\d{4}" required>
                    </div>

                    <!-- Sort Order & Active Status Row -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="sort_order" class="form-label fw-semibold">Sort Order</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                   value="<?= (int)$sortOrder ?>" min="0" step="1">
                        </div>

                        <div class="col-md-6 d-flex align-items-center pt-md-3">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" 
                                       value="1" <?= $isActive ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    Active / Visible on Public Site
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-brand-primary px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Update Session
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Metadata Side Card -->
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-info-circle me-1 text-primary"></i> Session Info
            </div>
            <div class="card-body small text-muted">
                <p><strong>Created:</strong> <?= date('d M Y, h:i A', strtotime($session['created_at'])) ?></p>
                <p><strong>Last Updated:</strong> <?= date('d M Y, h:i A', strtotime($session['updated_at'])) ?></p>
                <p class="mb-0">
                    <a href="../events/index.php?session_id=<?= $id ?>" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fa-solid fa-calendar-check me-1"></i> Manage Events in this Session
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

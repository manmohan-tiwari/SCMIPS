<?php
/**
 * SCMIPS Gallery Admin Panel - Create Academic Session (Phase 7)
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Add Academic Session';
$activePage = 'sessions';

$error = '';
$sessionName = '';
$academicYear = '';
$sortOrder = 0;
$isActive = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $error = 'Security session expired. Please try submitting again.';
    } else {
        $sessionName  = trim($_POST['session_name'] ?? '');
        $academicYear = trim($_POST['academic_year'] ?? '');
        $sortOrder    = (int)($_POST['sort_order'] ?? 0);
        $isActive     = isset($_POST['is_active']) ? 1 : 0;

        if (empty($sessionName)) {
            $error = 'Please enter a session title (e.g. "Session 2024-2025").';
        } elseif (empty($academicYear)) {
            $error = 'Please enter the academic year (e.g. "2024-2025").';
        } elseif (!preg_match('/^\d{4}-\d{4}$/', $academicYear)) {
            $error = 'Academic year must be in YYYY-YYYY format (e.g. "2024-2025").';
        } else {
            try {
                // Check for duplicate academic year
                $stmtCheck = $pdo->prepare('SELECT COUNT(*) FROM gallery_sessions WHERE academic_year = :academic_year');
                $stmtCheck->execute([':academic_year' => $academicYear]);
                if ($stmtCheck->fetchColumn() > 0) {
                    $error = "Academic year '{$academicYear}' already exists. Please choose a unique year.";
                } else {
                    $stmtInsert = $pdo->prepare(
                        'INSERT INTO gallery_sessions (session_name, academic_year, is_active, sort_order) 
                         VALUES (:session_name, :academic_year, :is_active, :sort_order)'
                    );
                    $stmtInsert->execute([
                        ':session_name'  => $sessionName,
                        ':academic_year' => $academicYear,
                        ':is_active'     => $isActive,
                        ':sort_order'    => $sortOrder
                    ]);

                    set_flash('success', "Academic session '{$sessionName}' created successfully!");
                    redirect('index.php');
                }
            } catch (PDOException $e) {
                error_log('Session Create Error: ' . $e->getMessage());
                $error = 'Database error while saving session. Please try again.';
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
            <li class="breadcrumb-item active" aria-current="page">Add Session</li>
        </ol>
    </nav>
    <h1 class="h3 fw-bold" style="color: var(--brand-primary);">Add Academic Session</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-calendar-plus me-1 text-primary"></i> Session Details
            </div>
            <div class="card-body p-4">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= e($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="create.php" novalidate>
                    <?= csrf_field() ?>

                    <!-- Session Display Title -->
                    <div class="mb-3">
                        <label for="session_name" class="form-label fw-semibold">
                            Session Display Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="session_name" name="session_name" 
                               placeholder="e.g. Session 2024-2025" value="<?= e($sessionName) ?>" required autofocus>
                        <div class="form-text">This title appears on public gallery cards.</div>
                    </div>

                    <!-- Academic Year Identifier -->
                    <div class="mb-3">
                        <label for="academic_year" class="form-label fw-semibold">
                            Academic Year (YYYY-YYYY) <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="academic_year" name="academic_year" 
                               placeholder="e.g. 2024-2025" value="<?= e($academicYear) ?>" pattern="\d{4}-\d{4}" required>
                        <div class="form-text">Must be 4 digits, hyphen, 4 digits (e.g. <code>2024-2025</code>). Used for unique identification.</div>
                    </div>

                    <!-- Sort Order & Active Status Row -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="sort_order" class="form-label fw-semibold">Sort Order</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                   value="<?= (int)$sortOrder ?>" min="0" step="1">
                            <div class="form-text">Lower numbers appear first (0 = default).</div>
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
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save Session
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Contextual Guidance Card -->
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-circle-question me-1 text-primary"></i> Academic Year Guide
            </div>
            <div class="card-body small text-muted">
                <p>
                    Academic sessions are annual groupings for college gallery items.
                </p>
                <p>
                    <strong>Examples:</strong><br>
                    &bull; Display: <code>Session 2024-2025</code><br>
                    &bull; Year: <code>2024-2025</code>
                </p>
                <p class="mb-0">
                    Once a session is created, you can attach specific events (like <em>Annual Function</em> or <em>Sports Day</em>) to it and upload high-resolution event photographs.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

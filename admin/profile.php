<?php
/**
 * SCMIPS Gallery Admin Panel - Administrator Profile & Password Change
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Admin Profile & Security';
$activePage = 'profile';

$adminId = (int)$_SESSION['admin_id'];

// Fetch current admin account data
$stmt = $pdo->prepare('SELECT id, username, full_name, email, created_at FROM admins WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $adminId]);
$admin = $stmt->fetch();

if (!$admin) {
    set_flash('danger', 'Administrator profile not found.');
    redirect('logout.php');
}

$errors = [];
$successMessage = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Security token invalid or expired. Please reload and try again.';
    } else {
        $action = trim($_POST['action'] ?? '');

        // 1. Update Profile Details (Full Name, Email)
        if ($action === 'update_profile') {
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');

            if (empty($fullName)) {
                $errors[] = 'Full Name is required.';
            }

            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please enter a valid email address.';
            }

            if (empty($errors)) {
                try {
                    $stmtUpdate = $pdo->prepare(
                        'UPDATE admins SET full_name = :full_name, email = :email, updated_at = NOW() WHERE id = :id'
                    );
                    $stmtUpdate->execute([
                        ':full_name' => $fullName,
                        ':email'     => !empty($email) ? $email : null,
                        ':id'        => $adminId
                    ]);

                    $_SESSION['admin_name'] = $fullName;
                    set_flash('success', 'Profile details updated successfully!');
                    redirect('profile.php');
                } catch (PDOException $e) {
                    error_log('Profile Update Error: ' . $e->getMessage());
                    $errors[] = 'Database error updating profile.';
                }
            }
        }

        // 2. Change Password
        if ($action === 'change_password') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($currentPassword)) {
                $errors[] = 'Please enter your current password.';
            }

            if (empty($newPassword) || strlen($newPassword) < 6) {
                $errors[] = 'New password must be at least 6 characters long.';
            }

            if ($newPassword !== $confirmPassword) {
                $errors[] = 'New password and confirmation password do not match.';
            }

            if (empty($errors)) {
                // Verify current password against database
                $stmtCheck = $pdo->prepare('SELECT password_hash FROM admins WHERE id = :id LIMIT 1');
                $stmtCheck->execute([':id' => $adminId]);
                $currentHash = $stmtCheck->fetchColumn();

                if (!password_verify($currentPassword, $currentHash)) {
                    $errors[] = 'The current password you entered is incorrect.';
                } else {
                    try {
                        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                        $stmtPass = $pdo->prepare('UPDATE admins SET password_hash = :hash, updated_at = NOW() WHERE id = :id');
                        $stmtPass->execute([
                            ':hash' => $newHash,
                            ':id'   => $adminId
                        ]);

                        // Regenerate session ID for extra security
                        session_regenerate_id(true);

                        set_flash('success', 'Password changed successfully! Keep your new password in a safe place.');
                        redirect('profile.php');
                    } catch (PDOException $e) {
                        error_log('Password Update Error: ' . $e->getMessage());
                        $errors[] = 'Database error updating password.';
                    }
                }
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="mb-4">
    <p class="text-muted mb-0 small">Update your administrative profile details and change your account password.</p>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <h6 class="alert-heading fw-bold mb-1">
            <i class="fa-solid fa-triangle-exclamation me-1"></i> Please correct the following errors:
        </h6>
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Profile Information Card -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="fa-solid fa-id-card text-primary me-2 fa-lg"></i>
                <h5 class="card-title mb-0 fw-bold">Profile Details</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="profile.php">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="update_profile">

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Username</label>
                        <input type="text" class="form-control bg-light" value="<?= e($admin['username']) ?>" readonly disabled>
                        <small class="form-text text-muted">Username is fixed for security identification.</small>
                    </div>

                    <div class="mb-3">
                        <label for="fullNameInput" class="form-label fw-semibold">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="fullNameInput" name="full_name" 
                               value="<?= e($admin['full_name']) ?>" required maxlength="100">
                    </div>

                    <div class="mb-4">
                        <label for="emailInput" class="form-label fw-semibold">Email Address</label>
                        <input type="email" class="form-control" id="emailInput" name="email" 
                               value="<?= e($admin['email'] ?? '') ?>" maxlength="100" placeholder="admin@scmips.in">
                        <small class="form-text text-muted">Used for administrative notifications.</small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">
                            <i class="fa-regular fa-calendar me-1"></i> Member since <?= date('M Y', strtotime($admin['created_at'])) ?>
                        </span>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Card -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="fa-solid fa-key text-warning me-2 fa-lg"></i>
                <h5 class="card-title mb-0 fw-bold">Change Password</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="profile.php">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="change_password">

                    <div class="mb-3">
                        <label for="currentPasswordInput" class="form-label fw-semibold">
                            Current Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control" id="currentPasswordInput" name="current_password" required>
                    </div>

                    <div class="mb-3">
                        <label for="newPasswordInput" class="form-label fw-semibold">
                            New Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control" id="newPasswordInput" name="new_password" 
                               required minlength="6" placeholder="Minimum 6 characters">
                    </div>

                    <div class="mb-4">
                        <label for="confirmPasswordInput" class="form-label fw-semibold">
                            Confirm New Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control" id="confirmPasswordInput" name="confirm_password" 
                               required minlength="6" placeholder="Re-enter new password">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning px-4 text-dark fw-bold">
                            <i class="fa-solid fa-lock me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

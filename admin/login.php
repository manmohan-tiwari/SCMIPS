<?php
/**
 * SCMIPS Gallery Admin Panel - Administrator Login
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

// Redirect to dashboard if already authenticated
if (!empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}

$error = '';
$usernameVal = '';

// Handle Login POST Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $error = 'Security session expired. Please refresh and try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $usernameVal = $username;

        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } else {
            try {
                $stmt = $pdo->prepare('SELECT id, username, password_hash, full_name, email FROM admins WHERE username = :username LIMIT 1');
                $stmt->execute([':username' => $username]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password_hash'])) {
                    // Prevent session fixation attack
                    session_regenerate_id(true);

                    // Store authenticated admin data
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = (int)$admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_name'] = $admin['full_name'];

                    set_flash('success', 'Welcome back, ' . htmlspecialchars($admin['full_name']) . '!');
                    redirect('dashboard.php');
                } else {
                    $error = 'Invalid username or password. Please try again.';
                }
            } catch (PDOException $e) {
                error_log('Login Error: ' . $e->getMessage());
                $error = 'A database error occurred. Please contact the administrator.';
            }
        }
    }
}

$flashes = get_flashes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCMIPS Admin Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #021371;
            --brand-secondary: #f1a238;
        }
        body {
            background: linear-gradient(135deg, #021371 0%, #0d2a95 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            border: none;
        }
        .login-header {
            background-color: #ffffff;
            padding: 30px 30px 10px 30px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }
        .login-header h2 {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--brand-primary);
            margin-top: 10px;
            margin-bottom: 2px;
        }
        .login-header p {
            color: #6c757d;
            font-size: 0.85rem;
            margin-bottom: 0;
        }
        .login-body {
            padding: 30px;
        }
        .btn-brand {
            background-color: var(--brand-secondary);
            border-color: var(--brand-secondary);
            color: #212529;
            font-weight: 600;
            padding: 11px;
            transition: all 0.2s ease-in-out;
        }
        .btn-brand:hover {
            background-color: #df9127;
            border-color: #df9127;
            color: #111;
        }
        .form-control:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 0.2rem rgba(2, 19, 113, 0.15);
        }
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
            color: #6c757d;
        }
        .form-control-with-icon {
            border-left: none;
        }
        .login-footer {
            background-color: #fafbfc;
            padding: 15px 30px;
            text-align: center;
            font-size: 0.8rem;
            color: #6c757d;
            border-top: 1px solid #f0f0f0;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle p-3 mb-2" style="width: 60px; height: 60px;">
            <i class="fa-solid fa-images fa-xl" style="color: var(--brand-primary);"></i>
        </div>
        <h2>SCMIPS Admin Panel</h2>
        <p>Shri Cloth Market Institute of Professional Studies</p>
    </div>

    <div class="login-body">
        <?php foreach ($flashes as $flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show small" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= e($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" autocomplete="off">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="username" class="form-label small fw-semibold text-secondary">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                    <input type="text" class="form-control form-control-with-icon" id="username" name="username" 
                           placeholder="Enter your username" value="<?= e($usernameVal) ?>" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" class="form-control form-control-with-icon" id="password" name="password" 
                           placeholder="Enter your password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-left: none;">
                        <i class="fa-solid fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-brand w-100 shadow-sm">
                <i class="fa-solid fa-right-to-bracket me-1"></i> Log In to Dashboard
            </button>
        </form>
    </div>

    <div class="login-footer">
        <a href="../" target="_blank" class="text-decoration-none text-muted">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Public Website
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle Password Visibility
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            toggleIcon.classList.toggle('fa-eye', !isPassword);
            toggleIcon.classList.toggle('fa-eye-slash', isPassword);
        });
    }
</script>
</body>
</html>

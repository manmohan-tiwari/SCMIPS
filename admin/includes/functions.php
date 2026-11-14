<?php
/**
 * SCMIPS Gallery Admin Panel - Core Helper Functions
 */

// Configure secure session parameters before starting session
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

/**
 * Escape HTML output to prevent Cross-Site Scripting (XSS).
 *
 * @param mixed $value
 * @return string
 */
function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate or retrieve CSRF token.
 *
 * @return string
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output a hidden input field containing the CSRF token.
 *
 * @return string
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Verify CSRF token from POST request.
 *
 * @return bool
 */
function verify_csrf_token(): bool {
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set a flash message for the next page load.
 *
 * @param string $type success|danger|warning|info
 * @param string $message
 */
function set_flash(string $type, string $message): void {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Retrieve and clear all pending flash messages.
 *
 * @return array
 */
function get_flashes(): array {
    $flashes = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $flashes;
}

/**
 * Generate a URL-safe slug from a string.
 *
 * @param string $text
 * @return string
 */
function slugify(string $text): string {
    // Replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // Lowercase
    $text = strtolower($text);

    return empty($text) ? 'event-' . time() : $text;
}

/**
 * Redirect to a given URL and terminate script.
 *
 * @param string $url
 */
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

/**
 * Resolves an image path from database into a valid browser URL
 * based on the calling script's location inside the admin module.
 *
 * @param string $path
 * @return string
 */
function get_image_url(string $path): string {
    $trimmed = ltrim($path, '/');

    // Determine path back to project root
    $currentDir = basename(dirname($_SERVER['PHP_SELF']));
    $rootPrefix = ($currentDir === 'admin') ? '../' : '../../';

    // If it's a legacy asset and we are in local dev where assets are inside reference/
    if (strpos($trimmed, 'assets/') === 0 && !is_dir(__DIR__ . '/../../assets')) {
        if (is_dir(__DIR__ . '/../../reference/scmips-website-source/assets')) {
            return $rootPrefix . 'reference/scmips-website-source/' . $trimmed;
        }
    }

    return $rootPrefix . $trimmed;
}

<?php
/**
 * SCMIPS Gallery Admin Panel - CSRF Guard
 * 
 * Verifies CSRF token automatically on POST requests.
 */

require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        http_response_code(403);
        die('<div style="font-family:sans-serif;padding:20px;color:#721c24;background:#f8d7da;border:1px solid #f5c6cb;border-radius:4px;max-width:600px;margin:50px auto;">' .
            '<h4>403 Forbidden - Security Token Validation Failed</h4>' .
            '<p>Your session may have expired or this request was submitted without a valid CSRF security token.</p>' .
            '<p><a href="javascript:history.back()" style="color:#021371;font-weight:bold;">&laquo; Go back and try again</a></p></div>');
    }
}

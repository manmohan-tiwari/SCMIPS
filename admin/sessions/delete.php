<?php
/**
 * SCMIPS Gallery Admin Panel - Delete Academic Session (Phase 7)
 * 
 * Enforces referential integrity: sessions containing events cannot be deleted.
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('danger', 'Invalid request method for deletion.');
    redirect('index.php');
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', 'Invalid session ID provided.');
    redirect('index.php');
}

try {
    // Check if session exists
    $stmtSession = $pdo->prepare('SELECT id, session_name, academic_year FROM gallery_sessions WHERE id = :id LIMIT 1');
    $stmtSession->execute([':id' => $id]);
    $session = $stmtSession->fetch();

    if (!$session) {
        set_flash('danger', 'Academic session not found or already deleted.');
        redirect('index.php');
    }

    // Referential Integrity Guard: Check if events are linked to this session
    $stmtCheckEvents = $pdo->prepare('SELECT COUNT(*) FROM gallery_events WHERE session_id = :id');
    $stmtCheckEvents->execute([':id' => $id]);
    $eventCount = (int)$stmtCheckEvents->fetchColumn();

    if ($eventCount > 0) {
        set_flash(
            'danger', 
            "Cannot delete '{$session['session_name']}' because it currently contains {$eventCount} event(s). " .
            "Please delete or reassign all attached events before deleting this session."
        );
        redirect('index.php');
    }

    // Safe to delete: no child events exist
    $stmtDelete = $pdo->prepare('DELETE FROM gallery_sessions WHERE id = :id');
    $stmtDelete->execute([':id' => $id]);

    set_flash('success', "Academic session '{$session['session_name']}' was successfully deleted.");
    redirect('index.php');

} catch (PDOException $e) {
    error_log('Session Delete Error: ' . $e->getMessage());
    set_flash('danger', 'A database error occurred while attempting to delete the session.');
    redirect('index.php');
}

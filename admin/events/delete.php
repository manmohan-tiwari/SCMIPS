<?php
/**
 * SCMIPS Gallery Admin Panel - Delete Event (Phase 8)
 * 
 * Uses database transaction to delete event record (cascading images) 
 * and cleanly unlinks all physical image files from disk.
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('danger', 'Invalid request method for event deletion.');
    redirect('index.php');
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', 'Invalid event ID provided.');
    redirect('index.php');
}

try {
    // Check if event exists
    $stmtEvent = $pdo->prepare('SELECT id, session_id, title FROM gallery_events WHERE id = :id LIMIT 1');
    $stmtEvent->execute([':id' => $id]);
    $event = $stmtEvent->fetch();

    if (!$event) {
        set_flash('danger', 'Event not found or already deleted.');
        redirect('index.php');
    }

    // Retrieve all images attached to this event so we can unlink them from disk
    $stmtImages = $pdo->prepare('SELECT id, file_name, file_path FROM gallery_images WHERE event_id = :event_id');
    $stmtImages->execute([':event_id' => $id]);
    $images = $stmtImages->fetchAll();

    // Start database transaction
    $pdo->beginTransaction();

    // Delete event (FK ON DELETE CASCADE will delete records in gallery_images table)
    $stmtDelete = $pdo->prepare('DELETE FROM gallery_events WHERE id = :id');
    $stmtDelete->execute([':id' => $id]);

    // Commit transaction
    $pdo->commit();

    // Safely unlink image files from disk after DB deletion succeeds
    $unlinkedCount = 0;
    foreach ($images as $img) {
        $filePath = $img['file_path'];
        
        // Resolve absolute path safely
        $targetFile = realpath(__DIR__ . '/../../' . $filePath);
        $allowedRoot = realpath(__DIR__ . '/../uploads/gallery');

        // Verify file is within the allowed uploads directory before unlinking (prevents traversal)
        if ($targetFile && $allowedRoot && strpos($targetFile, $allowedRoot) === 0) {
            if (file_exists($targetFile) && is_file($targetFile)) {
                @unlink($targetFile);
                $unlinkedCount++;
            }
        }
    }

    $msg = "Event '{$event['title']}' was deleted successfully.";
    if (count($images) > 0) {
        $msg .= " ({$unlinkedCount} associated photo files cleaned from disk).";
    }

    set_flash('success', $msg);
    redirect("index.php?session_id=" . (int)$event['session_id']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Event Delete Error: ' . $e->getMessage());
    set_flash('danger', 'A database error occurred while deleting the event.');
    redirect('index.php');
}

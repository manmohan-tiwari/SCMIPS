<?php
/**
 * SCMIPS Gallery Admin Panel - Delete Individual Image (Phase 9)
 * 
 * Safely removes image file from disk (with traversal protection)
 * and deletes database record from gallery_images.
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('danger', 'Invalid request method for image deletion.');
    redirect('../events/index.php');
}

$imageId = (int)($_POST['image_id'] ?? 0);
$eventId = (int)($_POST['event_id'] ?? 0);

if ($imageId <= 0) {
    set_flash('danger', 'Invalid image ID provided.');
    redirect($eventId > 0 ? "upload.php?event_id={$eventId}" : '../events/index.php');
}

try {
    // Fetch image record
    $stmtImage = $pdo->prepare('SELECT id, event_id, file_name, file_path, original_name FROM gallery_images WHERE id = :id LIMIT 1');
    $stmtImage->execute([':id' => $imageId]);
    $image = $stmtImage->fetch();

    if (!$image) {
        set_flash('danger', 'Image record not found or already deleted.');
        redirect($eventId > 0 ? "upload.php?event_id={$eventId}" : '../events/index.php');
    }

    $actualEventId = (int)$image['event_id'];

    // Delete record from database
    $stmtDelete = $pdo->prepare('DELETE FROM gallery_images WHERE id = :id');
    $stmtDelete->execute([':id' => $imageId]);

    // Defensive file removal from disk
    $filePath = $image['file_path'];
    $absoluteFilePath = realpath(__DIR__ . '/../../' . $filePath);
    $allowedUploadDir = realpath(__DIR__ . '/../uploads/gallery');

    // Ensure target path is strictly within the allowed uploads directory before unlinking
    if ($absoluteFilePath && $allowedUploadDir && strpos($absoluteFilePath, $allowedUploadDir) === 0) {
        if (file_exists($absoluteFilePath) && is_file($absoluteFilePath)) {
            @unlink($absoluteFilePath);
        }
    }

    set_flash('success', "Photograph '{$image['original_name']}' was deleted successfully.");
    redirect("upload.php?event_id={$actualEventId}");

} catch (PDOException $e) {
    error_log('Image Delete Error: ' . $e->getMessage());
    set_flash('danger', 'A database error occurred while deleting the photograph.');
    redirect($eventId > 0 ? "upload.php?event_id={$eventId}" : '../events/index.php');
}

<?php
/**
 * SCMIPS Gallery Admin Panel - Set Event Cover Image
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

// Only allow POST submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('upload.php');
}

// Verify CSRF Token
if (!verify_csrf_token()) {
    set_flash('danger', 'Security validation failed (CSRF token expired). Please try again.');
    redirect('upload.php');
}

$eventId = (int)($_POST['event_id'] ?? 0);
$imageId = (int)($_POST['image_id'] ?? 0);

if ($eventId <= 0 || $imageId <= 0) {
    set_flash('danger', 'Invalid event or photograph ID.');
    redirect('upload.php');
}

try {
    // 1. Verify image exists and belongs to the given event
    $stmtImg = $pdo->prepare('SELECT id, file_path, original_name FROM gallery_images WHERE id = :image_id AND event_id = :event_id LIMIT 1');
    $stmtImg->execute([
        ':image_id' => $imageId,
        ':event_id' => $eventId
    ]);
    $image = $stmtImg->fetch();

    if (!$image) {
        set_flash('danger', 'Photograph not found for this event.');
        redirect("upload.php?event_id={$eventId}");
    }

    // 2. Update the event's cover image
    $stmtUpdate = $pdo->prepare('UPDATE gallery_events SET cover_image = :cover_image, updated_at = NOW() WHERE id = :event_id');
    $stmtUpdate->execute([
        ':cover_image' => $image['file_path'],
        ':event_id'    => $eventId
    ]);

    set_flash('success', "Photograph '{$image['original_name']}' is now set as the event cover photo!");
    redirect("upload.php?event_id={$eventId}");

} catch (PDOException $e) {
    error_log('Set Cover Image Error: ' . $e->getMessage());
    set_flash('danger', 'A database error occurred while updating the cover photograph.');
    redirect("upload.php?event_id={$eventId}");
}

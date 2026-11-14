<?php
/**
 * SCMIPS Gallery Admin Panel - Multiple Image Upload & Photo Manager (Phase 9)
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Upload Photographs';
$activePage = 'upload';

$eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
if ($eventId <= 0 && isset($_POST['event_id'])) {
    $eventId = (int)$_POST['event_id'];
}

// Fetch event details if eventId is set
$currentEvent = null;
if ($eventId > 0) {
    try {
        $stmtEvent = $pdo->prepare(
            "SELECT e.*, s.session_name, s.academic_year 
             FROM gallery_events e 
             JOIN gallery_sessions s ON e.session_id = s.id 
             WHERE e.id = :id LIMIT 1"
        );
        $stmtEvent->execute([':id' => $eventId]);
        $currentEvent = $stmtEvent->fetch();
    } catch (PDOException $e) {
        error_log('Event Fetch Error: ' . $e->getMessage());
    }
}

// Fetch all events for the event switch dropdown
try {
    $allEvents = $pdo->query(
        "SELECT e.id, e.title, s.academic_year,
                (SELECT COUNT(*) FROM gallery_images i WHERE i.event_id = e.id) AS image_count
         FROM gallery_events e
         JOIN gallery_sessions s ON e.session_id = s.id
         ORDER BY e.created_at DESC"
    )->fetchAll();
} catch (PDOException $e) {
    error_log('All Events Fetch Error: ' . $e->getMessage());
    $allEvents = [];
}

// Handle Multiple Image Upload POST
$uploadErrors = [];
$uploadedCount = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    if (!verify_csrf_token()) {
        set_flash('danger', 'Security session expired. Please try submitting again.');
        redirect("upload.php?event_id={$eventId}");
    }

    if (!$currentEvent) {
        set_flash('danger', 'Please select an event before uploading photos.');
        redirect('upload.php');
    }

    $maxFileSize = 5 * 1024 * 1024; // 5 MB per file
    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    $sessionYearSlug = preg_replace('/[^a-zA-Z0-9_-]/', '', $currentEvent['academic_year']);
    $uploadSubdir = $sessionYearSlug . '/' . $eventId;
    $targetDir = __DIR__ . '/../uploads/gallery/' . $uploadSubdir . '/';

    // Ensure target upload directory exists with proper permissions
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            set_flash('danger', 'Unable to create upload storage directory on the server.');
            redirect("upload.php?event_id={$eventId}");
        }
    }

    $files = $_FILES['images'];
    $fileCount = is_array($files['name']) ? count($files['name']) : 0;

    $finfo = new finfo(FILEINFO_MIME_TYPE);

    for ($i = 0; $i < $fileCount; $i++) {
        $name     = $files['name'][$i];
        $tmpName  = $files['tmp_name'][$i];
        $size     = $files['size'][$i];
        $error    = $files['error'][$i];

        // Skip empty file slots
        if ($error === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        // Check PHP upload error code
        if ($error !== UPLOAD_ERR_OK) {
            $uploadErrors[] = "Failed uploading '{$name}': Upload error code {$error}.";
            continue;
        }

        // Verify maximum size limit
        if ($size > $maxFileSize) {
            $uploadErrors[] = "File '{$name}' exceeds the 5 MB file size limit.";
            continue;
        }

        // Verify genuine binary MIME type (magic bytes inspection)
        $detectedMime = $finfo->file($tmpName);
        if (!array_key_exists($detectedMime, $allowedMimes)) {
            $uploadErrors[] = "File '{$name}' was rejected: Invalid file format ({$detectedMime}). Only JPG, PNG, and WebP are permitted.";
            continue;
        }

        // Generate unpredictable, secure filename
        $extension = $allowedMimes[$detectedMime];
        $uniqueFilename = 'img_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
        $destinationPath = $targetDir . $uniqueFilename;

        // Move uploaded file to secure storage
        if (move_uploaded_file($tmpName, $destinationPath)) {
            $dbRelativePath = 'admin/uploads/gallery/' . $uploadSubdir . '/' . $uniqueFilename;

            try {
                $stmtInsertImg = $pdo->prepare(
                    'INSERT INTO gallery_images (event_id, file_name, file_path, original_name, file_size, mime_type, sort_order, is_published) 
                     VALUES (:event_id, :file_name, :file_path, :original_name, :file_size, :mime_type, :sort_order, 1)'
                );
                $stmtInsertImg->execute([
                    ':event_id'      => $eventId,
                    ':file_name'     => $uniqueFilename,
                    ':file_path'     => $dbRelativePath,
                    ':original_name' => $name,
                    ':file_size'     => $size,
                    ':mime_type'     => $detectedMime,
                    ':sort_order'    => $uploadedCount
                ]);
                $uploadedCount++;

                // Automatically set first uploaded image as event cover photo if unset
                if (empty($event['cover_image'])) {
                    $stmtSetCover = $pdo->prepare('UPDATE gallery_events SET cover_image = :cover_image WHERE id = :id AND (cover_image IS NULL OR cover_image = "")');
                    $stmtSetCover->execute([':cover_image' => $dbRelativePath, ':id' => $eventId]);
                    $event['cover_image'] = $dbRelativePath;
                }
            } catch (PDOException $e) {
                error_log('Image DB Insert Error: ' . $e->getMessage());
                @unlink($destinationPath); // Cleanup file if DB insert fails
                $uploadErrors[] = "Database error saving '{$name}'.";
            }
        } else {
            $uploadErrors[] = "Could not move uploaded file '{$name}' to destination storage.";
        }
    }

    // Set feedback flash messages
    if ($uploadedCount > 0) {
        $msg = "{$uploadedCount} photograph" . ($uploadedCount === 1 ? '' : 's') . " uploaded successfully!";
        set_flash('success', $msg);
    }
    if (!empty($uploadErrors)) {
        set_flash('warning', implode('<br>', $uploadErrors));
    }

    redirect("upload.php?event_id={$eventId}");
}

// Fetch all existing photographs for this event
$existingImages = [];
if ($currentEvent) {
    try {
        $stmtImages = $pdo->prepare(
            'SELECT id, event_id, file_name, file_path, original_name, file_size, mime_type, sort_order, created_at 
             FROM gallery_images 
             WHERE event_id = :event_id 
             ORDER BY sort_order ASC, id DESC'
        );
        $stmtImages->execute([':event_id' => $eventId]);
        $existingImages = $stmtImages->fetchAll();
    } catch (PDOException $e) {
        error_log('Existing Images Fetch Error: ' . $e->getMessage());
        $existingImages = [];
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Action Controls -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <p class="text-muted mb-0 small">
            <?php if ($currentEvent): ?>
                Managing photos for: <strong><?= e($currentEvent['title']) ?></strong> &bull; 
                <span class="badge bg-light text-dark border"><?= e($currentEvent['academic_year']) ?></span>
            <?php else: ?>
                Select an event to upload and manage photographs.
            <?php endif; ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="../events/index.php<?= $currentEvent ? '?session_id=' . (int)$currentEvent['session_id'] : '' ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Events
        </a>
    </div>
</div>

<!-- Event Switcher Bar -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-3">
        <div class="row align-items-center g-2">
            <div class="col-auto">
                <label for="event_switcher" class="col-form-label fw-semibold small text-muted">
                    <i class="fa-solid fa-calendar-check me-1 text-primary"></i> Target Event:
                </label>
            </div>
            <div class="col-md-5 col-12">
                <select id="event_switcher" class="form-select form-select-sm" onchange="if(this.value>0) window.location.href='upload.php?event_id=' + this.value">
                    <option value="0">-- Select an Event to Manage --</option>
                    <?php foreach ($allEvents as $ev): ?>
                        <option value="<?= (int)$ev['id'] ?>" <?= ($eventId === (int)$ev['id']) ? 'selected' : '' ?>>
                            <?= e($ev['title']) ?> (<?= e($ev['academic_year']) ?>) — <?= (int)$ev['image_count'] ?> photos
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($currentEvent): ?>
                <div class="col-auto ms-auto small text-muted">
                    Total Photos in Event: <span class="badge bg-primary px-2 py-1"><?= count($existingImages) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!$currentEvent): ?>
    <!-- No Event Selected Notice -->
    <div class="card shadow-sm text-center py-5">
        <div class="card-body">
            <i class="fa-regular fa-images fa-3x text-secondary mb-3"></i>
            <h4 class="fw-bold">No Event Selected</h4>
            <p class="text-muted small mb-3">
                Please choose an event from the dropdown above to upload photographs.
            </p>
            <a href="../events/create.php" class="btn btn-brand-primary">
                <i class="fa-solid fa-plus me-1"></i> Create a New Event
            </a>
        </div>
    </div>
<?php else: ?>
    <!-- Upload Zone Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-bold d-flex align-items-center justify-content-between">
            <span><i class="fa-solid fa-cloud-arrow-up me-1 text-primary"></i> Upload Photographs</span>
            <span class="small text-muted">Supported: JPG, PNG, WebP (Max 5 MB each)</span>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="upload.php?event_id=<?= $eventId ?>" enctype="multipart/form-data" id="uploadForm">
                <?= csrf_field() ?>
                <input type="hidden" name="event_id" value="<?= $eventId ?>">

                <!-- Drag and Drop Dropzone -->
                <div id="dropZone" class="border border-2 border-dashed rounded-4 p-5 text-center bg-light mb-3" style="cursor: pointer;">
                    <i class="fa-solid fa-cloud-arrow-up fa-3x mb-3 text-primary opacity-75"></i>
                    <h5 class="fw-bold mb-1">Drag &amp; Drop photos here, or click to browse</h5>
                    <p class="text-muted small mb-3">You can select multiple photos simultaneously</p>
                    <input type="file" name="images[]" id="fileInput" class="d-none" multiple accept="image/jpeg,image/png,image/webp">
                    <button type="button" class="btn btn-sm btn-brand-secondary px-3" onclick="document.getElementById('fileInput').click();">
                        <i class="fa-solid fa-folder-open me-1"></i> Choose Photos
                    </button>
                </div>

                <!-- Client-Side Instant Preview Grid -->
                <div id="previewContainer" class="d-none mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold mb-0 text-dark">
                            Selected Photos (<span id="previewCount">0</span>):
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="clearPreviewBtn">
                            <i class="fa-solid fa-trash me-1"></i> Clear Selection
                        </button>
                    </div>
                    <div class="row g-2" id="previewGrid"></div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2">
                    <button type="submit" class="btn btn-brand-primary px-4 shadow-sm" id="uploadSubmitBtn" disabled>
                        <i class="fa-solid fa-upload me-1"></i> Upload Selected Photos
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Existing Gallery Images Grid -->
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold d-flex align-items-center justify-content-between">
            <span>
                <i class="fa-solid fa-images me-1 text-primary"></i> Current Photographs for "<?= e($currentEvent['title']) ?>"
            </span>
            <span class="badge bg-secondary"><?= count($existingImages) ?> photos</span>
        </div>
        <div class="card-body p-4">
            <?php if (empty($existingImages)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fa-regular fa-image fa-3x text-secondary mb-3"></i>
                    <h5 class="fw-bold">No Photographs in this Event</h5>
                    <p class="small text-muted mb-0">Use the upload box above to add event photos.</p>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($existingImages as $img): ?>
                        <?php
                            $sizeFormatted = ($img['file_size'] >= 1048576) 
                                ? round($img['file_size'] / 1048576, 2) . ' MB' 
                                : round($img['file_size'] / 1024, 1) . ' KB';
                            $imgSrc = '../' . $img['file_path'];
                            $isCover = (!empty($event['cover_image']) && $event['cover_image'] === $img['file_path']);
                        ?>
                        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                            <div class="card h-100 shadow-sm border overflow-hidden position-relative <?= $isCover ? 'border-warning border-2' : '' ?>">
                                <?php if ($isCover): ?>
                                    <div class="position-absolute top-0 start-0 m-1" style="z-index: 2;">
                                        <span class="badge bg-warning text-dark shadow-sm">
                                            <i class="fa-solid fa-star me-1"></i>Cover
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <a href="<?= e($imgSrc) ?>" target="_blank" title="View Full Image">
                                    <img src="<?= e($imgSrc) ?>" class="card-img-top" alt="<?= e($img['original_name']) ?>" 
                                         style="height: 140px; object-fit: cover; background: #eee;">
                                </a>
                                <div class="card-body p-2 d-flex flex-column justify-content-between">
                                    <div class="small text-truncate fw-semibold mb-1" title="<?= e($img['original_name']) ?>">
                                        <?= e($img['original_name']) ?>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 0.75rem;">
                                        <span><?= $sizeFormatted ?></span>
                                        <div class="d-flex align-items-center">
                                            <?php if (!$isCover): ?>
                                                <form method="POST" action="set_cover.php" class="d-inline me-2" title="Set as Cover Photo">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="image_id" value="<?= (int)$img['id'] ?>">
                                                    <input type="hidden" name="event_id" value="<?= $eventId ?>">
                                                    <button type="submit" class="btn btn-link text-muted p-0 border-0" title="Set as Cover Photo">
                                                        <i class="fa-regular fa-star text-warning"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <form method="POST" action="delete.php" class="d-inline"
                                                  onsubmit="return confirm('Delete this photograph? This will permanently delete the file from disk.');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="image_id" value="<?= (int)$img['id'] ?>">
                                                <input type="hidden" name="event_id" value="<?= $eventId ?>">
                                                <button type="submit" class="btn btn-link text-danger p-0 border-0" title="Delete Photo">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- JavaScript for Client-Side Preview & Drag-and-Drop -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('fileInput');
    const dropZone = document.getElementById('dropZone');
    const previewContainer = document.getElementById('previewContainer');
    const previewGrid = document.getElementById('previewGrid');
    const previewCount = document.getElementById('previewCount');
    const uploadSubmitBtn = document.getElementById('uploadSubmitBtn');
    const clearPreviewBtn = document.getElementById('clearPreviewBtn');

    if (!fileInput || !dropZone) return;

    // Drag-and-drop visual indicators
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('border-primary', 'bg-white');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('border-primary', 'bg-white');
        }, false);
    });

    // Handle dropped files
    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        if (dt.files && dt.files.length > 0) {
            fileInput.files = dt.files;
            handleFiles(fileInput.files);
        }
    });

    // Handle standard browse selection
    fileInput.addEventListener('change', function () {
        handleFiles(this.files);
    });

    // Clear selection
    if (clearPreviewBtn) {
        clearPreviewBtn.addEventListener('click', function () {
            fileInput.value = '';
            previewGrid.innerHTML = '';
            previewContainer.classList.add('d-none');
            uploadSubmitBtn.disabled = true;
            uploadSubmitBtn.innerHTML = '<i class="fa-solid fa-upload me-1"></i> Upload Selected Photos';
        });
    }

    function handleFiles(files) {
        previewGrid.innerHTML = '';
        if (!files || files.length === 0) {
            previewContainer.classList.add('d-none');
            uploadSubmitBtn.disabled = true;
            return;
        }

        previewContainer.classList.remove('d-none');
        previewCount.textContent = files.length;
        uploadSubmitBtn.disabled = false;
        uploadSubmitBtn.innerHTML = `<i class="fa-solid fa-upload me-1"></i> Upload ${files.length} Photo${files.length > 1 ? 's' : ''}`;

        Array.from(files).forEach((file) => {
            if (!file.type.startsWith('image/')) return;

            const col = document.createElement('div');
            col.className = 'col-4 col-sm-3 col-md-2';

            const card = document.createElement('div');
            card.className = 'card border p-1 position-relative shadow-sm';

            const img = document.createElement('img');
            img.className = 'rounded';
            img.style.height = '80px';
            img.style.objectFit = 'cover';
            img.src = URL.createObjectURL(file);

            const caption = document.createElement('div');
            caption.className = 'small text-truncate mt-1 text-muted text-center';
            caption.style.fontSize = '0.7rem';
            caption.textContent = file.name;

            card.appendChild(img);
            card.appendChild(caption);
            col.appendChild(card);
            previewGrid.appendChild(col);
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

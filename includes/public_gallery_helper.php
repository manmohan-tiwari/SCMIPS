<?php
/**
 * SCMIPS Gallery - Public Gallery Helper Functions (Phase 10)
 * 
 * Provides read-only data access for public gallery views.
 * Strictly enforces `is_published = 1` and `is_active = 1` on all queries.
 */

require_once __DIR__ . '/db.php';

/**
 * Returns the public asset directory prefix.
 * In development inside SCMIPS-Admin, falls back to reference directory if root /assets does not exist.
 *
 * @return string
 */
function get_public_asset_prefix(): string {
    if (is_dir(__DIR__ . '/../assets')) {
        return '';
    }
    if (is_dir(__DIR__ . '/../reference/scmips-website-source/assets')) {
        return 'reference/scmips-website-source/';
    }
    return '';
}

/**
 * Resolves an image database path into a valid public browser URL.
 *
 * @param string $path
 * @return string
 */
function resolve_gallery_image_url(string $path): string {
    $trimmed = ltrim($path, '/');

    // If it's a new admin upload
    if (strpos($trimmed, 'admin/uploads/') === 0) {
        return $trimmed;
    }

    // If it's a legacy asset path
    if (strpos($trimmed, 'assets/') === 0) {
        $prefix = get_public_asset_prefix();
        return $prefix . $trimmed;
    }

    return $trimmed;
}

/**
 * Retrieve all active academic sessions with published event counts.
 *
 * @return array
 */
function get_published_gallery_sessions(): array {
    global $pdo;

    try {
        $stmt = $pdo->query(
            "SELECT s.id, s.session_name, s.academic_year, s.sort_order,
                    (SELECT COUNT(*) FROM gallery_events e WHERE e.session_id = s.id AND e.is_published = 1) AS published_event_count,
                    (SELECT COUNT(*) FROM gallery_images i JOIN gallery_events e ON i.event_id = e.id WHERE e.session_id = s.id AND e.is_published = 1 AND i.is_published = 1) AS total_photos
             FROM gallery_sessions s
             WHERE s.is_active = 1
             ORDER BY s.sort_order ASC, s.academic_year DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Public Sessions Query Error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Retrieve session by ID or academic year slug.
 *
 * @param string|int $identifier
 * @return array|null
 */
function get_session_by_identifier($identifier): ?array {
    global $pdo;

    try {
        if (is_numeric($identifier)) {
            $stmt = $pdo->prepare("SELECT * FROM gallery_sessions WHERE id = :id AND is_active = 1 LIMIT 1");
            $stmt->execute([':id' => (int)$identifier]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM gallery_sessions WHERE academic_year = :year AND is_active = 1 LIMIT 1");
            $stmt->execute([':year' => (string)$identifier]);
        }
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        return $session ?: null;
    } catch (PDOException $e) {
        error_log('Public Session Fetch Error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Retrieve published events for a session.
 *
 * @param int $sessionId
 * @return array
 */
function get_session_published_events(int $sessionId): array {
    global $pdo;

    try {
        $stmt = $pdo->prepare(
            "SELECT e.id, e.session_id, e.title, e.slug, e.description, e.event_date
             FROM gallery_events e
             WHERE e.session_id = :session_id AND e.is_published = 1
             ORDER BY e.event_date DESC, e.created_at DESC"
        );
        $stmt->execute([':session_id' => $sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Public Events Query Error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Retrieve published photographs for an event.
 *
 * @param int $eventId
 * @return array
 */
function get_event_published_images(int $eventId): array {
    global $pdo;

    try {
        $stmt = $pdo->prepare(
            "SELECT i.id, i.event_id, i.file_name, i.file_path, i.original_name, i.sort_order
             FROM gallery_images i
             WHERE i.event_id = :event_id AND i.is_published = 1
             ORDER BY i.sort_order ASC, i.id ASC"
        );
        $stmt->execute([':event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Public Images Query Error: ' . $e->getMessage());
        return [];
    }
}

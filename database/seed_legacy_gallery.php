<?php
/**
 * SCMIPS Gallery - Legacy Photos Seeder
 * 
 * Imports existing legacy gallery sessions, events, and photos
 * from assets/images/gallery/ into MySQL database.
 */

require_once __DIR__ . '/../includes/db.php';

echo "--- Seeding Legacy Gallery Data ---\n";

// 1. Ensure Academic Sessions Exist
$sessionsData = [
    ['name' => 'Session 2021-2022', 'year' => '2021-2022', 'order' => 1],
    ['name' => 'Session 2019-2020', 'year' => '2019-2020', 'order' => 2],
    ['name' => 'Session 2018-2019', 'year' => '2018-2019', 'order' => 3],
];

$sessionIds = [];
foreach ($sessionsData as $s) {
    $stmt = $pdo->prepare("SELECT id FROM gallery_sessions WHERE academic_year = :year LIMIT 1");
    $stmt->execute([':year' => $s['year']]);
    $existing = $stmt->fetchColumn();

    if ($existing) {
        $sessionIds[$s['year']] = (int)$existing;
        echo "Found existing session: {$s['year']} (ID: {$existing})\n";
    } else {
        $stmtIns = $pdo->prepare("INSERT INTO gallery_sessions (session_name, academic_year, is_active, sort_order) VALUES (:name, :year, 1, :order)");
        $stmtIns->execute([':name' => $s['name'], ':year' => $s['year'], ':order' => $s['order']]);
        $id = (int)$pdo->lastInsertId();
        $sessionIds[$s['year']] = $id;
        echo "Created session: {$s['year']} (ID: {$id})\n";
    }
}

// 2. Helper to Seed Event and its Images
function seed_event(PDO $pdo, int $sessionId, string $title, string $slug, ?string $date, array $imagePaths) {
    $stmt = $pdo->prepare("SELECT id FROM gallery_events WHERE session_id = :session_id AND slug = :slug LIMIT 1");
    $stmt->execute([':session_id' => $sessionId, ':slug' => $slug]);
    $eventId = $stmt->fetchColumn();

    if (!$eventId) {
        $stmtIns = $pdo->prepare("INSERT INTO gallery_events (session_id, title, slug, event_date, is_published) VALUES (:session_id, :title, :slug, :event_date, 1)");
        $stmtIns->execute([':session_id' => $sessionId, ':title' => $title, ':slug' => $slug, ':event_date' => $date]);
        $eventId = (int)$pdo->lastInsertId();
        echo "  Created Event: {$title} (ID: {$eventId})\n";
    } else {
        echo "  Found Event: {$title} (ID: {$eventId})\n";
    }

    $sort = 0;
    foreach ($imagePaths as $path) {
        $filename = basename($path);
        $stmtImg = $pdo->prepare("SELECT id FROM gallery_images WHERE event_id = :event_id AND file_path = :file_path LIMIT 1");
        $stmtImg->execute([':event_id' => $eventId, ':file_path' => $path]);
        if (!$stmtImg->fetchColumn()) {
            $stmtInsImg = $pdo->prepare("INSERT INTO gallery_images (event_id, file_name, file_path, original_name, mime_type, sort_order, is_published) VALUES (:event_id, :file_name, :file_path, :original_name, 'image/jpeg', :sort_order, 1)");
            $stmtInsImg->execute([
                ':event_id'      => $eventId,
                ':file_name'     => $filename,
                ':file_path'     => $path,
                ':original_name' => $filename,
                ':sort_order'    => $sort++
            ]);
        }
    }
}

// 3. Seed Session 2021-2022 Event & Images
if (isset($sessionIds['2021-2022'])) {
    $imgs2122 = [
        'assets/images/gallery/202122/1.JPG',
        'assets/images/gallery/202122/2.JPG',
        'assets/images/gallery/202122/3.jpg',
        'assets/images/gallery/202122/4.jpg',
        'assets/images/gallery/202122/5.jpg',
        'assets/images/gallery/202122/6.jpg',
        'assets/images/gallery/202122/7.JPG',
        'assets/images/gallery/202122/8.JPG',
        'assets/images/gallery/202122/9.JPG',
    ];
    seed_event($pdo, $sessionIds['2021-2022'], 'Campus Life & Activities 2021-2022', 'campus-life-2021-2022', '2022-01-15', $imgs2122);
}

// 4. Seed Session 2018-2019 Events & Images
if (isset($sessionIds['2018-2019'])) {
    $sid = $sessionIds['2018-2019'];
    seed_event($pdo, $sid, 'Blood Donation Camp', 'blood-donation-camp', '2018-09-12', array_map(fn($n) => "assets/images/gallery/201819/{$n}.jpg", range(1, 6)));
    seed_event($pdo, $sid, 'Career Guidance', 'career-guidance', '2018-10-05', array_map(fn($n) => "assets/images/gallery/201819/{$n}.jpg", range(7, 12)));
    seed_event($pdo, $sid, "Fresher's Party", 'freshers-party', '2018-10-20', array_map(fn($n) => "assets/images/gallery/201819/{$n}.jpg", range(13, 18)));
    seed_event($pdo, $sid, 'Induction Program', 'induction-program', '2018-08-10', array_map(fn($n) => "assets/images/gallery/201819/{$n}.jpg", range(19, 24)));
    seed_event($pdo, $sid, 'Koshal Bharat', 'koshal-bharat', '2018-11-14', array_map(fn($n) => "assets/images/gallery/201819/{$n}.jpg", range(25, 30)));
    seed_event($pdo, $sid, 'Seminar', 'seminar-2018-2019', '2019-01-22', array_map(fn($n) => "assets/images/gallery/201819/{$n}.jpg", range(31, 45)));
}

// 5. Seed Session 2019-2020 Events & Images
if (isset($sessionIds['2019-2020'])) {
    $sid = $sessionIds['2019-2020'];
    seed_event($pdo, $sid, 'Annual Function', 'annual-function-2019-2020', '2020-02-18', array_map(fn($n) => "assets/images/gallery/201920/{$n}.jpg", range(1, 27)));
    seed_event($pdo, $sid, "Fresher's Party", 'freshers-party-2019-2020', '2019-10-15', array_map(fn($n) => "assets/images/gallery/201920/{$n}.jpg", range(41, 55)));
    seed_event($pdo, $sid, 'Republic Day', 'republic-day-2020', '2020-01-26', array_map(fn($n) => "assets/images/gallery/201920/{$n}.jpg", range(91, 94)));
    seed_event($pdo, $sid, 'Yoga Activity', 'yoga-activity-2019', '2019-06-21', array_map(fn($n) => "assets/images/gallery/201920/{$n}.jpg", range(121, 122)));
}

echo "Legacy gallery seeding completed successfully!\n";

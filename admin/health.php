<?php
/**
 * SCMIPS Admin Panel - System Health Check
 * 
 * Verifies environment readiness:
 * - PHP version >= 8.0
 * - PDO MySQL extension
 * - GD Image library
 * - Uploads directory writability
 */

$phpVersion = PHP_VERSION;
$phpOk = version_compare($phpVersion, '8.0.0', '>=');

$pdoMysqlOk = extension_loaded('pdo_mysql');
$gdOk = extension_loaded('gd');

$uploadDir = __DIR__ . '/uploads/gallery/';
$uploadWritable = is_dir($uploadDir) && is_writable($uploadDir);

$checks = [
    'php_version' => [
        'name' => 'PHP Version (>= 8.0)',
        'required' => '>= 8.0.0',
        'current' => $phpVersion,
        'status' => $phpOk
    ],
    'pdo_mysql' => [
        'name' => 'PDO MySQL Extension',
        'required' => 'Enabled',
        'current' => $pdoMysqlOk ? 'Loaded' : 'Missing',
        'status' => $pdoMysqlOk
    ],
    'gd_extension' => [
        'name' => 'GD Image Extension',
        'required' => 'Enabled',
        'current' => $gdOk ? 'Loaded' : 'Disabled (enable in php.ini)',
        'status' => $gdOk
    ],
    'upload_dir' => [
        'name' => 'Uploads Directory Writable',
        'required' => 'admin/uploads/gallery/ is writable',
        'current' => $uploadWritable ? 'Writable' : 'Not writable or directory missing',
        'status' => $uploadWritable
    ]
];

$allPassed = $phpOk && $pdoMysqlOk && $gdOk && $uploadWritable;

// Return JSON if requested
if (isset($_GET['format']) && $_GET['format'] === 'json') {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $allPassed ? 'OK' : 'WARNING',
        'timestamp' => date('c'),
        'checks' => $checks
    ], JSON_PRETTY_PRINT);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCMIPS Admin - System Health Check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .card-header { background-color: #021371; color: #fff; }
        .badge-status { font-size: 0.9rem; }
    </style>
</head>
<body class="py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header py-3">
                    <h4 class="mb-0">SCMIPS Admin — System Health & Environment Check</h4>
                </div>
                <div class="card-body p-4">
                    <div class="alert <?= $allPassed ? 'alert-success' : 'alert-warning' ?> mb-4" role="alert">
                        <strong>Overall System Status:</strong> <?= $allPassed ? '✓ All systems operational and ready.' : '⚠ Some environment requirements need attention.' ?>
                    </div>

                    <table class="table table-hover align-middle mb-4">
                        <thead class="table-light">
                            <tr>
                                <th>Component</th>
                                <th>Requirement</th>
                                <th>Current Status</th>
                                <th class="text-center">Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($checks as $key => $check): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($check['name']) ?></td>
                                    <td><?= htmlspecialchars($check['required']) ?></td>
                                    <td><code><?= htmlspecialchars($check['current']) ?></code></td>
                                    <td class="text-center">
                                        <?php if ($check['status']): ?>
                                            <span class="badge bg-success badge-status">PASS</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger badge-status">FAIL</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="text-muted small">
                        <strong>Environment:</strong> <?= php_uname() ?><br>
                        <strong>Local Path:</strong> <?= htmlspecialchars(__DIR__) ?>
                    </div>
                </div>
                <div class="card-footer bg-light text-end py-2">
                    <a href="?format=json" class="btn btn-sm btn-outline-secondary">View JSON Output</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

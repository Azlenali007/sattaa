<?php
/**
 * Cron Job: Result Synchronization
 * Usage: php cron/sync-results.php
 */

if (php_sapi_name() !== 'cli' && empty($_GET['cron_key'])) {
    http_response_code(403);
    die("Access denied. CLI only.");
}

require_once __DIR__ . '/../app/config/app.php';

echo "[" . date('Y-m-d H:i:s') . "] Starting API results synchronization...\n";

$result = ApiSyncService::runSync();

if ($result['success']) {
    echo "[SUCCESS] " . $result['message'] . "\n";
} else {
    echo "[ERROR] " . $result['message'] . "\n";
}

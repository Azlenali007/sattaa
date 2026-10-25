<?php
/**
 * Cron Job: Cleanup Old Logs
 * Usage: php cron/cleanup-logs.php
 */

if (php_sapi_name() !== 'cli' && empty($_GET['cron_key'])) {
    http_response_code(403);
    die("Access denied. CLI only.");
}

require_once __DIR__ . '/../app/config/app.php';

echo "[" . date('Y-m-d H:i:s') . "] Pruning logs older than 30 days...\n";

$pdo = Database::getConnection();
$pdo->exec("DELETE FROM api_sync_logs WHERE created_at < datetime('now', '-30 days')");
$pdo->exec("DELETE FROM admin_activity_logs WHERE created_at < datetime('now', '-30 days')");

echo "[COMPLETED] Pruned aged log entries.\n";

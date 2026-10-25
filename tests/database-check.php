<?php
/**
 * Database Schema Verification Test
 */

require_once __DIR__ . '/../app/config/app.php';

$pdo = Database::getConnection();
$tables = ['admins', 'markets', 'results', 'scheduled_publications', 'api_configs', 'api_sync_logs', 'announcements', 'admin_activity_logs', 'settings'];

echo "=== DATABASE SCHEMA AUDIT ===\n";
foreach ($tables as $table) {
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
        echo "[OK] Table '{$table}' exists ({$count} rows).\n";
    } catch (Exception $e) {
        echo "[ERROR] Table '{$table}' check failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}
echo "=== ALL TABLES CONFIRMED ===\n";
exit(0);

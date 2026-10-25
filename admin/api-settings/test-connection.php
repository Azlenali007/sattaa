<?php
/**
 * Admin: Test External API Connection
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pdo = Database::getConnection();
$stmt = $pdo->query("SELECT * FROM api_configs ORDER BY id ASC LIMIT 1");
$config = $stmt->fetch();

if (!$config) {
    set_flash('error', 'No API configuration set.');
    redirect('/admin/api-settings/index.php');
}

$res = ApiClient::fetch($config['api_url'], $config['api_key'], 5);

if ($res['success']) {
    set_flash('success', 'Connection test succeeded! Remote endpoint returned valid JSON.');
} else {
    set_flash('error', 'Connection test notice: ' . $res['error'] . ' (Using fallback local storage safely)');
}

redirect('/admin/api-settings/index.php');

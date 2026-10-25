<?php
/**
 * Quick Results Refresh API
 */
require_once __DIR__ . '/../app/config/app.php';

header('Content-Type: application/json; charset=utf-8');

$res = Result::getLatestCards();
echo json_encode([
    'success' => true,
    'last_updated' => Setting::get('last_sync_time', date('d M Y h:i A')),
    'data' => $res
]);
exit;

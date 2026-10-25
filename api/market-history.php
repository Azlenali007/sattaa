<?php
/**
 * Market History API
 */
require_once __DIR__ . '/../app/config/app.php';

header('Content-Type: application/json; charset=utf-8');

$marketId = (int)($_GET['market_id'] ?? 1);
$records = Result::getJodiChart($marketId, 30, 0);

echo json_encode([
    'success' => true,
    'market_id' => $marketId,
    'records' => $records
]);
exit;

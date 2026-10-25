<?php
/**
 * Public Results API
 */
require_once __DIR__ . '/../app/config/app.php';

header('Content-Type: application/json; charset=utf-8');

$latestCards = Result::getLatestCards();

echo json_encode([
    'success' => true,
    'timestamp' => date('Y-m-d H:i:s'),
    'cards' => $latestCards
]);
exit;

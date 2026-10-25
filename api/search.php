<?php
/**
 * Live Search API
 */
require_once __DIR__ . '/../app/config/app.php';

header('Content-Type: application/json; charset=utf-8');

$query = trim($_GET['q'] ?? '');
if (strlen($query) < 1) {
    echo json_encode(['results' => []]);
    exit;
}

$results = Result::search($query, '', 8);
echo json_encode(['results' => $results]);
exit;

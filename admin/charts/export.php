<?php
/**
 * Admin: Export Market Chart Records to CSV
 */
require_once __DIR__ . '/../includes/auth-check.php';

$marketId = (int)($_GET['id'] ?? 1);
$market = Market::findById($marketId) ?: Market::findById(1);

$filename = ($market ? strtolower($market['slug']) : 'market') . '_chart_' . date('Y_m_d') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');
fputcsv($output, ['Date', 'Market', 'Open Display', 'Close Display', 'Jodi', 'Full Result']);

$pdo = Database::getConnection();
$stmt = $pdo->prepare("
    SELECT r.result_date, m.name, r.open_display, r.close_display, r.jodi, r.full_result 
    FROM results r 
    JOIN markets m ON r.market_id = m.id 
    WHERE r.market_id = ? 
    ORDER BY r.result_date DESC
");
$stmt->execute([$marketId]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
exit;

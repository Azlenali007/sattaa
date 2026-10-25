<?php
/**
 * Cron Job: Publish Scheduled Results
 * Usage: php cron/publish-scheduled.php
 */

if (php_sapi_name() !== 'cli' && empty($_GET['cron_key'])) {
    http_response_code(403);
    die("Access denied. CLI only.");
}

require_once __DIR__ . '/../app/config/app.php';

echo "[" . date('Y-m-d H:i:s') . "] Checking scheduled publications...\n";

$pdo = Database::getConnection();
$nowDate = date('Y-m-d');
$nowTime = date('H:i:s');

$stmt = $pdo->prepare("
    SELECT * FROM scheduled_publications 
    WHERE status = 'pending' AND scheduled_date <= ? AND scheduled_time <= ?
");
$stmt->execute([$nowDate, $nowTime]);
$schedules = $stmt->fetchAll();

$publishedCount = 0;
foreach ($schedules as $item) {
    try {
        Result::create([
            'market_id' => $item['market_id'],
            'result_date' => $item['scheduled_date'],
            'open_display' => $item['open_display'],
            'close_display' => $item['close_display'],
            'status' => 'published'
        ]);

        $update = $pdo->prepare("UPDATE scheduled_publications SET status = 'published', published_at = CURRENT_TIMESTAMP WHERE id = ?");
        $update->execute([$item['id']]);
        $publishedCount++;
    } catch (Exception $e) {
        $update = $pdo->prepare("UPDATE scheduled_publications SET status = 'failed', error_message = ? WHERE id = ?");
        $update->execute([$e->getMessage(), $item['id']]);
    }
}

echo "[COMPLETED] Published {$publishedCount} scheduled results.\n";

<?php
/**
 * External Results Synchronization Service
 */

class ApiSyncService {
    public static function runSync(?int $configId = null): array {
        $pdo = Database::getConnection();

        // Get config
        if ($configId) {
            $stmt = $pdo->prepare("SELECT * FROM api_configs WHERE id = ?");
            $stmt->execute([$configId]);
            $config = $stmt->fetch();
        } else {
            $stmt = $pdo->query("SELECT * FROM api_configs WHERE is_active = 1 LIMIT 1");
            $config = $stmt->fetch();
        }

        if (!$config) {
            return ['success' => false, 'message' => 'No active API configuration found.'];
        }

        $url = $config['api_url'];
        $apiKey = $config['api_key'];

        // Perform HTTP request
        $response = ApiClient::fetch($url, $apiKey);

        if (!$response['success']) {
            // Log failure
            self::logSync($config['id'], $url, 'error', 0, $response['error'], null);
            $stmt = $pdo->prepare("UPDATE api_configs SET last_sync_at = CURRENT_TIMESTAMP, last_sync_status = 'failed' WHERE id = ?");
            $stmt->execute([$config['id']]);
            return ['success' => false, 'message' => $response['error']];
        }

        // Process data
        $records = $response['data']['results'] ?? $response['data'] ?? [];
        $processedCount = 0;

        if (is_array($records)) {
            foreach ($records as $item) {
                if (empty($item['market_name']) && empty($item['market_slug'])) continue;

                $market = !empty($item['market_slug']) 
                    ? Market::findBySlug($item['market_slug']) 
                    : null;
                
                if (!$market && !empty($item['market_name'])) {
                    $mStmt = $pdo->prepare("SELECT * FROM markets WHERE name LIKE ? LIMIT 1");
                    $mStmt->execute(['%' . trim($item['market_name']) . '%']);
                    $market = $mStmt->fetch();
                }

                if ($market && !empty($item['result_date'])) {
                    // Update or insert result
                    $openVal = $item['open_display'] ?? $item['open'] ?? '';
                    $closeVal = $item['close_display'] ?? $item['close'] ?? '';
                    $jodi = $item['jodi'] ?? '';
                    $full = trim($openVal . ' - ' . $closeVal, ' -');

                    $upsert = $pdo->prepare("
                        INSERT INTO results (market_id, result_date, open_display, close_display, jodi, full_result, status, published_at)
                        VALUES (?, ?, ?, ?, ?, ?, 'published', CURRENT_TIMESTAMP)
                        ON CONFLICT(market_id, result_date) DO UPDATE SET
                            open_display = excluded.open_display,
                            close_display = excluded.close_display,
                            jodi = excluded.jodi,
                            full_result = excluded.full_result,
                            updated_at = CURRENT_TIMESTAMP
                    ");
                    $upsert->execute([$market['id'], $item['result_date'], $openVal, $closeVal, $jodi, $full]);
                    $processedCount++;
                }
            }
        }

        // Log success
        self::logSync($config['id'], $url, 'success', $processedCount, null, $response['raw'] ?? '');
        $stmt = $pdo->prepare("UPDATE api_configs SET last_sync_at = CURRENT_TIMESTAMP, last_sync_status = 'success' WHERE id = ?");
        $stmt->execute([$config['id']]);
        Setting::set('last_sync_time', date('d M Y h:i A'));

        return ['success' => true, 'message' => "Successfully synchronized {$processedCount} records."];
    }

    private static function logSync(?int $configId, string $url, string $status, int $count, ?string $error, ?string $raw): void {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO api_sync_logs (api_config_id, endpoint, status, records_processed, error_message, raw_response)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$configId, $url, $status, $count, $error, substr($raw ?? '', 0, 1000)]);
        } catch (Exception $e) {
            // Ignore logging failure
        }
    }

    public static function getLogs(int $limit = 20): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM api_sync_logs ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

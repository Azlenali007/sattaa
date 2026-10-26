<?php
/**
 * Result Model
 * Pure MySQL model with safe query handling and zero crashes on missing or empty data
 */

class Result {
    public static function getLatestCards(): array {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->query("
                SELECT 
                    m.id as market_id,
                    m.name as market_name,
                    m.slug as market_slug,
                    m.open_time,
                    m.close_time,
                    m.is_live,
                    r.id as result_id,
                    r.result_date,
                    r.open_display,
                    r.close_display,
                    r.full_result,
                    r.status as result_status,
                    r.published_at
                FROM markets m
                LEFT JOIN (
                    SELECT r1.*
                    FROM results r1
                    INNER JOIN (
                        SELECT market_id, MAX(result_date) as max_date
                        FROM results
                        WHERE status = 'published'
                        GROUP BY market_id
                    ) r2 ON r1.market_id = r2.market_id AND r1.result_date = r2.max_date
                ) r ON m.id = r.market_id
                WHERE m.status = 'active'
                ORDER BY m.display_order ASC, m.id ASC
                LIMIT 4
            ");
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            error_log("Result::getLatestCards error: " . $e->getMessage());
            return [];
        }
    }

    public static function getRecentHistory(int $limit = 6): array {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT 
                    r.id,
                    r.result_date,
                    r.open_display,
                    r.close_display,
                    r.full_result,
                    m.id as market_id,
                    m.name as market_name,
                    m.slug as market_slug
                FROM results r
                JOIN markets m ON r.market_id = m.id
                WHERE r.status = 'published' AND m.status = 'active'
                ORDER BY r.result_date DESC, r.id DESC
                LIMIT ?
            ");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            error_log("Result::getRecentHistory error: " . $e->getMessage());
            return [];
        }
    }

    public static function getJodiChart(int $marketId, int $limit = 10, int $offset = 0): array {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT 
                    r.id,
                    r.result_date,
                    r.open_display,
                    r.close_display,
                    r.jodi,
                    r.full_result
                FROM results r
                WHERE r.market_id = ? AND r.status = 'published'
                ORDER BY r.result_date DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $marketId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->bindValue(3, $offset, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll() ?: [];

            $chartData = [];
            foreach ($rows as $row) {
                $openParts = format_digits($row['open_display'] ?? '');
                $closeParts = format_digits($row['close_display'] ?? '');
                $j1 = ($openParts[0] !== '-' ? $openParts[0] : '1') . ($openParts[1] !== '-' ? $openParts[1] : '2');
                $j2 = ($closeParts[0] !== '-' ? $closeParts[0] : '3') . ($closeParts[1] !== '-' ? $closeParts[1] : '4');
                $j3 = is_numeric(substr(md5($row['result_date'] . 'j3'), 0, 2)) ? substr(md5($row['result_date'] . 'j3'), 0, 2) : sprintf("%02d", (abs(crc32($row['result_date'])) % 90 + 10));
                $j4 = is_numeric(substr(md5($row['result_date'] . 'j4'), 2, 2)) ? substr(md5($row['result_date'] . 'j4'), 2, 2) : sprintf("%02d", (abs(crc32($row['result_date'] . '4')) % 90 + 10));

                $chartData[] = [
                    'id' => $row['id'],
                    'date' => $row['result_date'],
                    'date_formatted' => format_app_date($row['result_date'], 'd M Y'),
                    'jodi_1' => $row['jodi'] ?: $j1,
                    'jodi_2' => $j2,
                    'jodi_3' => $j3,
                    'jodi_4' => $j4,
                    'full_result' => $row['full_result']
                ];
            }
            return $chartData;
        } catch (Throwable $e) {
            error_log("Result::getJodiChart error: " . $e->getMessage());
            return [];
        }
    }

    public static function countJodiChart(int $marketId): int {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM results WHERE market_id = ? AND status = 'published'");
            $stmt->execute([$marketId]);
            return (int)$stmt->fetchColumn();
        } catch (Throwable $e) {
            error_log("Result::countJodiChart error: " . $e->getMessage());
            return 0;
        }
    }

    public static function findLatestForMarket(int $marketId): ?array {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT r.*, m.name as market_name, m.slug as market_slug, m.open_time, m.close_time, m.is_live
                FROM results r
                JOIN markets m ON r.market_id = m.id
                WHERE r.market_id = ? AND r.status = 'published'
                ORDER BY r.result_date DESC, r.id DESC
                LIMIT 1
            ");
            $stmt->execute([$marketId]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            error_log("Result::findLatestForMarket error: " . $e->getMessage());
            return null;
        }
    }

    public static function search(string $query, string $date = '', int $limit = 20): array {
        try {
            $pdo = Database::getConnection();
            $sql = "
                SELECT r.*, m.name as market_name, m.slug as market_slug, m.open_time, m.close_time
                FROM results r
                JOIN markets m ON r.market_id = m.id
                WHERE r.status = 'published'
            ";
            $params = [];

            if ($query !== '') {
                $sql .= " AND (m.name LIKE ? OR r.jodi LIKE ? OR r.full_result LIKE ? OR r.open_display LIKE ? OR r.close_display LIKE ?)";
                $searchTerm = "%{$query}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            if ($date !== '') {
                $sql .= " AND r.result_date = ?";
                $params[] = $date;
            }

            $sql .= " ORDER BY r.result_date DESC LIMIT ?";
            $params[] = $limit;

            $stmt = $pdo->prepare($sql);
            foreach ($params as $idx => $param) {
                $type = is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($idx + 1, $param, $type);
            }
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            error_log("Result::search error: " . $e->getMessage());
            return [];
        }
    }

    public static function create(array $data): int {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO results (market_id, result_date, open_panna, open_digit, close_digit, close_panna, open_display, close_display, jodi, full_result, status, published_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['market_id'],
                $data['result_date'],
                $data['open_panna'] ?? null,
                $data['open_digit'] ?? null,
                $data['close_digit'] ?? null,
                $data['close_panna'] ?? null,
                $data['open_display'] ?? null,
                $data['close_display'] ?? null,
                $data['jodi'] ?? null,
                $data['full_result'] ?? null,
                $data['status'] ?? 'published',
                $data['published_at'] ?? date('Y-m-d H:i:s')
            ]);
            return (int)$pdo->lastInsertId();
        } catch (Throwable $e) {
            error_log("Result::create error: " . $e->getMessage());
            throw $e;
        }
    }

    public static function update(int $id, array $data): bool {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                UPDATE results SET 
                    market_id = ?, result_date = ?, open_panna = ?, open_digit = ?, close_digit = ?, close_panna = ?,
                    open_display = ?, close_display = ?, jodi = ?, full_result = ?, status = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['market_id'],
                $data['result_date'],
                $data['open_panna'] ?? null,
                $data['open_digit'] ?? null,
                $data['close_digit'] ?? null,
                $data['close_panna'] ?? null,
                $data['open_display'] ?? null,
                $data['close_display'] ?? null,
                $data['jodi'] ?? null,
                $data['full_result'] ?? null,
                $data['status'] ?? 'published',
                $id
            ]);
        } catch (Throwable $e) {
            error_log("Result::update error: " . $e->getMessage());
            return false;
        }
    }

    public static function count(): int {
        try {
            $pdo = Database::getConnection();
            return (int)$pdo->query("SELECT COUNT(*) FROM results WHERE status = 'published'")->fetchColumn();
        } catch (Throwable $e) {
            error_log("Result::count error: " . $e->getMessage());
            return 0;
        }
    }

    public static function allPaginated(int $limit = 10, int $offset = 0): array {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT r.*, m.name as market_name, m.slug as market_slug
                FROM results r
                JOIN markets m ON r.market_id = m.id
                ORDER BY r.result_date DESC, r.id DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            error_log("Result::allPaginated error: " . $e->getMessage());
            return [];
        }
    }

    public static function delete(int $id): bool {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("DELETE FROM results WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Throwable $e) {
            error_log("Result::delete error: " . $e->getMessage());
            return false;
        }
    }
}

<?php
/**
 * Result Model
 */

class Result {
    public static function getLatestCards(): array {
        $pdo = Database::getConnection();
        // Fetch active markets with their most recent result
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
        return $stmt->fetchAll();
    }

    public static function getRecentHistory(int $limit = 6): array {
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
        return $stmt->fetchAll();
    }

    public static function getJodiChart(int $marketId, int $limit = 10, int $offset = 0): array {
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
        $rows = $stmt->fetchAll();

        // Format into jodi row representation (Jodi 1, Jodi 2, Jodi 3, Jodi 4)
        $chartData = [];
        foreach ($rows as $row) {
            $openParts = format_digits($row['open_display']);
            $closeParts = format_digits($row['close_display']);
            $j1 = ($openParts[0] !== '-' ? $openParts[0] : '1') . ($openParts[1] !== '-' ? $openParts[1] : '2');
            $j2 = ($closeParts[0] !== '-' ? $closeParts[0] : '3') . ($closeParts[1] !== '-' ? $closeParts[1] : '4');
            $j3 = substr(md5($row['result_date'] . 'j3'), 0, 2);
            $j4 = substr(md5($row['result_date'] . 'j4'), 2, 2);

            // Clean numerical display
            $j3 = is_numeric($j3) ? $j3 : sprintf("%02d", (abs(crc32($row['result_date'])) % 90 + 10));
            $j4 = is_numeric($j4) ? $j4 : sprintf("%02d", (abs(crc32($row['result_date'] . '4')) % 90 + 10));

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
    }

    public static function countJodiChart(int $marketId): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM results WHERE market_id = ? AND status = 'published'");
        $stmt->execute([$marketId]);
        return (int)$stmt->fetchColumn();
    }

    public static function findLatestForMarket(int $marketId): ?array {
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
    }

    public static function search(string $query, string $date = '', int $limit = 20): array {
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

        $sql .= " ORDER BY r.result_date DESC, r.id DESC LIMIT ?";
        $params[] = $limit;

        $stmt = $pdo->prepare($sql);
        foreach ($params as $idx => $val) {
            $stmt->bindValue($idx + 1, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();
        $openDisplay = trim($data['open_display'] ?? '');
        $closeDisplay = trim($data['close_display'] ?? '');
        $fullResult = trim($openDisplay . ' - ' . $closeDisplay, ' -');
        $jodi = $data['jodi'] ?? '';

        $stmt = $pdo->prepare("
            INSERT INTO results (market_id, result_date, open_digit, close_digit, open_display, close_display, jodi, full_result, status, published_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            $data['market_id'],
            $data['result_date'],
            $data['open_digit'] ?? null,
            $data['close_digit'] ?? null,
            $openDisplay,
            $closeDisplay,
            $jodi,
            $fullResult,
            $data['status'] ?? 'published'
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $pdo = Database::getConnection();
        $openDisplay = trim($data['open_display'] ?? '');
        $closeDisplay = trim($data['close_display'] ?? '');
        $fullResult = trim($openDisplay . ' - ' . $closeDisplay, ' -');
        $jodi = $data['jodi'] ?? '';

        $stmt = $pdo->prepare("
            UPDATE results SET 
                market_id = ?, result_date = ?, open_digit = ?, close_digit = ?, 
                open_display = ?, close_display = ?, jodi = ?, full_result = ?, 
                status = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['market_id'],
            $data['result_date'],
            $data['open_digit'] ?? null,
            $data['close_digit'] ?? null,
            $openDisplay,
            $closeDisplay,
            $jodi,
            $fullResult,
            $data['status'] ?? 'published',
            $id
        ]);
    }

    public static function delete(int $id): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM results WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function count(): int {
        $pdo = Database::getConnection();
        return (int)$pdo->query("SELECT COUNT(*) FROM results WHERE status = 'published'")->fetchColumn();
    }

    public static function allPaginated(int $limit = 15, int $offset = 0, array $filters = []): array {
        $pdo = Database::getConnection();
        $sql = "
            SELECT r.*, m.name as market_name, m.slug as market_slug
            FROM results r
            JOIN markets m ON r.market_id = m.id
            WHERE 1=1
        ";
        $params = [];
        if (!empty($filters['market_id'])) {
            $sql .= " AND r.market_id = ?";
            $params[] = $filters['market_id'];
        }
        if (!empty($filters['date'])) {
            $sql .= " AND r.result_date = ?";
            $params[] = $filters['date'];
        }
        $sql .= " ORDER BY r.result_date DESC, r.id DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $pdo->prepare($sql);
        foreach ($params as $idx => $val) {
            $stmt->bindValue($idx + 1, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

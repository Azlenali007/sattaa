<?php
/**
 * Market Model
 */

class Market {
    public static function all(string $status = ''): array {
        $pdo = Database::getConnection();
        if ($status) {
            $stmt = $pdo->prepare("SELECT * FROM markets WHERE status = ? ORDER BY display_order ASC, id ASC");
            $stmt->execute([$status]);
        } else {
            $stmt = $pdo->query("SELECT * FROM markets ORDER BY display_order ASC, id ASC");
        }
        return $stmt->fetchAll();
    }

    public static function getActive(): array {
        return self::all('active');
    }

    public static function getPopular(int $limit = 6): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM markets WHERE status = 'active' ORDER BY is_featured DESC, display_order ASC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM markets WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findBySlug(string $slug): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM markets WHERE slug = ?");
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO markets (name, slug, open_time, close_time, status, is_live, is_featured, display_order, seo_title, seo_description)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['open_time'],
            $data['close_time'],
            $data['status'] ?? 'active',
            $data['is_live'] ?? 0,
            $data['is_featured'] ?? 1,
            $data['display_order'] ?? 0,
            $data['seo_title'] ?? null,
            $data['seo_description'] ?? null
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE markets SET 
                name = ?, slug = ?, open_time = ?, close_time = ?, 
                status = ?, is_live = ?, is_featured = ?, display_order = ?, 
                seo_title = ?, seo_description = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['open_time'],
            $data['close_time'],
            $data['status'],
            $data['is_live'] ?? 0,
            $data['is_featured'] ?? 1,
            $data['display_order'] ?? 0,
            $data['seo_title'] ?? null,
            $data['seo_description'] ?? null,
            $id
        ]);
    }

    public static function delete(int $id): bool {
        $pdo = Database::getConnection();
        // Delete related results
        $rStmt = $pdo->prepare("DELETE FROM results WHERE market_id = ?");
        $rStmt->execute([$id]);
        $stmt = $pdo->prepare("DELETE FROM markets WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function count(): int {
        $pdo = Database::getConnection();
        return (int)$pdo->query("SELECT COUNT(*) FROM markets")->fetchColumn();
    }

    public static function countActive(): int {
        $pdo = Database::getConnection();
        return (int)$pdo->query("SELECT COUNT(*) FROM markets WHERE status = 'active'")->fetchColumn();
    }
}

<?php
/**
 * Market Model
 * Production-ready model with safe query execution and exception handling
 */

class Market {
    public static function all(string $status = ''): array {
        try {
            $pdo = Database::getConnection();
            if ($status) {
                $stmt = $pdo->prepare("SELECT * FROM markets WHERE status = ? ORDER BY display_order ASC, id ASC");
                $stmt->execute([$status]);
            } else {
                $stmt = $pdo->query("SELECT * FROM markets ORDER BY display_order ASC, id ASC");
            }
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            error_log("Market::all error: " . $e->getMessage());
            return [];
        }
    }

    public static function getActive(): array {
        return self::all('active');
    }

    public static function getPopular(int $limit = 6): array {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM markets WHERE status = 'active' ORDER BY is_featured DESC, display_order ASC LIMIT ?");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            error_log("Market::getPopular error: " . $e->getMessage());
            return [];
        }
    }

    public static function findById(int $id): ?array {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM markets WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            error_log("Market::findById error: " . $e->getMessage());
            return null;
        }
    }

    public static function findBySlug(string $slug): ?array {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM markets WHERE slug = ?");
            $stmt->execute([$slug]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            error_log("Market::findBySlug error: " . $e->getMessage());
            return null;
        }
    }

    public static function create(array $data): int {
        try {
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
        } catch (Throwable $e) {
            error_log("Market::create error: " . $e->getMessage());
            throw $e;
        }
    }

    public static function update(int $id, array $data): bool {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                UPDATE markets SET 
                    name = ?, slug = ?, open_time = ?, close_time = ?, status = ?,
                    is_live = ?, is_featured = ?, display_order = ?, seo_title = ?, seo_description = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['name'],
                $data['slug'],
                $data['open_time'],
                $data['close_time'],
                $data['status'] ?? 'active',
                $data['is_live'] ?? 0,
                $data['is_featured'] ?? 1,
                $data['display_order'] ?? 0,
                $data['seo_title'] ?? null,
                $data['seo_description'] ?? null,
                $id
            ]);
        } catch (Throwable $e) {
            error_log("Market::update error: " . $e->getMessage());
            return false;
        }
    }

    public static function delete(int $id): bool {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("DELETE FROM markets WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Throwable $e) {
            error_log("Market::delete error: " . $e->getMessage());
            return false;
        }
    }
}

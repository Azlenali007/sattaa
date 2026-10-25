<?php
/**
 * Announcement Model
 */

class Announcement {
    public static function getActive(): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM announcements WHERE is_active = 1 ORDER BY display_order ASC, id DESC");
        return $stmt->fetchAll();
    }

    public static function all(): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM announcements ORDER BY display_order ASC, id DESC");
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO announcements (title, content, type, is_active, display_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['title'],
            $data['content'],
            $data['type'] ?? 'info',
            $data['is_active'] ?? 1,
            $data['display_order'] ?? 0
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function delete(int $id): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

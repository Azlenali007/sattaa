<?php
/**
 * Setting Model
 */

class Setting {
    private static array $cache = [];

    public static function get(string $key, ?string $default = null): ?string {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            $val = $stmt->fetchColumn();
            if ($val !== false) {
                self::$cache[$key] = (string)$val;
                return self::$cache[$key];
            }
        } catch (Exception $e) {
            // fallback
        }

        return $default;
    }

    public static function set(string $key, ?string $value, string $group = 'general', ?string $description = null): bool {
        $pdo = Database::getConnection();
        self::$cache[$key] = $value ?? '';
        
        $stmt = $pdo->prepare("
            INSERT INTO settings (setting_key, setting_value, setting_group, description, updated_at)
            VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
            ON CONFLICT(setting_key) DO UPDATE SET 
                setting_value = excluded.setting_value,
                updated_at = CURRENT_TIMESTAMP
        ");
        return $stmt->execute([$key, $value, $group, $description]);
    }

    public static function all(): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM settings ORDER BY setting_group ASC, setting_key ASC");
        return $stmt->fetchAll();
    }
}

<?php
/**
 * Setting Model
 * Exclusively supports MySQL 8+ / MariaDB
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
            if ($val !== false && $val !== null) {
                self::$cache[$key] = (string)$val;
                return self::$cache[$key];
            }
        } catch (Throwable $e) {
            error_log("Setting::get('{$key}') error: " . $e->getMessage());
        }

        return $default;
    }

    public static function set(string $key, ?string $value, string $group = 'general', ?string $description = null): bool {
        self::$cache[$key] = $value ?? '';
        
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO settings (setting_key, setting_value, setting_group, description, updated_at)
                VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
                ON DUPLICATE KEY UPDATE 
                    setting_value = VALUES(setting_value),
                    setting_group = VALUES(setting_group),
                    description = COALESCE(VALUES(description), description),
                    updated_at = CURRENT_TIMESTAMP
            ");
            return $stmt->execute([$key, $value, $group, $description]);
        } catch (Throwable $e) {
            error_log("Setting::set('{$key}') error: " . $e->getMessage());
            return false;
        }
    }

    public static function all(): array {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->query("SELECT * FROM settings ORDER BY setting_group ASC, setting_key ASC");
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            error_log("Setting::all() error: " . $e->getMessage());
            return [];
        }
    }
}

<?php
/**
 * Admin Model
 */

class Admin {
    public static function findByUsername(string $username): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function authenticate(string $username, string $password): ?array {
        $admin = self::findByUsername($username);
        if (!$admin) {
            return null;
        }

        if ($admin['status'] !== 'active') {
            return null;
        }

        // Check lockout
        if (!empty($admin['lockout_until']) && strtotime($admin['lockout_until']) > time()) {
            return null;
        }

        if (password_verify($password, $admin['password_hash'])) {
            // Reset failed attempts & set last login
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("UPDATE admins SET failed_attempts = 0, lockout_until = NULL, last_login_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$admin['id']]);
            return $admin;
        }

        // Record failed attempt
        $pdo = Database::getConnection();
        $failed = $admin['failed_attempts'] + 1;
        $lockout = ($failed >= 5) ? date('Y-m-d H:i:s', time() + 900) : null; // 15 mins lock
        $stmt = $pdo->prepare("UPDATE admins SET failed_attempts = ?, lockout_until = ? WHERE id = ?");
        $stmt->execute([$failed, $lockout, $admin['id']]);

        return null;
    }

    public static function updatePassword(int $adminId, string $newPassword): bool {
        $pdo = Database::getConnection();
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE admins SET password_hash = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$hash, $adminId]);
    }

    public static function logActivity(?int $adminId, string $action, string $module, ?int $recordId = null, ?string $details = null): void {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO admin_activity_logs (admin_id, action, module, record_id, details, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $adminId,
                $action,
                $module,
                $recordId,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)
            ]);
        } catch (Exception $e) {
            error_log("Failed to write activity log: " . $e->getMessage());
        }
    }

    public static function getRecentLogs(int $limit = 10): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT l.*, a.username 
            FROM admin_activity_logs l 
            LEFT JOIN admins a ON l.admin_id = a.id 
            ORDER BY l.created_at DESC 
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

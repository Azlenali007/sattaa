<?php
/**
 * CLI Administrator Password Reset Utility
 * Usage: php bin/reset-admin.php [new_password]
 * Secure server-side tool to restore administrator access
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Access Denied: This utility must be executed from the server command line (CLI) only.\n");
}

require_once dirname(__DIR__) . '/app/config/app.php';

$newPassword = $argv[1] ?? 'AdminPassword@2025';
$username = 'admin';

echo "=== Satka Matka Super Admin Password Reset ===\n";

try {
    $pdo = Database::getConnection();
    
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id, username, email FROM admins WHERE id = 1 OR username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    $hash = password_hash($newPassword, PASSWORD_BCRYPT);

    if ($admin) {
        $update = $pdo->prepare("
            UPDATE admins 
            SET password_hash = ?, failed_attempts = 0, lockout_until = NULL, status = 'active', updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $update->execute([$hash, $admin['id']]);
        echo "[SUCCESS] Super Admin ({$admin['username']}) password has been reset successfully!\n";
    } else {
        $insert = $pdo->prepare("
            INSERT INTO admins (id, username, email, password_hash, full_name, role, status, failed_attempts, lockout_until, created_at, updated_at)
            VALUES (1, 'admin', 'admin@satkamatka.com', ?, 'Super Administrator', 'super_admin', 'active', 0, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");
        $insert->execute([$hash]);
        echo "[SUCCESS] Super Admin account created successfully!\n";
    }

    echo "Username: admin\n";
    echo "Password: " . $newPassword . "\n";
    echo "Login URL: " . url('admin/login.php') . "\n";
    echo "=============================================\n";
} catch (Exception $e) {
    echo "[ERROR] Failed to reset administrator password: " . $e->getMessage() . "\n";
    exit(1);
}

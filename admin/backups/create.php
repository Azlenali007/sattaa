<?php
/**
 * Admin: Trigger Database Backup
 */
require_once __DIR__ . '/../includes/auth-check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $res = BackupService::createBackup();
    Admin::logActivity($currentAdmin['id'], 'Create Backup', 'Backups', null, "Created snapshot {$res['filename']}");
    set_flash('success', "Database snapshot '{$res['filename']}' generated successfully!");
}

redirect('/admin/backups/index.php');

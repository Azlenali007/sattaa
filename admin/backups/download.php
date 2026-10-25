<?php
/**
 * Admin: Download Database Backup
 */
require_once __DIR__ . '/../includes/auth-check.php';

$filename = basename($_GET['file'] ?? '');
$filepath = STORAGE_PATH . '/backups/' . $filename;

if (!empty($filename) && file_exists($filepath) && str_ends_with($filename, '.sql')) {
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit;
}

set_flash('error', 'Requested backup file could not be found.');
redirect('/admin/backups/index.php');

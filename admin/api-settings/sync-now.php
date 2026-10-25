<?php
/**
 * Admin: Immediate API Synchronization Trigger
 */
require_once __DIR__ . '/../includes/auth-check.php';

$res = ApiSyncService::runSync();

if ($res['success']) {
    set_flash('success', $res['message']);
} else {
    set_flash('error', 'API Sync: ' . $res['message']);
}

redirect('/admin/api-settings/index.php');

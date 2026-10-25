<?php
/**
 * Admin: Toggle Market Status or Live State
 */
require_once __DIR__ . '/../includes/auth-check.php';

$id = (int)($_GET['id'] ?? 0);
$toggle = $_GET['toggle'] ?? 'status';
$market = Market::findById($id);

if ($market) {
    if ($toggle === 'status') {
        $newStatus = $market['status'] === 'active' ? 'inactive' : 'active';
        Market::update($id, array_merge($market, ['status' => $newStatus]));
        Admin::logActivity($currentAdmin['id'], 'Toggle Market Status', 'Markets', $id, "Toggled status to {$newStatus}");
        set_flash('success', "Market '{$market['name']}' status updated to {$newStatus}.");
    } elseif ($toggle === 'live') {
        $newLive = !empty($market['is_live']) ? 0 : 1;
        Market::update($id, array_merge($market, ['is_live' => $newLive]));
        Admin::logActivity($currentAdmin['id'], 'Toggle Market Live', 'Markets', $id, "Toggled live indicator to {$newLive}");
        set_flash('success', "Market '{$market['name']}' live status updated.");
    }
}

redirect('/admin/markets/index.php');

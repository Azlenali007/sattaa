<?php
/**
 * Admin: Delete Market
 */
require_once __DIR__ . '/../includes/auth-check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $market = Market::findById($id);

    if ($market) {
        Market::delete($id);
        Admin::logActivity($currentAdmin['id'], 'Delete Market', 'Markets', $id, "Deleted market {$market['name']}");
        set_flash('success', "Market '{$market['name']}' was successfully deleted.");
    } else {
        set_flash('error', 'Market record not found.');
    }
}

redirect('/admin/markets/index.php');

<?php
/**
 * Admin: Delete Result
 */
require_once __DIR__ . '/../includes/auth-check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $id = (int)($_POST['id'] ?? 0);
    Result::delete($id);
    Admin::logActivity($currentAdmin['id'], 'Delete Result', 'Results', $id, "Deleted result ID {$id}");
    set_flash('success', 'Result record deleted successfully.');
}

redirect('/admin/results/index.php');

<?php
/**
 * Admin: Cancel Scheduled Publication
 */
require_once __DIR__ . '/../includes/auth-check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("UPDATE scheduled_publications SET status = 'cancelled', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$id]);
    Admin::logActivity($currentAdmin['id'], 'Cancel Schedule', 'Scheduled', $id, "Cancelled schedule ID {$id}");
    set_flash('success', 'Scheduled publication cancelled.');
}

redirect('/admin/scheduled/index.php');

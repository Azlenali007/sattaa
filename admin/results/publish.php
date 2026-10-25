<?php
/**
 * Admin: Toggle Result Status
 */
require_once __DIR__ . '/../includes/auth-check.php';

$id = (int)($_GET['id'] ?? 0);
$pdo = Database::getConnection();
$stmt = $pdo->prepare("SELECT * FROM results WHERE id = ?");
$stmt->execute([$id]);
$res = $stmt->fetch();

if ($res) {
    $newStatus = $res['status'] === 'published' ? 'draft' : 'published';
    $uStmt = $pdo->prepare("UPDATE results SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $uStmt->execute([$newStatus, $id]);
    Admin::logActivity($currentAdmin['id'], 'Toggle Result Status', 'Results', $id, "Status changed to {$newStatus}");
    set_flash('success', "Result status updated to {$newStatus}.");
}

redirect('/admin/results/index.php');

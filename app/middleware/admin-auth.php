<?php
/**
 * Admin Authentication Middleware
 */

function require_admin_auth(): array {
    if (empty($_SESSION['admin_user'])) {
        $returnUrl = urlencode($_SERVER['REQUEST_URI'] ?? '/admin/dashboard.php');
        redirect('/admin/login.php?redirect=' . $returnUrl);
    }
    return $_SESSION['admin_user'];
}

function current_admin(): ?array {
    return $_SESSION['admin_user'] ?? null;
}

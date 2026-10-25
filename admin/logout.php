<?php
/**
 * Admin Logout
 * Destroys session, logs activity, and clears session cookie
 */
require_once __DIR__ . '/../app/config/app.php';

if (!empty($_SESSION['admin_user'])) {
    Admin::logActivity($_SESSION['admin_user']['id'], 'Logout', 'Auth', null, 'Super Admin logged out');
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

redirect('/admin/login.php');

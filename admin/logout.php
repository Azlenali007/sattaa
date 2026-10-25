<?php
/**
 * Admin Logout
 */
require_once __DIR__ . '/../app/config/app.php';

if (!empty($_SESSION['admin_user'])) {
    Admin::logActivity($_SESSION['admin_user']['id'], 'Logout', 'Auth', null, 'Super Admin logged out');
}

unset($_SESSION['admin_user']);
session_destroy();

redirect('/admin/login.php');

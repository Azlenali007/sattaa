<?php
/**
 * Admin Index Redirector
 */
require_once __DIR__ . '/../app/config/app.php';

if (!empty($_SESSION['admin_user'])) {
    redirect('/admin/dashboard.php');
} else {
    redirect('/admin/login.php');
}

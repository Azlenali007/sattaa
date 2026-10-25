<?php
/**
 * Application Bootstrap
 */

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/database.php';

// Safe session startup
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// Load helpers
require_once APP_PATH . '/helpers/security.php';
require_once APP_PATH . '/helpers/common.php';
require_once APP_PATH . '/helpers/url.php';
require_once APP_PATH . '/helpers/validation.php';
require_once APP_PATH . '/helpers/pagination.php';
require_once APP_PATH . '/helpers/seo.php';

// Load models
require_once APP_PATH . '/models/Market.php';
require_once APP_PATH . '/models/Result.php';
require_once APP_PATH . '/models/Admin.php';
require_once APP_PATH . '/models/Setting.php';
require_once APP_PATH . '/models/Announcement.php';

// Load services
require_once APP_PATH . '/services/ResultService.php';
require_once APP_PATH . '/services/ApiClient.php';
require_once APP_PATH . '/services/ApiSyncService.php';
require_once APP_PATH . '/services/SeoService.php';
require_once APP_PATH . '/services/BackupService.php';

// Load middleware
require_once APP_PATH . '/middleware/csrf.php';
require_once APP_PATH . '/middleware/admin-auth.php';

// Set application timezone
date_default_timezone_set(Setting::get('timezone', 'Asia/Kolkata'));

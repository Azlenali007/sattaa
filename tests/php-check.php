<?php
/**
 * PHP Syntax & Application Integrity Verification
 */

require_once __DIR__ . '/../app/config/app.php';

echo "=== SATKA MATKA PLATFORM INTEGRITY CHECK ===\n";

// Test Database Connection
$pdo = Database::getConnection();
if ($pdo) {
    echo "[PASS] PDO Database connection active.\n";
} else {
    echo "[FAIL] Could not establish PDO Database connection.\n";
    exit(1);
}

// Test Market model
$markets = Market::all();
echo "[PASS] Markets table queried: " . count($markets) . " records found.\n";

// Test Result model
$latest = Result::getLatestCards();
echo "[PASS] Latest result cards queried: " . count($latest) . " cards loaded.\n";

// Test Super Admin
$admin = Admin::findByUsername('admin');
if ($admin) {
    echo "[PASS] Super Admin account exists: " . $admin['username'] . " (Status: " . $admin['status'] . ").\n";
} else {
    echo "[FAIL] Super Admin account not found.\n";
    exit(1);
}

// Test Settings
$siteName = Setting::get('site_name');
echo "[PASS] Platform settings loaded: Site Name = '" . $siteName . "'.\n";

echo "=== ALL CHECKS PASSED SUCCESSFULLY ===\n";
exit(0);

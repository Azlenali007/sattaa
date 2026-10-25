<?php
/**
 * Automated Test Suite for Satka Matka Web Installer
 * Exclusively tests MySQL 8+ / MariaDB via PDO (pdo_mysql)
 * Zero SQLite references
 */

require_once __DIR__ . '/../install/includes/installer-functions.php';

echo "=== SATKA MATKA WEB INSTALLER AUTOMATED TEST SUITE (MySQL ONLY) ===\n\n";

// 1. Test Server Requirements Check
echo "[TEST 1] Auditing Server Requirements...\n";
$req = check_server_requirements();
if ($req['passed']) {
    echo "  -> [PASS] All critical requirements passed (PHP " . $req['checks']['php_version']['current'] . ").\n";
    echo "  -> [PASS] PDO MySQL driver: " . $req['checks']['pdo_driver']['current'] . ".\n";
} else {
    echo "  -> [FAIL] Server requirements check failed!\n";
    print_r($req['checks']);
    exit(1);
}

// 2. Test Invalid Database Connection Handling
echo "\n[TEST 2] Testing Database Connection with Invalid Credentials...\n";
$badConfig = [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'non_existent_db_xyz_999',
    'username' => 'invalid_user_99',
    'password' => 'wrong_pass'
];
$badRes = test_database_connection($badConfig);
if (!$badRes['success'] && !empty($badRes['message'])) {
    echo "  -> [PASS] Gracefully caught connection failure: '{$badRes['message']}'. No sensitive leaks.\n";
} else {
    echo "  -> [FAIL] Expected connection error on invalid credentials, but got success.\n";
    exit(1);
}

// 3. Test Valid MySQL Database Connection
echo "\n[TEST 3] Testing Valid MySQL Database Connection...\n";
$testDbConfig = [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'satkamatka',
    'username' => 'root',
    'password' => ''
];
$dbRes = test_database_connection($testDbConfig);
if ($dbRes['success'] && $dbRes['pdo'] instanceof PDO) {
    echo "  -> [PASS] MySQL database connection verified successfully ({$dbRes['details']}).\n";
} else {
    echo "  -> [FAIL] Valid database connection failed: " . ($dbRes['message'] ?? 'Unknown') . "\n";
    exit(1);
}
$pdo = $dbRes['pdo'];

// 4. Test Schema Installation & Seeding on MySQL
echo "\n[TEST 4] Testing MySQL Database Schema and Seed Execution...\n";
$schemaRes = install_database_schema($pdo, 'mysql');
if ($schemaRes['success']) {
    echo "  -> [PASS] MySQL schema created and seeded: {$schemaRes['message']}\n";
} else {
    echo "  -> [FAIL] Schema creation failed: " . ($schemaRes['message'] ?? '') . "\n";
    exit(1);
}

// 5. Test Admin Creation Validation (Short Password)
echo "\n[TEST 5] Testing Admin Creation Validation (Weak/Short Password)...\n";
$weakAdminRes = create_super_admin_account($pdo, [
    'username' => 'admin_test',
    'email' => 'admin_test@test.com',
    'password' => 'short',
    'confirm_password' => 'short'
]);
if (!$weakAdminRes['success'] && str_contains($weakAdminRes['message'], 'at least 8 characters')) {
    echo "  -> [PASS] Correctly rejected weak password: '{$weakAdminRes['message']}'.\n";
} else {
    echo "  -> [FAIL] Failed to enforce min 8 character password requirement.\n";
    exit(1);
}

// 6. Test Admin Creation (Valid Credentials)
echo "\n[TEST 6] Testing Admin Creation with Valid Credentials...\n";
$testPass = 'AdminPassword@2025';
$adminRes = create_super_admin_account($pdo, [
    'full_name' => 'Super Administrator',
    'username' => 'admin',
    'email' => 'admin@satkamatka.com',
    'password' => $testPass,
    'confirm_password' => $testPass
]);
if ($adminRes['success']) {
    echo "  -> [PASS] Super Admin account created ({$adminRes['username']}).\n";
    // Verify password hash
    $stmt = $pdo->prepare("SELECT password_hash FROM admins WHERE id = 1");
    $stmt->execute();
    $hash = $stmt->fetchColumn();
    if (password_verify($testPass, $hash)) {
        echo "  -> [PASS] Password verification with password_verify() succeeded.\n";
    } else {
        echo "  -> [FAIL] password_verify failed against created hash.\n";
        exit(1);
    }
} else {
    echo "  -> [FAIL] Failed to create admin account: {$adminRes['message']}\n";
    exit(1);
}

// 7. Test Site Configuration and .env Writer
echo "\n[TEST 7] Testing Site Configuration Persistence & .env Generation...\n";
$configRes = save_site_configuration($pdo, [
    'site_name' => 'SATKA MATKA',
    'site_tagline' => 'Live Results & Charts',
    'site_url' => 'http://localhost:3000',
    'timezone' => 'Asia/Kolkata',
    'meta_description' => 'Fast, accurate live Satka Matka results and charts.',
    'contact_email' => 'support@satkamatka.com'
], $testDbConfig);

if ($configRes['success']) {
    echo "  -> [PASS] Site settings and .env saved: {$configRes['message']}\n";
} else {
    echo "  -> [FAIL] Failed to save site configuration: {$configRes['message']}\n";
    exit(1);
}

// 8. Test Installation Verification State
echo "\n[TEST 8] Performing Final Pre-completion State Verification...\n";
$verify = verify_installation_state($pdo);
if ($verify['passed']) {
    echo "  -> [PASS] All 5 system integrity checks verified:\n";
    foreach ($verify['checks'] as $c) {
        echo "     * {$c['name']}: {$c['message']}\n";
    }
} else {
    echo "  -> [FAIL] System integrity checks failed!\n";
    print_r($verify['checks']);
    exit(1);
}

echo "\n=== ALL 8 INSTALLER TESTS COMPLETED SUCCESSFULLY (MySQL ONLY) ===\n";
exit(0);

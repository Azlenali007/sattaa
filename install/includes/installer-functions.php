<?php
/**
 * Satka Matka Web Installer Engine
 * Exclusively supports MySQL 8+ / MariaDB via PDO (pdo_mysql)
 * Zero SQLite code or fallback
 */

define('INSTALLER_LOCK_FILE', dirname(__DIR__, 2) . '/storage/installed.lock');
define('INSTALLER_ROOT_PATH', dirname(__DIR__, 2));

if (!function_exists('e')) {
    function e(?string $value): string {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

// Initialize session securely
function installer_init(): void {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_samesite', 'Lax');
        session_start();
    }

    if (empty($_SESSION['installer_csrf'])) {
        $_SESSION['installer_csrf'] = bin2hex(random_bytes(32));
    }
}

function installer_csrf_token(): string {
    return $_SESSION['installer_csrf'] ?? '';
}

function installer_csrf_field(): string {
    return '<input type="hidden" name="installer_csrf" value="' . htmlspecialchars(installer_csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function installer_verify_csrf(): bool {
    $token = $_POST['installer_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return !empty($_SESSION['installer_csrf']) && hash_equals($_SESSION['installer_csrf'], $token);
}

function is_installed(): bool {
    return file_exists(INSTALLER_LOCK_FILE);
}

function lock_installer(array $meta = []): bool {
    $storageDir = dirname(INSTALLER_LOCK_FILE);
    if (!is_dir($storageDir)) {
        @mkdir($storageDir, 0755, true);
    }

    $payload = [
        'installed_at' => date('Y-m-d H:i:s'),
        'database_engine' => 'MySQL 8+ (InnoDB / utf8mb4)',
        'php_version' => PHP_VERSION,
        'app_version' => '1.0.0',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        'meta' => $meta
    ];

    $written = @file_put_contents(INSTALLER_LOCK_FILE, json_encode($payload, JSON_PRETTY_PRINT));
    
    // Secondary lock in app/config for redundancy
    $configLock = INSTALLER_ROOT_PATH . '/app/config/installed.lock';
    @file_put_contents($configLock, json_encode($payload, JSON_PRETTY_PRINT));

    return $written !== false;
}

function render_locked_screen(): void {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <title>Installation Locked | SATKA MATKA</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
      <script src="https://cdn.tailwindcss.com"></script>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="/install/assets/installer.css">
    </head>
    <body class="bg-[#FAF9FF] text-[#191747] min-h-screen flex items-center justify-center p-4 font-sans">
      <div class="max-w-lg w-full bg-white rounded-3xl border border-[#EAE8F5] p-8 sm:p-10 shadow-xl text-center">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center mx-auto mb-5">
          <svg class="w-8 h-8 fill-none stroke-current" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
        </div>
        <h1 class="text-2xl font-black text-[#191747]">Installation Locked</h1>
        <p class="text-xs text-[#737493] mt-2 leading-relaxed">
          The Satka Matka platform has already been installed and securely locked. For security purposes, the installer cannot be accessed or re-run while the lock file exists.
        </p>

        <div class="my-6 p-4 rounded-2xl bg-[#FAF9FF] border border-[#EAE8F5] text-left text-xs space-y-2">
          <div class="font-bold text-[#191747]">To reinstall the application:</div>
          <ol class="list-decimal list-inside text-[#737493] space-y-1">
            <li>Connect to your server via SSH, FTP, or cPanel File Manager.</li>
            <li>Delete the file: <code class="bg-white px-1.5 py-0.5 rounded border border-[#EAE8F5] text-[#6425D9] font-mono">storage/installed.lock</code></li>
            <li>Refresh this page in your web browser.</li>
          </ol>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2">
          <a href="/index.php" class="py-3 px-4 rounded-xl bg-[#FAF9FF] hover:bg-[#F3EDFF] text-[#6425D9] text-xs font-bold border border-[#EAE8F5] transition text-center">
            &larr; Public Website
          </a>
          <a href="/admin/login.php" class="py-3 px-4 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold transition text-center shadow-xs">
            Admin Login &rarr;
          </a>
        </div>
      </div>
    </body>
    </html>
    <?php
    exit;
}

function check_installer_guard(): void {
    if (is_installed()) {
        render_locked_screen();
    }
}

function check_server_requirements(): array {
    $results = [];

    // 1. PHP Version >= 8.0
    $phpVersion = PHP_VERSION;
    $phpPassed = version_compare($phpVersion, '8.0.0', '>=');
    $results['php_version'] = [
        'name' => 'PHP Version',
        'required' => '>= 8.0.0',
        'current' => $phpVersion,
        'passed' => $phpPassed,
        'critical' => true,
        'help' => 'Upgrade your server PHP version to 8.0, 8.2 or 8.3 via your hosting control panel.'
    ];

    // 2. PDO Extension
    $pdoPassed = extension_loaded('pdo');
    $results['ext_pdo'] = [
        'name' => 'PDO Core Extension',
        'required' => 'Enabled',
        'current' => $pdoPassed ? 'Enabled' : 'Missing',
        'passed' => $pdoPassed,
        'critical' => true,
        'help' => 'Enable the PDO extension in php.ini or PHP extensions in cPanel.'
    ];

    // 3. PDO MySQL Extension (Strictly MySQL)
    $hasPdoMysql = extension_loaded('pdo_mysql');
    $results['pdo_driver'] = [
        'name' => 'PDO MySQL Driver (pdo_mysql)',
        'required' => 'pdo_mysql Enabled',
        'current' => $hasPdoMysql ? 'pdo_mysql Active' : 'Missing',
        'passed' => $hasPdoMysql,
        'critical' => true,
        'help' => 'Enable the pdo_mysql PHP extension to connect to MySQL 8+ database.'
    ];

    // 4. JSON Extension
    $jsonPassed = extension_loaded('json');
    $results['ext_json'] = [
        'name' => 'JSON Extension',
        'required' => 'Enabled',
        'current' => $jsonPassed ? 'Enabled' : 'Missing',
        'passed' => $jsonPassed,
        'critical' => true,
        'help' => 'Install and enable the PHP JSON extension.'
    ];

    // 5. Sessions
    $sessionPassed = extension_loaded('session') && (session_status() === PHP_SESSION_ACTIVE || session_status() === PHP_SESSION_NONE);
    $results['session'] = [
        'name' => 'Native PHP Sessions',
        'required' => 'Operational',
        'current' => $sessionPassed ? 'Operational' : 'Disabled',
        'passed' => $sessionPassed,
        'critical' => true,
        'help' => 'Ensure native PHP sessions are enabled and session save path is writable.'
    ];

    // 6. Ctype & OpenSSL
    $securityExtPassed = extension_loaded('ctype') && extension_loaded('openssl');
    $results['security_ext'] = [
        'name' => 'Cryptographic Extensions (OpenSSL, Ctype)',
        'required' => 'Enabled',
        'current' => $securityExtPassed ? 'Operational' : 'Missing',
        'passed' => $securityExtPassed,
        'critical' => true,
        'help' => 'Enable openssl and ctype extensions for secure token hashing.'
    ];

    // Directory Permissions Check
    $directories = [
        'storage' => INSTALLER_ROOT_PATH . '/storage',
        'storage_logs' => INSTALLER_ROOT_PATH . '/storage/logs',
        'storage_backups' => INSTALLER_ROOT_PATH . '/storage/backups',
        'storage_cache' => INSTALLER_ROOT_PATH . '/storage/cache',
        'app_config' => INSTALLER_ROOT_PATH . '/app/config',
        'root_env' => INSTALLER_ROOT_PATH,
    ];

    $dirLabels = [
        'storage' => 'storage/',
        'storage_logs' => 'storage/logs/',
        'storage_backups' => 'storage/backups/',
        'storage_cache' => 'storage/cache/',
        'app_config' => 'app/config/',
        'root_env' => 'Project Root (for .env write)',
    ];

    $allDirsWritable = true;
    foreach ($directories as $key => $path) {
        if (!is_dir($path) && $key !== 'root_env') {
            @mkdir($path, 0755, true);
        }
        $isWritable = is_writable($path);
        if (!$isWritable) {
            $allDirsWritable = false;
        }
        $results['dir_' . $key] = [
            'name' => 'Directory Permission: ' . $dirLabels[$key],
            'required' => 'Writable (0755 / 0775)',
            'current' => $isWritable ? 'Writable' : 'Not Writable',
            'passed' => $isWritable,
            'critical' => true,
            'help' => 'Set permissions on ' . $dirLabels[$key] . ' to 0755 or 0775 via chmod or FTP.'
        ];
    }

    $allPassed = true;
    foreach ($results as $item) {
        if (!empty($item['critical']) && !$item['passed']) {
            $allPassed = false;
            break;
        }
    }

    return [
        'passed' => $allPassed,
        'checks' => $results
    ];
}

function test_database_connection(array $config): array {
    $host = trim($config['host'] ?? '127.0.0.1');
    $port = (int)($config['port'] ?? 3306);
    $database = trim($config['database'] ?? '');
    $username = trim($config['username'] ?? '');
    $password = (string)($config['password'] ?? '');

    if (empty($host)) {
        return ['success' => false, 'message' => 'MySQL host cannot be empty.', 'details' => 'Please provide a valid database host (e.g. 127.0.0.1 or localhost).', 'pdo' => null];
    }
    if (empty($database)) {
        return ['success' => false, 'message' => 'MySQL database name cannot be empty.', 'details' => 'Please provide the name of your MySQL database.', 'pdo' => null];
    }

    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]);

        $serverVer = $pdo->query("SELECT VERSION()")->fetchColumn();

        return [
            'success' => true,
            'message' => 'MySQL database connection established successfully.',
            'details' => "Connected to MySQL `{$database}` on {$host}:{$port} (Server: {$serverVer})",
            'pdo' => $pdo
        ];
    } catch (PDOException $e) {
        $errCode = $e->getCode();
        $errMsg = $e->getMessage();

        // If database does not exist yet (error 1049), attempt auto-provisioning
        if ($errCode === 1049 || str_contains($errMsg, 'Unknown database')) {
            try {
                $rootDsn = "mysql:host={$host};port={$port};charset=utf8mb4";
                $rootPdo = new PDO($rootDsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 5
                ]);

                $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

                $pdo = new PDO("mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4", $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);

                return [
                    'success' => true,
                    'message' => "MySQL database '{$database}' was created and connected successfully.",
                    'details' => "Provisioned new database `{$database}` on {$host}.",
                    'pdo' => $pdo
                ];
            } catch (Exception $subErr) {
                return [
                    'success' => false,
                    'message' => "Database '{$database}' does not exist on MySQL server and could not be created automatically.",
                    'details' => "Please create the database '{$database}' through your hosting control panel (cPanel, phpMyAdmin) and try again.",
                    'pdo' => null
                ];
            }
        }

        $friendlyReason = 'Please verify that MySQL is active and your host, port, username, and password are correct.';
        if (str_contains($errMsg, 'Access denied')) {
            $friendlyReason = "Access denied for user '{$username}'. Please verify your database username and password.";
        } elseif (str_contains($errMsg, 'Connection refused') || str_contains($errMsg, 'actively refused')) {
            $friendlyReason = "Connection refused on {$host}:{$port}. Ensure the MySQL server process is running.";
        }

        return [
            'success' => false,
            'message' => 'Could not connect to MySQL server.',
            'details' => $friendlyReason,
            'pdo' => null
        ];
    }
}

function install_database_schema(PDO $pdo, string $driver = 'mysql'): array {
    $schemaFile = INSTALLER_ROOT_PATH . '/database/schema.sql';
    $seedFile = INSTALLER_ROOT_PATH . '/database/seed.sql';

    if (!file_exists($schemaFile)) {
        return ['success' => false, 'message' => 'Schema file database/schema.sql not found.'];
    }

    try {
        // Read and execute schema
        $schemaSql = file_get_contents($schemaFile);
        $pdo->exec($schemaSql);

        // Read and execute seed data
        if (file_exists($seedFile)) {
            $seedSql = file_get_contents($seedFile);
            $pdo->exec($seedSql);
        }

        return ['success' => true, 'message' => 'MySQL database tables and initial records installed successfully.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'MySQL error during schema installation: ' . $e->getMessage()];
    }
}

function create_super_admin_account(PDO $pdo, array $data): array {
    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = (string)($data['password'] ?? '');
    $confirmPassword = (string)($data['confirm_password'] ?? '');
    $fullName = trim($data['full_name'] ?? 'Super Administrator');

    if (empty($username) || strlen($username) < 3 || strlen($username) > 30) {
        return ['success' => false, 'message' => 'Username must be between 3 and 30 characters.'];
    }
    if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $username)) {
        return ['success' => false, 'message' => 'Username may only contain letters, numbers, underscores, and dashes.'];
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Please provide a valid administrator email address.'];
    }
    if (strlen($password) < 8) {
        return ['success' => false, 'message' => 'Password must be at least 8 characters long.'];
    }
    if ($password !== $confirmPassword) {
        return ['success' => false, 'message' => 'Password confirmation does not match.'];
    }

    try {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        // Remove prior administrator record
        $del = $pdo->prepare("DELETE FROM admins WHERE id = 1 OR username = ? OR email = ?");
        $del->execute([$username, $email]);

        $stmt = $pdo->prepare("
            INSERT INTO admins (id, username, email, password_hash, full_name, role, status, failed_attempts, lockout_until, created_at, updated_at)
            VALUES (1, ?, ?, ?, ?, 'super_admin', 'active', 0, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([$username, $email, $hash, $fullName]);

        return [
            'success' => true,
            'message' => 'First Super Administrator account created successfully.',
            'admin_id' => 1,
            'username' => $username,
            'email' => $email
        ];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to create administrator account in MySQL: ' . $e->getMessage()];
    }
}

function save_site_configuration(PDO $pdo, array $siteData, array $dbConfig): array {
    $siteName = trim($siteData['site_name'] ?? 'SATKA MATKA');
    $siteTagline = trim($siteData['site_tagline'] ?? 'Live Results & Charts');
    $siteUrl = rtrim(trim($siteData['site_url'] ?? ''), '/');
    $timezone = trim($siteData['timezone'] ?? 'Asia/Kolkata');
    $description = trim($siteData['meta_description'] ?? 'Get the latest Satka Matka results, market charts and historical data all in one place.');
    $contactEmail = trim($siteData['contact_email'] ?? 'support@satkamatka.com');

    if (empty($siteName)) {
        return ['success' => false, 'message' => 'Website name cannot be empty.'];
    }
    if (empty($siteUrl) || !filter_var($siteUrl, FILTER_VALIDATE_URL)) {
        return ['success' => false, 'message' => 'Please enter a valid website URL (including http:// or https://).'];
    }

    try {
        // Save settings in MySQL with ON DUPLICATE KEY UPDATE
        $settingsToSave = [
            'site_name' => $siteName,
            'site_tagline' => $siteTagline,
            'site_url' => $siteUrl,
            'timezone' => $timezone,
            'meta_description' => $description,
            'contact_email' => $contactEmail,
            'installed_date' => date('Y-m-d H:i:s')
        ];

        $stmt = $pdo->prepare("
            INSERT INTO settings (setting_key, setting_value, setting_group, description, updated_at)
            VALUES (?, ?, 'general', 'Installer Configured', CURRENT_TIMESTAMP)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP
        ");

        foreach ($settingsToSave as $k => $v) {
            $stmt->execute([$k, $v]);
        }

        // Generate .env file strictly with MySQL configuration
        $envPath = INSTALLER_ROOT_PATH . '/.env';
        $dbHost = $dbConfig['host'] ?? '127.0.0.1';
        $dbPort = $dbConfig['port'] ?? '3306';
        $dbName = $dbConfig['database'] ?? 'satkamatka';
        $dbUser = $dbConfig['username'] ?? 'root';
        $dbPass = $dbConfig['password'] ?? '';

        $envContent = "# Database Configuration (MySQL 8+ only)\n";
        $envContent .= "DB_CONNECTION=mysql\n";
        $envContent .= "DB_HOST={$dbHost}\n";
        $envContent .= "DB_PORT={$dbPort}\n";
        $envContent .= "DB_DATABASE={$dbName}\n";
        $envContent .= "DB_USERNAME={$dbUser}\n";
        $envContent .= "DB_PASSWORD={$dbPass}\n\n";

        $envContent .= "# Application Settings\n";
        $envContent .= "APP_ENV=production\n";
        $envContent .= "APP_DEBUG=false\n";
        $envContent .= "APP_URL={$siteUrl}\n";
        $envContent .= "APP_TIMEZONE={$timezone}\n\n";

        $envContent .= "# External API Integration\n";
        $envContent .= "API_PROVIDER_URL=https://api.satkamatka-feed.net/v1/live\n";
        $envContent .= "API_PROVIDER_KEY=sm_live_sec_99342\n";

        file_put_contents($envPath, $envContent);

        return ['success' => true, 'message' => 'Website configuration and .env file saved successfully.'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Configuration save error: ' . $e->getMessage()];
    }
}

function verify_installation_state(PDO $pdo): array {
    $checks = [];

    // 1. Check Tables
    $requiredTables = ['admins', 'markets', 'results', 'scheduled_publications', 'api_configs', 'settings', 'announcements'];
    $tablesOk = true;
    foreach ($requiredTables as $tbl) {
        try {
            $pdo->query("SELECT 1 FROM {$tbl} LIMIT 1");
        } catch (Exception $e) {
            $tablesOk = false;
            break;
        }
    }
    $checks['tables'] = [
        'name' => 'MySQL Database Schema & Tables',
        'passed' => $tablesOk,
        'message' => $tablesOk ? 'All required MySQL tables exist and are accessible.' : 'One or more database tables are missing.'
    ];

    // 2. Check Admin Account
    try {
        $admin = $pdo->query("SELECT * FROM admins WHERE id = 1 AND status = 'active'")->fetch(PDO::FETCH_ASSOC);
        $adminOk = !empty($admin) && !empty($admin['username']) && !empty($admin['password_hash']);
    } catch (Exception $e) {
        $adminOk = false;
    }
    $checks['admin'] = [
        'name' => 'First Super Administrator',
        'passed' => $adminOk,
        'message' => $adminOk ? "Super Admin account verified (username: {$admin['username']})." : 'Administrator account could not be verified.'
    ];

    // 3. Check Markets
    try {
        $marketCount = (int)$pdo->query("SELECT COUNT(*) FROM markets")->fetchColumn();
        $marketsOk = $marketCount >= 1;
    } catch (Exception $e) {
        $marketsOk = false;
    }
    $checks['markets'] = [
        'name' => 'Markets & Schedule Configuration',
        'passed' => $marketsOk,
        'message' => $marketsOk ? "Verified {$marketCount} active daily markets initialized." : 'No markets configured.'
    ];

    // 4. Check Settings
    try {
        $siteName = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'site_name'")->fetchColumn();
        $settingsOk = !empty($siteName);
    } catch (Exception $e) {
        $settingsOk = false;
    }
    $checks['settings'] = [
        'name' => 'Platform Settings',
        'passed' => $settingsOk,
        'message' => $settingsOk ? "Platform settings verified ({$siteName})." : 'Site settings could not be retrieved.'
    ];

    // 5. Check Environment File
    $envExists = file_exists(INSTALLER_ROOT_PATH . '/.env');
    $checks['env_file'] = [
        'name' => 'Configuration File (.env)',
        'passed' => $envExists,
        'message' => $envExists ? 'Application environment configuration is readable.' : '.env file missing.'
    ];

    $allPassed = true;
    foreach ($checks as $c) {
        if (!$c['passed']) {
            $allPassed = false;
            break;
        }
    }

    return [
        'passed' => $allPassed,
        'checks' => $checks
    ];
}

<?php
/**
 * Satka Matka Web Installer Engine
 * Secure helper functions for server audit, database setup, and lock controls
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
        'php_version' => PHP_VERSION,
        'app_version' => '1.0.0',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        'meta' => $meta
    ];

    $written = @file_put_contents(INSTALLER_LOCK_FILE, json_encode($payload, JSON_PRETTY_PRINT));
    
    // Also create secondary lock in app/config for redundancy
    $configLock = INSTALLER_ROOT_PATH . '/app/config/installed.lock';
    @file_put_contents($configLock, json_encode($payload, JSON_PRETTY_PRINT));

    return $written !== false;
}

function render_locked_screen(): void {
    $pageTitle = 'Installation Locked';
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
        'name' => 'PDO Extension',
        'required' => 'Enabled',
        'current' => $pdoPassed ? 'Enabled' : 'Missing',
        'passed' => $pdoPassed,
        'critical' => true,
        'help' => 'Enable the PDO extension in php.ini or PHP extensions in cPanel.'
    ];

    // 3. PDO MySQL or PDO SQLite
    $pdoDrivers = extension_loaded('pdo') ? PDO::getAvailableDrivers() : [];
    $hasPdoMysql = in_array('mysql', $pdoDrivers, true);
    $hasPdoSqlite = in_array('sqlite', $pdoDrivers, true);
    $dbDriverPassed = $hasPdoMysql || $hasPdoSqlite;
    $results['pdo_driver'] = [
        'name' => 'PDO Database Driver (MySQL / SQLite)',
        'required' => 'pdo_mysql (or pdo_sqlite)',
        'current' => $dbDriverPassed ? implode(', ', $pdoDrivers) : 'None',
        'passed' => $dbDriverPassed,
        'critical' => true,
        'help' => 'Enable the pdo_mysql extension for MySQL database support.'
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
        'name' => 'Session Support',
        'required' => 'Active',
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
    $driver = $config['driver'] ?? 'mysql';
    $host = trim($config['host'] ?? '127.0.0.1');
    $port = (int)($config['port'] ?? 3306);
    $database = trim($config['database'] ?? '');
    $username = trim($config['username'] ?? '');
    $password = (string)($config['password'] ?? '');

    if ($driver === 'sqlite') {
        $storageDir = INSTALLER_ROOT_PATH . '/storage';
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0755, true);
        }
        $dbPath = $storageDir . '/satkamatka.sqlite';
        try {
            $pdo = new PDO("sqlite:" . $dbPath, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $pdo->query("SELECT 1");
            return [
                'success' => true,
                'message' => 'SQLite connection verified successfully.',
                'details' => 'SQLite database initialized at storage/satkamatka.sqlite',
                'pdo' => $pdo
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to initialize SQLite database.',
                'details' => $e->getMessage(),
                'pdo' => null
            ];
        }
    }

    // MySQL connection testing
    if (empty($host)) {
        return ['success' => false, 'message' => 'Database host cannot be empty.', 'details' => 'Please provide a valid database host (e.g. 127.0.0.1 or localhost).', 'pdo' => null];
    }
    if (empty($database)) {
        return ['success' => false, 'message' => 'Database name cannot be empty.', 'details' => 'Please provide the name of your MySQL database.', 'pdo' => null];
    }

    try {
        // Attempt connecting directly with dbname
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5
        ]);

        $serverVer = $pdo->query("SELECT VERSION()")->fetchColumn();

        return [
            'success' => true,
            'message' => 'MySQL database connection established successfully.',
            'details' => "Connected to {$database} on {$host}:{$port} (Server: {$serverVer})",
            'pdo' => $pdo
        ];
    } catch (PDOException $e) {
        $errCode = $e->getCode();
        $errMsg = $e->getMessage();

        // Check if database simply does not exist yet; try connecting without dbname and creating it
        if ($errCode === 1049 || str_contains($errMsg, 'Unknown database')) {
            try {
                $rootDsn = "mysql:host={$host};port={$port};charset=utf8mb4";
                $rootPdo = new PDO($rootDsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 5
                ]);

                // Try creating database
                $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

                // Re-test connection with newly created database
                $pdo = new PDO("mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4", $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);

                return [
                    'success' => true,
                    'message' => "Database '{$database}' was created and connected successfully.",
                    'details' => "Provisioned new MySQL database `{$database}` on {$host}.",
                    'pdo' => $pdo
                ];
            } catch (Exception $subErr) {
                return [
                    'success' => false,
                    'message' => "Database '{$database}' does not exist and could not be created automatically.",
                    'details' => "Your hosting provider may require databases to be created through cPanel or your hosting panel first. Please create the database '{$database}' in your control panel and try again.",
                    'pdo' => null
                ];
            }
        }

        // Generic connection error message
        $friendlyReason = 'Please verify that MySQL is running and your host, port, username, and password are correct.';
        if (str_contains($errMsg, 'Access denied')) {
            $friendlyReason = 'Access denied for user ' . htmlspecialchars($username) . '. Please double-check your username and password.';
        } elseif (str_contains($errMsg, 'Connection refused') || str_contains($errMsg, 'actively refused')) {
            $friendlyReason = "Connection refused on host {$host} port {$port}. Ensure MySQL is active and listening.";
        }

        return [
            'success' => false,
            'message' => 'Could not connect to MySQL server.',
            'details' => $friendlyReason,
            'pdo' => null
        ];
    }
}

function install_database_schema(PDO $pdo, string $driver): array {
    $schemaFile = INSTALLER_ROOT_PATH . '/database/schema.sql';
    $seedFile = INSTALLER_ROOT_PATH . '/database/seed.sql';

    if (!file_exists($schemaFile)) {
        return ['success' => false, 'message' => 'Schema file database/schema.sql not found.'];
    }

    $schemaSql = file_get_contents($schemaFile);
    $seedSql = file_exists($seedFile) ? file_get_contents($seedFile) : '';

    try {
        if ($driver === 'mysql') {
            // MySQL specific adjustments
            // 1. Transform syntax for clean MySQL execution
            $schemaStatements = [
                "CREATE TABLE IF NOT EXISTS admins (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(64) NOT NULL UNIQUE,
                    email VARCHAR(128) NOT NULL UNIQUE,
                    password_hash VARCHAR(255) NOT NULL,
                    full_name VARCHAR(128) NOT NULL DEFAULT '',
                    role VARCHAR(32) NOT NULL DEFAULT 'super_admin',
                    status VARCHAR(20) NOT NULL DEFAULT 'active',
                    last_login_at DATETIME NULL,
                    failed_attempts INT NOT NULL DEFAULT 0,
                    lockout_until DATETIME NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                "CREATE TABLE IF NOT EXISTS markets (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    slug VARCHAR(120) NOT NULL UNIQUE,
                    open_time VARCHAR(20) NOT NULL,
                    close_time VARCHAR(20) NOT NULL,
                    status VARCHAR(20) NOT NULL DEFAULT 'active',
                    is_live TINYINT(1) NOT NULL DEFAULT 0,
                    is_featured TINYINT(1) NOT NULL DEFAULT 1,
                    display_order INT NOT NULL DEFAULT 0,
                    seo_title VARCHAR(255) NULL,
                    seo_description TEXT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                "CREATE TABLE IF NOT EXISTS results (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    market_id INT NOT NULL,
                    result_date DATE NOT NULL,
                    open_panna VARCHAR(10) NULL,
                    open_digit VARCHAR(5) NULL,
                    close_digit VARCHAR(5) NULL,
                    close_panna VARCHAR(10) NULL,
                    open_display VARCHAR(20) NULL,
                    close_display VARCHAR(20) NULL,
                    jodi VARCHAR(10) NULL,
                    full_result VARCHAR(50) NULL,
                    status VARCHAR(20) NOT NULL DEFAULT 'published',
                    published_at DATETIME NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY uq_market_date (market_id, result_date)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                "CREATE TABLE IF NOT EXISTS scheduled_publications (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    market_id INT NOT NULL,
                    scheduled_date DATE NOT NULL,
                    scheduled_time TIME NOT NULL,
                    open_display VARCHAR(20) NULL,
                    close_display VARCHAR(20) NULL,
                    status VARCHAR(20) NOT NULL DEFAULT 'pending',
                    notes TEXT NULL,
                    published_at DATETIME NULL,
                    error_message TEXT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                "CREATE TABLE IF NOT EXISTS api_configs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    provider_name VARCHAR(100) NOT NULL,
                    api_url VARCHAR(255) NOT NULL,
                    api_key VARCHAR(255) NULL,
                    api_secret VARCHAR(255) NULL,
                    sync_interval_minutes INT NOT NULL DEFAULT 15,
                    is_active TINYINT(1) NOT NULL DEFAULT 1,
                    last_sync_at DATETIME NULL,
                    last_sync_status VARCHAR(20) NULL,
                    field_mapping TEXT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                "CREATE TABLE IF NOT EXISTS api_sync_logs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    api_config_id INT NULL,
                    endpoint VARCHAR(255) NOT NULL,
                    status VARCHAR(20) NOT NULL,
                    records_processed INT NOT NULL DEFAULT 0,
                    error_message TEXT NULL,
                    raw_response TEXT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                "CREATE TABLE IF NOT EXISTS announcements (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    content TEXT NOT NULL,
                    type VARCHAR(30) NOT NULL DEFAULT 'info',
                    is_active TINYINT(1) NOT NULL DEFAULT 1,
                    display_order INT NOT NULL DEFAULT 0,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                "CREATE TABLE IF NOT EXISTS admin_activity_logs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    admin_id INT NULL,
                    action VARCHAR(100) NOT NULL,
                    module VARCHAR(64) NOT NULL,
                    record_id INT NULL,
                    details TEXT NULL,
                    ip_address VARCHAR(45) NULL,
                    user_agent VARCHAR(255) NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                "CREATE TABLE IF NOT EXISTS settings (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    setting_key VARCHAR(100) NOT NULL UNIQUE,
                    setting_value TEXT NULL,
                    setting_group VARCHAR(50) NOT NULL DEFAULT 'general',
                    description VARCHAR(255) NULL,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
            ];

            foreach ($schemaStatements as $stmt) {
                $pdo->exec($stmt);
            }

            // Execute seed data safely with INSERT IGNORE
            $seedStatements = [
                "INSERT IGNORE INTO markets (id, name, slug, open_time, close_time, status, is_live, is_featured, display_order, seo_title, seo_description)
                VALUES 
                (1, 'Kalyan', 'kalyan', '10:00 AM', '12:00 PM', 'active', 1, 1, 1, 'Kalyan Matka Live Results & Jodi Chart', 'Live Kalyan matka result, open close patti and Jodi chart updated daily.'),
                (2, 'Main Mumbai', 'main-mumbai', '11:00 AM', '01:00 PM', 'active', 1, 1, 2, 'Main Mumbai Matka Live Results & Chart', 'Check fast Main Mumbai results and panel jodi records.'),
                (3, 'Rajdhani', 'rajdhani', '01:00 PM', '03:00 PM', 'active', 0, 1, 3, 'Rajdhani Matka Today Result & Jodi Chart', 'Official Rajdhani open and close live timing and results.'),
                (4, 'Sridevi', 'sridevi', '03:00 PM', '05:00 PM', 'active', 0, 1, 4, 'Sridevi Matka Chart & Live Updates', 'Accurate Sridevi live results, panna chart and historical data.'),
                (5, 'Milan Day', 'milan-day', '05:00 PM', '07:00 PM', 'active', 0, 1, 5, 'Milan Day Live Results & Market Chart', 'Milan Day daily timing, live results and jodi history.'),
                (6, 'Milan Night', 'milan-night', '09:00 PM', '11:00 PM', 'active', 0, 1, 6, 'Milan Night Results & Complete Chart', 'Milan Night opening and closing numbers and comprehensive history.');",

                "INSERT IGNORE INTO results (market_id, result_date, open_digit, close_digit, open_display, close_display, jodi, full_result, status)
                VALUES
                (1, '2025-04-25', '2', '5', '8 2', '7 5', '25', '8 2 - 7 5', 'published'),
                (2, '2025-04-25', '8', '6', '3 8', '1 6', '86', '3 8 - 1 6', 'published'),
                (3, '2025-04-25', '9', '2', '6 9', '4 2', '92', '6 9 - 4 2', 'published'),
                (4, '2025-04-25', '1', '3', '7 1', '8 3', '13', '7 1 - 8 3', 'published'),
                (1, '2025-04-24', '0', '6', '1 0', '5 6', '06', '1 0 - 5 6', 'published'),
                (2, '2025-04-24', '4', '7', '9 4', '2 7', '47', '9 4 - 2 7', 'published'),
                (1, '2025-04-23', '5', '9', '4 5', '8 9', '59', '4 5 - 8 9', 'published'),
                (1, '2025-04-22', '3', '8', '2 3', '4 8', '38', '2 3 - 4 8', 'published'),
                (1, '2025-04-21', '6', '1', '7 6', '3 1', '61', '7 6 - 3 1', 'published'),
                (1, '2025-04-20', '4', '0', '5 4', '9 0', '40', '5 4 - 9 0', 'published');",

                "INSERT IGNORE INTO settings (setting_key, setting_value, setting_group, description)
                VALUES
                ('site_name', 'SATKA MATKA', 'general', 'Platform Name'),
                ('site_tagline', 'Live Results & Charts', 'general', 'Platform Tagline'),
                ('meta_title', 'Satka Matka - Live Results & Historical Charts', 'seo', 'Default SEO Meta Title'),
                ('meta_description', 'Get the latest Satka Matka results, market charts and historical data all in one place. Fast, accurate and easy to use.', 'seo', 'Default SEO Meta Description'),
                ('contact_email', 'support@satkamatka.com', 'contact', 'Public Support Email'),
                ('contact_phone', '+91 98765 43210', 'contact', 'Public Support Phone'),
                ('telegram_channel', 'https://t.me/satkamatka_live', 'social', 'Telegram Link'),
                ('maintenance_mode', '0', 'security', 'Maintenance Mode Flag'),
                ('api_sync_enabled', '1', 'api', 'Enable Automatic API Sync'),
                ('timezone', 'Asia/Kolkata', 'general', 'Platform Timezone'),
                ('last_sync_time', '25 Apr 2025 10:32 AM', 'api', 'Last Sync Display Time');",

                "INSERT IGNORE INTO api_configs (id, provider_name, api_url, api_key, is_active, sync_interval_minutes, last_sync_status)
                VALUES (1, 'Standard Matka Results API', 'https://api.satkamatka-feed.net/v1/live', 'sm_live_sec_99342', 1, 15, 'success');"
            ];

            foreach ($seedStatements as $stmt) {
                $pdo->exec($stmt);
            }
        } else {
            // SQLite execution
            $sqliteStatements = [
                "CREATE TABLE IF NOT EXISTS admins (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username TEXT NOT NULL UNIQUE,
                    email TEXT NOT NULL UNIQUE,
                    password_hash TEXT NOT NULL,
                    full_name TEXT NOT NULL DEFAULT '',
                    role TEXT NOT NULL DEFAULT 'super_admin',
                    status TEXT NOT NULL DEFAULT 'active',
                    last_login_at DATETIME NULL,
                    failed_attempts INTEGER NOT NULL DEFAULT 0,
                    lockout_until DATETIME NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                );",
                "CREATE TABLE IF NOT EXISTS markets (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    slug TEXT NOT NULL UNIQUE,
                    open_time TEXT NOT NULL,
                    close_time TEXT NOT NULL,
                    status TEXT NOT NULL DEFAULT 'active',
                    is_live INTEGER NOT NULL DEFAULT 0,
                    is_featured INTEGER NOT NULL DEFAULT 1,
                    display_order INTEGER NOT NULL DEFAULT 0,
                    seo_title TEXT NULL,
                    seo_description TEXT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                );",
                "CREATE TABLE IF NOT EXISTS results (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    market_id INTEGER NOT NULL,
                    result_date DATE NOT NULL,
                    open_panna TEXT NULL,
                    open_digit TEXT NULL,
                    close_digit TEXT NULL,
                    close_panna TEXT NULL,
                    open_display TEXT NULL,
                    close_display TEXT NULL,
                    jodi TEXT NULL,
                    full_result TEXT NULL,
                    status TEXT NOT NULL DEFAULT 'published',
                    published_at DATETIME NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE(market_id, result_date)
                );",
                "CREATE TABLE IF NOT EXISTS scheduled_publications (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    market_id INTEGER NOT NULL,
                    scheduled_date DATE NOT NULL,
                    scheduled_time TIME NOT NULL,
                    open_display TEXT NULL,
                    close_display TEXT NULL,
                    status TEXT NOT NULL DEFAULT 'pending',
                    notes TEXT NULL,
                    published_at DATETIME NULL,
                    error_message TEXT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                );",
                "CREATE TABLE IF NOT EXISTS api_configs (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    provider_name TEXT NOT NULL,
                    api_url TEXT NOT NULL,
                    api_key TEXT NULL,
                    api_secret TEXT NULL,
                    sync_interval_minutes INTEGER NOT NULL DEFAULT 15,
                    is_active INTEGER NOT NULL DEFAULT 1,
                    last_sync_at DATETIME NULL,
                    last_sync_status TEXT NULL,
                    field_mapping TEXT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                );",
                "CREATE TABLE IF NOT EXISTS api_sync_logs (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    api_config_id INTEGER NULL,
                    endpoint TEXT NOT NULL,
                    status TEXT NOT NULL,
                    records_processed INTEGER NOT NULL DEFAULT 0,
                    error_message TEXT NULL,
                    raw_response TEXT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                );",
                "CREATE TABLE IF NOT EXISTS announcements (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL,
                    content TEXT NOT NULL,
                    type TEXT NOT NULL DEFAULT 'info',
                    is_active INTEGER NOT NULL DEFAULT 1,
                    display_order INTEGER NOT NULL DEFAULT 0,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                );",
                "CREATE TABLE IF NOT EXISTS admin_activity_logs (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    admin_id INTEGER NULL,
                    action TEXT NOT NULL,
                    module TEXT NOT NULL,
                    record_id INTEGER NULL,
                    details TEXT NULL,
                    ip_address TEXT NULL,
                    user_agent TEXT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                );",
                "CREATE TABLE IF NOT EXISTS settings (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    setting_key TEXT NOT NULL UNIQUE,
                    setting_value TEXT NULL,
                    setting_group TEXT NOT NULL DEFAULT 'general',
                    description TEXT NULL,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                );"
            ];

            foreach ($sqliteStatements as $stmt) {
                $pdo->exec($stmt);
            }

            // Seed initial data for SQLite
            $seedStatements = [
                "INSERT OR IGNORE INTO markets (id, name, slug, open_time, close_time, status, is_live, is_featured, display_order, seo_title, seo_description)
                VALUES 
                (1, 'Kalyan', 'kalyan', '10:00 AM', '12:00 PM', 'active', 1, 1, 1, 'Kalyan Matka Live Results & Jodi Chart', 'Live Kalyan matka result, open close patti and Jodi chart updated daily.'),
                (2, 'Main Mumbai', 'main-mumbai', '11:00 AM', '01:00 PM', 'active', 1, 1, 2, 'Main Mumbai Matka Live Results & Chart', 'Check fast Main Mumbai results and panel jodi records.'),
                (3, 'Rajdhani', 'rajdhani', '01:00 PM', '03:00 PM', 'active', 0, 1, 3, 'Rajdhani Matka Today Result & Jodi Chart', 'Official Rajdhani open and close live timing and results.'),
                (4, 'Sridevi', 'sridevi', '03:00 PM', '05:00 PM', 'active', 0, 1, 4, 'Sridevi Matka Chart & Live Updates', 'Accurate Sridevi live results, panna chart and historical data.'),
                (5, 'Milan Day', 'milan-day', '05:00 PM', '07:00 PM', 'active', 0, 1, 5, 'Milan Day Live Results & Market Chart', 'Milan Day daily timing, live results and jodi history.'),
                (6, 'Milan Night', 'milan-night', '09:00 PM', '11:00 PM', 'active', 0, 1, 6, 'Milan Night Results & Complete Chart', 'Milan Night opening and closing numbers and comprehensive history.');",

                "INSERT OR IGNORE INTO results (market_id, result_date, open_digit, close_digit, open_display, close_display, jodi, full_result, status)
                VALUES
                (1, '2025-04-25', '2', '5', '8 2', '7 5', '25', '8 2 - 7 5', 'published'),
                (2, '2025-04-25', '8', '6', '3 8', '1 6', '86', '3 8 - 1 6', 'published'),
                (3, '2025-04-25', '9', '2', '6 9', '4 2', '92', '6 9 - 4 2', 'published'),
                (4, '2025-04-25', '1', '3', '7 1', '8 3', '13', '7 1 - 8 3', 'published'),
                (1, '2025-04-24', '0', '6', '1 0', '5 6', '06', '1 0 - 5 6', 'published'),
                (2, '2025-04-24', '4', '7', '9 4', '2 7', '47', '9 4 - 2 7', 'published'),
                (1, '2025-04-23', '5', '9', '4 5', '8 9', '59', '4 5 - 8 9', 'published'),
                (1, '2025-04-22', '3', '8', '2 3', '4 8', '38', '2 3 - 4 8', 'published'),
                (1, '2025-04-21', '6', '1', '7 6', '3 1', '61', '7 6 - 3 1', 'published'),
                (1, '2025-04-20', '4', '0', '5 4', '9 0', '40', '5 4 - 9 0', 'published');",

                "INSERT OR IGNORE INTO settings (setting_key, setting_value, setting_group, description)
                VALUES
                ('site_name', 'SATKA MATKA', 'general', 'Platform Name'),
                ('site_tagline', 'Live Results & Charts', 'general', 'Platform Tagline'),
                ('meta_title', 'Satka Matka - Live Results & Historical Charts', 'seo', 'Default SEO Meta Title'),
                ('meta_description', 'Get the latest Satka Matka results, market charts and historical data all in one place. Fast, accurate and easy to use.', 'seo', 'Default SEO Meta Description'),
                ('contact_email', 'support@satkamatka.com', 'contact', 'Public Support Email'),
                ('contact_phone', '+91 98765 43210', 'contact', 'Public Support Phone'),
                ('telegram_channel', 'https://t.me/satkamatka_live', 'social', 'Telegram Link'),
                ('maintenance_mode', '0', 'security', 'Maintenance Mode Flag'),
                ('api_sync_enabled', '1', 'api', 'Enable Automatic API Sync'),
                ('timezone', 'Asia/Kolkata', 'general', 'Platform Timezone'),
                ('last_sync_time', '25 Apr 2025 10:32 AM', 'api', 'Last Sync Display Time');",

                "INSERT OR IGNORE INTO api_configs (id, provider_name, api_url, api_key, is_active, sync_interval_minutes, last_sync_status)
                VALUES (1, 'Standard Matka Results API', 'https://api.satkamatka-feed.net/v1/live', 'sm_live_sec_99342', 1, 15, 'success');"
            ];

            foreach ($seedStatements as $stmt) {
                $pdo->exec($stmt);
            }
        }

        return ['success' => true, 'message' => 'Database tables created and baseline records initialized.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error during schema installation: ' . $e->getMessage()];
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
        
        // Remove any prior default admin record if ID 1 exists
        $del = $pdo->prepare("DELETE FROM admins WHERE id = 1 OR username = ? OR email = ?");
        $del->execute([$username, $email]);

        $stmt = $pdo->prepare("
            INSERT INTO admins (id, username, email, password_hash, full_name, role, status, created_at, updated_at)
            VALUES (1, ?, ?, ?, ?, 'super_admin', 'active', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
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
        return ['success' => false, 'message' => 'Failed to create administrator account: ' . $e->getMessage()];
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
        // Save to settings table
        $settingsToSave = [
            'site_name' => $siteName,
            'site_tagline' => $siteTagline,
            'site_url' => $siteUrl,
            'timezone' => $timezone,
            'meta_description' => $description,
            'contact_email' => $contactEmail,
            'installed_date' => date('Y-m-d H:i:s')
        ];

        foreach ($settingsToSave as $k => $v) {
            $stmt = $pdo->prepare("
                INSERT INTO settings (setting_key, setting_value, setting_group, description, updated_at)
                VALUES (?, ?, 'general', 'Installer Configured', CURRENT_TIMESTAMP)
                ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value, updated_at = CURRENT_TIMESTAMP
            ");
            try {
                $stmt->execute([$k, $v]);
            } catch (Exception $e) {
                // If MySQL ON CONFLICT syntax differs:
                $mysqlStmt = $pdo->prepare("
                    INSERT INTO settings (setting_key, setting_value, setting_group, description, updated_at)
                    VALUES (?, ?, 'general', 'Installer Configured', CURRENT_TIMESTAMP)
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP
                ");
                $mysqlStmt->execute([$k, $v]);
            }
        }

        // Generate / update .env file
        $envPath = INSTALLER_ROOT_PATH . '/.env';
        $driver = $dbConfig['driver'] ?? 'mysql';
        $dbHost = $dbConfig['host'] ?? '127.0.0.1';
        $dbPort = $dbConfig['port'] ?? '3306';
        $dbName = $dbConfig['database'] ?? 'satkamatka';
        $dbUser = $dbConfig['username'] ?? 'root';
        $dbPass = $dbConfig['password'] ?? '';

        $envContent = "# Database Configuration\n";
        $envContent .= "DB_CONNECTION={$driver}\n";
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
        'name' => 'Database Schema & Tables',
        'passed' => $tablesOk,
        'message' => $tablesOk ? 'All required database tables exist.' : 'One or more database tables are missing.'
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

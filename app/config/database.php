<?php
/**
 * Database Configuration & Connection Service
 * Supports MySQL 8+ with fallback to SQLite for immediate deployment
 */

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $driver = getenv('DB_CONNECTION') ?: 'sqlite';
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $dbName = getenv('DB_DATABASE') ?: 'satkamatka';
        $user = getenv('DB_USERNAME') ?: 'root';
        $pass = getenv('DB_PASSWORD') ?: '';

        if ($driver === 'mysql') {
            try {
                $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
                $pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                self::$instance = $pdo;
                return self::$instance;
            } catch (PDOException $e) {
                // If MySQL fails, fallback gracefully to SQLite
                error_log("MySQL connection failed: " . $e->getMessage() . ". Falling back to SQLite.");
            }
        }

        // SQLite Initialization
        $sqliteDir = dirname(__DIR__, 2) . '/storage';
        if (!is_dir($sqliteDir)) {
            mkdir($sqliteDir, 0755, true);
        }
        $sqlitePath = $sqliteDir . '/satkamatka.sqlite';
        $isNewDb = !file_exists($sqlitePath) || filesize($sqlitePath) === 0;

        $pdo = new PDO("sqlite:" . $sqlitePath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        // Enable foreign keys and WAL mode for high performance
        $pdo->exec("PRAGMA foreign_keys = ON;");
        $pdo->exec("PRAGMA journal_mode = WAL;");

        if ($isNewDb) {
            self::initializeSchema($pdo);
        }

        self::$instance = $pdo;
        return self::$instance;
    }

    private static function initializeSchema(PDO $pdo): void {
        // SQLite compatible schema creation
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS admins (
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
            );

            CREATE TABLE IF NOT EXISTS markets (
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
            );

            CREATE TABLE IF NOT EXISTS results (
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
            );

            CREATE TABLE IF NOT EXISTS scheduled_publications (
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
            );

            CREATE TABLE IF NOT EXISTS api_configs (
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
            );

            CREATE TABLE IF NOT EXISTS api_sync_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                api_config_id INTEGER NULL,
                endpoint TEXT NOT NULL,
                status TEXT NOT NULL,
                records_processed INTEGER NOT NULL DEFAULT 0,
                error_message TEXT NULL,
                raw_response TEXT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS announcements (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                content TEXT NOT NULL,
                type TEXT NOT NULL DEFAULT 'info',
                is_active INTEGER NOT NULL DEFAULT 1,
                display_order INTEGER NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS admin_activity_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                admin_id INTEGER NULL,
                action TEXT NOT NULL,
                module TEXT NOT NULL,
                record_id INTEGER NULL,
                details TEXT NULL,
                ip_address TEXT NULL,
                user_agent TEXT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                setting_key TEXT NOT NULL UNIQUE,
                setting_value TEXT NULL,
                setting_group TEXT NOT NULL DEFAULT 'general',
                description TEXT NULL,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );
        ");

        // Seed default Super Admin: admin / AdminPassword@2025
        $adminPass = password_hash('AdminPassword@2025', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT OR IGNORE INTO admins (id, username, email, password_hash, full_name, role, status) VALUES (1, 'admin', 'admin@satkamatka.com', ?, 'Super Administrator', 'super_admin', 'active')");
        $stmt->execute([$adminPass]);

        // Seed Markets from reference design
        $markets = [
            [1, 'Kalyan', 'kalyan', '10:00 AM', '12:00 PM', 'active', 1, 1, 1, 'Kalyan Matka Live Results & Historical Charts', 'Live Kalyan matka result, open close patti and Jodi chart.'],
            [2, 'Main Mumbai', 'main-mumbai', '11:00 AM', '01:00 PM', 'active', 1, 1, 2, 'Main Mumbai Matka Live Results & Chart', 'Check fast Main Mumbai results and panel jodi records.'],
            [3, 'Rajdhani', 'rajdhani', '01:00 PM', '03:00 PM', 'active', 0, 1, 3, 'Rajdhani Matka Today Result & Jodi Chart', 'Official Rajdhani open and close live timing and results.'],
            [4, 'Sridevi', 'sridevi', '03:00 PM', '05:00 PM', 'active', 0, 1, 4, 'Sridevi Matka Chart & Live Updates', 'Accurate Sridevi live results, panna chart and historical data.'],
            [5, 'Milan Day', 'milan-day', '05:00 PM', '07:00 PM', 'active', 0, 1, 5, 'Milan Day Live Results & Market Chart', 'Milan Day daily timing, live results and jodi history.'],
            [6, 'Milan Night', 'milan-night', '09:00 PM', '11:00 PM', 'active', 0, 1, 6, 'Milan Night Results & Complete Chart', 'Milan Night opening and closing numbers and comprehensive history.']
        ];
        $mStmt = $pdo->prepare("INSERT OR IGNORE INTO markets (id, name, slug, open_time, close_time, status, is_live, is_featured, display_order, seo_title, seo_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($markets as $m) {
            $mStmt->execute($m);
        }

        // Seed Results matching reference
        $results = [
            [1, '2025-04-25', '2', '5', '8 2', '7 5', '25', '8 2 - 7 5', 'published'],
            [2, '2025-04-25', '8', '6', '3 8', '1 6', '86', '3 8 - 1 6', 'published'],
            [3, '2025-04-25', '9', '2', '6 9', '4 2', '92', '6 9 - 4 2', 'published'],
            [4, '2025-04-25', '1', '3', '7 1', '8 3', '13', '7 1 - 8 3', 'published'],
            [1, '2025-04-24', '0', '6', '1 0', '5 6', '06', '1 0 - 5 6', 'published'],
            [2, '2025-04-24', '4', '7', '9 4', '2 7', '47', '9 4 - 2 7', 'published'],
            [1, '2025-04-23', '5', '9', '4 5', '8 9', '59', '4 5 - 8 9', 'published'],
            [1, '2025-04-22', '3', '8', '2 3', '4 8', '38', '2 3 - 4 8', 'published'],
            [1, '2025-04-21', '6', '1', '7 6', '3 1', '61', '7 6 - 3 1', 'published'],
            [1, '2025-04-20', '4', '0', '5 4', '9 0', '40', '5 4 - 9 0', 'published'],
        ];
        $rStmt = $pdo->prepare("INSERT OR IGNORE INTO results (market_id, result_date, open_digit, close_digit, open_display, close_display, jodi, full_result, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($results as $r) {
            $rStmt->execute($r);
        }

        // Seed Settings
        $settings = [
            ['site_name', 'SATKA MATKA', 'general', 'Platform Name'],
            ['site_tagline', 'Live Results & Charts', 'general', 'Platform Tagline'],
            ['meta_title', 'Satka Matka - Live Results & Historical Charts', 'seo', 'Default SEO Meta Title'],
            ['meta_description', 'Get the latest Satka Matka results, market charts and historical data all in one place. Fast, accurate and easy to use.', 'seo', 'Default SEO Meta Description'],
            ['contact_email', 'support@satkamatka.com', 'contact', 'Public Support Email'],
            ['contact_phone', '+91 98765 43210', 'contact', 'Public Support Phone'],
            ['telegram_channel', 'https://t.me/satkamatka_live', 'social', 'Telegram Link'],
            ['maintenance_mode', '0', 'security', 'Maintenance Mode Flag'],
            ['api_sync_enabled', '1', 'api', 'Enable Automatic API Sync'],
            ['timezone', 'Asia/Kolkata', 'general', 'Platform Timezone'],
            ['last_sync_time', '25 Apr 2025 10:32 AM', 'api', 'Last Sync Display Time']
        ];
        $sStmt = $pdo->prepare("INSERT OR IGNORE INTO settings (setting_key, setting_value, setting_group, description) VALUES (?, ?, ?, ?)");
        foreach ($settings as $s) {
            $sStmt->execute($s);
        }

        // Seed API Config
        $pdo->exec("INSERT OR IGNORE INTO api_configs (id, provider_name, api_url, api_key, is_active, sync_interval_minutes, last_sync_status) VALUES (1, 'Live Matka Verification Feed API', 'https://api.satkamatka-feed.net/v1/live', 'sm_live_sec_99342', 1, 15, 'success')");
    }
}

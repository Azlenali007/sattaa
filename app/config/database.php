<?php
/**
 * Database Configuration & Connection Service
 * Exclusively supports MySQL 8+ / MariaDB via PDO (pdo_mysql)
 * Strictly zero SQLite fallback
 */

class Database {
    private static ?PDO $instance = null;

    /**
     * Get the active PDO MySQL connection instance (Singleton)
     *
     * @return PDO
     * @throws RuntimeException If MySQL connection fails or extension is missing
     */
    public static function getConnection(): PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        // Verify PDO and pdo_mysql extension
        if (!extension_loaded('pdo') || !extension_loaded('pdo_mysql')) {
            throw new RuntimeException("Fatal Database Error: The PHP PDO and pdo_mysql extensions are required but not installed.");
        }

        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $dbName = getenv('DB_DATABASE') ?: 'satkamatka';
        $user = getenv('DB_USERNAME') !== false ? getenv('DB_USERNAME') : 'root';
        $pass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';

        // Fallback checks from $_ENV or $_SERVER
        if ($user === '' && !empty($_ENV['DB_USERNAME'])) $user = $_ENV['DB_USERNAME'];
        if ($pass === '' && !empty($_ENV['DB_PASSWORD'])) $pass = $_ENV['DB_PASSWORD'];
        if (empty($dbName) && !empty($_ENV['DB_DATABASE'])) $dbName = $_ENV['DB_DATABASE'];

        $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]);
            self::$instance = $pdo;
            return self::$instance;
        } catch (PDOException $e) {
            error_log("MySQL Connection Failure: " . $e->getMessage());
            throw new RuntimeException(
                "Database Connection Error: Could not connect to MySQL server ({$host}:{$port}) using database '{$dbName}'. " .
                "Please verify your MySQL server is running and check credentials in your .env file or run the installer at /install/."
            );
        }
    }

    /**
     * Set explicit PDO instance (used by installer or test suites)
     */
    public static function setConnection(?PDO $pdo): void {
        self::$instance = $pdo;
    }

    /**
     * Close connection instance
     */
    public static function disconnect(): void {
        self::$instance = null;
    }
}

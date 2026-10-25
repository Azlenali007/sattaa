-- Satka Matka Results & Historical Charts Database Schema
-- Pure MySQL 8+ with InnoDB and UTF-8 Multi-Byte Encoding

CREATE TABLE IF NOT EXISTS admins (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS markets (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS results (
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
    UNIQUE KEY uq_market_date (market_id, result_date),
    INDEX idx_result_date (result_date),
    INDEX idx_market_id (market_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS scheduled_publications (
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
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_scheduled_status (status, scheduled_date, scheduled_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS api_configs (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS api_sync_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    api_config_id INT NULL,
    endpoint VARCHAR(255) NOT NULL,
    status VARCHAR(20) NOT NULL,
    records_processed INT NOT NULL DEFAULT 0,
    error_message TEXT NULL,
    raw_response TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sync_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    type VARCHAR(30) NOT NULL DEFAULT 'info',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    display_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NULL,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(64) NOT NULL,
    record_id INT NULL,
    details TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin_log_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    setting_group VARCHAR(50) NOT NULL DEFAULT 'general',
    description VARCHAR(255) NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

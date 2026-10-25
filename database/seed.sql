-- Initial Data Seeder for Satka Matka Platform
-- Pure MySQL 8+ Seeder
-- Initial Super Admin (Username: admin, Password: AdminPassword@2025)
-- Verified bcrypt hash: $2y$10$mm0bRraOc.Q9M/ux4XIgeedLKQbt6z8zwIXtAxfwX9aP7ARmaTtRG

INSERT IGNORE INTO admins (id, username, email, password_hash, full_name, role, status)
VALUES (1, 'admin', 'admin@satkamatka.com', '$2y$10$mm0bRraOc.Q9M/ux4XIgeedLKQbt6z8zwIXtAxfwX9aP7ARmaTtRG', 'Super Administrator', 'super_admin', 'active');

-- Markets matching the reference layout
INSERT IGNORE INTO markets (id, name, slug, open_time, close_time, status, is_live, is_featured, display_order, seo_title, seo_description)
VALUES 
(1, 'Kalyan', 'kalyan', '10:00 AM', '12:00 PM', 'active', 1, 1, 1, 'Kalyan Matka Live Results & Jodi Chart', 'Live Kalyan matka result, open close patti and Jodi chart updated daily.'),
(2, 'Main Mumbai', 'main-mumbai', '11:00 AM', '01:00 PM', 'active', 1, 1, 2, 'Main Mumbai Matka Live Results & Chart', 'Check fast Main Mumbai results and panel jodi records.'),
(3, 'Rajdhani', 'rajdhani', '01:00 PM', '03:00 PM', 'active', 0, 1, 3, 'Rajdhani Matka Today Result & Jodi Chart', 'Official Rajdhani open and close live timing and results.'),
(4, 'Sridevi', 'sridevi', '03:00 PM', '05:00 PM', 'active', 0, 1, 4, 'Sridevi Matka Chart & Live Updates', 'Accurate Sridevi live results, panna chart and historical data.'),
(5, 'Milan Day', 'milan-day', '05:00 PM', '07:00 PM', 'active', 0, 1, 5, 'Milan Day Live Results & Market Chart', 'Milan Day daily timing, live results and jodi history.'),
(6, 'Milan Night', 'milan-night', '09:00 PM', '11:00 PM', 'active', 0, 1, 6, 'Milan Night Results & Complete Chart', 'Milan Night opening and closing numbers and comprehensive history.');

-- Results matching the reference image
INSERT IGNORE INTO results (market_id, result_date, open_digit, close_digit, open_display, close_display, jodi, full_result, status)
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
(1, '2025-04-20', '4', '0', '5 4', '9 0', '40', '5 4 - 9 0', 'published');

-- System Settings
INSERT IGNORE INTO settings (setting_key, setting_value, setting_group, description)
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
('last_sync_time', '25 Apr 2025 10:32 AM', 'api', 'Last Sync Display Time');

-- Initial API Config
INSERT IGNORE INTO api_configs (id, provider_name, api_url, api_key, is_active, sync_interval_minutes, last_sync_status)
VALUES (1, 'Standard Matka Results API', 'https://api.satkamatka-feed.net/v1/live', 'sm_live_sec_99342', 1, 15, 'success');

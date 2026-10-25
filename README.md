# SATKA MATKA — Live Results & Historical Charts Platform

A production-ready Satka Matka results publication and historical charting platform built with **Pure PHP 8+**, **MySQL 8+ (via PDO pdo_mysql)**, **Tailwind CSS**, **Alpine.js**, and **Vanilla JavaScript**.

---

## 1. Features

### Public Portal
- **Visual Design**: Premium white and purple visual identity matching the master design specification.
- **Header & Navigation**: Responsive sticky navigation bar with interactive desktop dropdown menus for Markets and Historical Charts, accessible keyboard controls, outside-click auto-close, and full mobile drawer.
- **Live Board**: Real-time 4-card display with open and close digit blocks, pulsating live indicators, and opening/closing schedules.
- **Popular Markets**: 6-card grid with responsive layouts and direct chart viewing.
- **Market Schedule & Recent History**: Side-by-side synchronized tables with open/upcoming status pills.
- **Interactive Right-Side Panel**: Market details header, live timestamps, tabbed Jodi Chart, related markets, and quick navigation.
- **Live Search**: Instant debounced search by market name, date, or jodi digits.
- **Sitemaps & SEO**: Automatic XML sitemap (`/sitemap.php?xml=1`), HTML sitemap, clean metadata, and search-engine friendly structure.
- **Zero Public Registration**: Visitors access all results and charts without account creation friction.

### Super Admin Control Center (`/admin`)
- **Single Super Admin**: Dedicated, secure administrative portal with session protection and audit logging.
- **Default Credentials**:
  - **Username**: `admin`
  - **Password**: `AdminPassword@2025`
- **Market Management**: Add, edit, activate/deactivate, reorder, and configure custom SEO titles and schedules.
- **Result Management**: Declare live numbers, edit historical entries, bulk import CSV datasets, and toggle draft/publish states.
- **Scheduled Publishing**: Pre-schedule results to automatically publish at future dates and times.
- **Charts & Export**: Audit complete jodi records and export datasets to CSV format.
- **API Results Sync**: Configure remote API credentials, test connections, view raw logs, and trigger manual or automated cron synchronization.
- **Homepage Notices**: Publish alert banners and announcements.
- **SEO & Settings**: Global meta titles, descriptions, support contact info, and timezone configurations.
- **Database Snapshots**: Export and download full SQL database backups directly from the panel.

### Browser-based Web Installer (`/install/`)
- 8-step installation wizard that verifies server requirements (`pdo_mysql`), tests database connectivity, installs the MySQL schema, provisions the Super Admin, and securely locks the installation.

---

## 2. Directory Structure

```
├── index.php                      # Homepage matching reference design
├── markets.php                    # All Markets directory
├── market.php                     # Market details and chart preview
├── results.php                    # Latest published results archive
├── chart.php                      # Jodi & Panel Charts with pagination
├── history.php                    # Historical records search & filter
├── search.php                     # Search results page
├── about.php                      # Platform overview
├── contact.php                    # Working inquiry form
├── privacy-policy.php             # Privacy Policy
├── terms.php                      # Terms & Conditions
├── 404.php                        # Custom not-found page
├── sitemap.php                    # XML & HTML sitemap
├── robots.txt                     # Crawler directives
├── .htaccess                      # Security headers & directory protections
├── .env.example                   # Environment configuration template
│
├── app/
│   ├── config/                    # Application configuration & MySQL database
│   ├── helpers/                   # Validation, security, URL, SEO helpers
│   ├── middleware/                # CSRF & admin authentication
│   ├── models/                    # Market, Result, Admin, Setting, Announcement
│   └── services/                  # ApiSyncService, SeoService, BackupService
│
├── includes/                      # Shared layouts & UI components
├── admin/                         # Complete Super Admin Control Center
├── install/                       # Browser-based 8-step web installer
├── api/                           # JSON endpoints for search & polling
├── bin/                           # CLI tools (reset-admin.php)
├── cron/                          # CLI automation scripts
├── assets/                        # CSS, JS, SVG icons & illustrations
├── database/                      # MySQL schema.sql and seed.sql
├── storage/                       # Database backups, cache, and logs
└── tests/                         # Integrity and schema verification tests
```

---

## 3. Technology Stack

- **Backend**: Core PHP 8.2+ with PDO prepared statements.
- **Database**: MySQL 8+ / MariaDB via PDO (`pdo_mysql`). Zero SQLite.
- **Frontend**: HTML5, Tailwind CSS, Alpine.js, Vanilla JavaScript, and SVG icons.
- **Zero Framework Bloat**: No React, Next.js, Vue, or TypeScript files.

---

## 4. Setup & Deployment Instructions

### Web Installation
Navigate to `/install/` in your web browser and follow the on-screen 8-step wizard.

### Manual Database Setup (MySQL)
1. Import `database/schema.sql` into your MySQL 8+ database.
2. Import `database/seed.sql` to populate initial markets and the default Super Admin.
3. Configure your `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=satkamatka
   DB_USERNAME=your_user
   DB_PASSWORD=your_password
   ```

### Reset Admin Password via CLI
```bash
php bin/reset-admin.php YourNewPassword123
```

### Running Background Cron Jobs
Set up the following crontabs on your Linux server:
```bash
# Sync API results every 15 minutes
*/15 * * * * php /path/to/satka-matka/cron/sync-results.php >> /path/to/satka-matka/storage/logs/cron.log 2>&1

# Publish scheduled results every minute
* * * * * php /path/to/satka-matka/cron/publish-scheduled.php >> /path/to/satka-matka/storage/logs/cron.log 2>&1

# Prune aged logs daily at midnight
0 0 * * * php /path/to/satka-matka/cron/cleanup-logs.php >> /path/to/satka-matka/storage/logs/cron.log 2>&1
```

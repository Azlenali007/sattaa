#!/bin/bash
# 0. Ensure PHP and MariaDB binaries exist
if ! command -v php &> /dev/null || ! command -v mariadb &> /dev/null; then
  export DEBIAN_FRONTEND=noninteractive
  apt-get update -qq > /dev/null 2>&1 || true
  apt-get install -y -qq --no-install-recommends -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" php8.2-cli php8.2-mysql mariadb-server curl > /dev/null 2>&1 || true
fi

# 1. Ensure MariaDB server is active
if command -v service &> /dev/null; then
  service mariadb status > /dev/null 2>&1 || service mariadb start > /dev/null 2>&1 || true
fi

# 2. Ensure MySQL database and privileges are in place
if command -v mariadb &> /dev/null; then
  mariadb -e "CREATE DATABASE IF NOT EXISTS satkamatka CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL PRIVILEGES ON satkamatka.* TO 'root'@'127.0.0.1' IDENTIFIED BY ''; GRANT ALL PRIVILEGES ON satkamatka.* TO 'root'@'localhost' IDENTIFIED BY ''; FLUSH PRIVILEGES;" > /dev/null 2>&1 || true
  
  # Check if tables exist, seed if empty
  TABLE_COUNT=$(mariadb -N -s -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'satkamatka';" 2>/dev/null || echo 0)
  if [ "$TABLE_COUNT" -eq 0 ]; then
    mariadb satkamatka < database/schema.sql 2>/dev/null || true
    mariadb satkamatka < database/seed.sql 2>/dev/null || true
  fi
fi

# 3. Start PHP development server on port 3000
echo "Starting Satka Matka PHP Server on http://0.0.0.0:3000..."
exec php -S 0.0.0.0:3000 router.php

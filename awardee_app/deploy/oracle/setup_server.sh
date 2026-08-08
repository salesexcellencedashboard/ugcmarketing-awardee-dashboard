#!/bin/bash
#===============================================================================
# AWARDEE System - Oracle Cloud Server Setup Script
#===============================================================================
# This script installs all required software on a fresh Ubuntu 22.04/24.04
# Oracle Cloud Compute VM.
#
# Usage:
#   chmod +x setup_server.sh
#   sudo ./setup_server.sh
#===============================================================================

set -e

echo "=============================================="
echo " AWARDEE System - Oracle Cloud Setup"
echo "=============================================="

# Check if running as root
if [[ $EUID -ne 0 ]]; then
    echo "ERROR: Please run as root: sudo ./setup_server.sh"
    exit 1
fi

echo ""
echo "[1/6] Updating system packages..."
apt update
apt upgrade -y

echo ""
echo "[2/6] Installing Nginx..."
apt install -y nginx

echo ""
echo "[3/6] Installing PHP 8.2 and extensions..."
apt install -y software-properties-common
add-apt-repository -y ppa:ondrej/php
apt update
apt install -y php8.2-fpm php8.2-mysql \
    php8.2-curl php8.2-gd php8.2-intl php8.2-mbstring \
    php8.2-xml php8.2-zip php8.2-bcmath php8.2-cli \
    php8.2-common php8.2-json php8.2-opcache

echo ""
echo "[4/6] Installing MySQL Server..."
apt install -y mysql-server

echo ""
echo "[5/6] Installing Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

echo ""
echo "[6/6] Installing Git..."
apt install -y git

echo ""
echo "=============================================="
echo " INSTALLATION COMPLETE!"
echo "=============================================="
echo ""
echo "Next steps:"
echo "  1. Secure MySQL:  sudo mysql_secure_installation"
echo "  2. Create database:  sudo mysql -u root -p"
echo "     CREATE DATABASE awardee_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo "     CREATE USER 'awardee_user'@'localhost' IDENTIFIED BY 'YOUR_PASSWORD';"
echo "     GRANT ALL PRIVILEGES ON awardee_system.* TO 'awardee_user'@'localhost';"
echo "     FLUSH PRIVILEGES;"
echo "  3. Upload code to /var/www/awardee"
echo "  4. Run:  sudo -u www-data composer install --no-dev --optimize-autoloader"
echo "  5. Configure .env"
echo "  6. Run:  sudo -u www-data php spark migrate"
echo "  7. Run:  sudo -u www-data php spark db:seed DatabaseSeeder"
echo "  8. Configure Nginx (see nginx_awardee.conf)"
echo "=============================================="
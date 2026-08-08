# AWARDEE System - Oracle Cloud Deployment Guide

> **Target Platform:** Oracle Cloud Infrastructure (OCI) Compute VM
> **Server OS:** Ubuntu 22.04 LTS / 24.04 LTS (or Oracle Linux 8/9)
> **Stack:** Nginx + PHP 8.2-FPM + MySQL 8 + CodeIgniter 4
> **Last Updated:** 2026-08-08

---

## Table of Contents

1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Step 1: Create an Oracle Cloud Free Tier Account](#step-1-create-an-oracle-cloud-free-tier-account)
4. [Step 2: Create a Compute Instance (VM)](#step-2-create-a-compute-instance-vm)
5. [Step 3: Connect to Your Server via SSH](#step-3-connect-to-your-server-via-ssh)
6. [Step 4: Install Required Software](#step-4-install-required-software)
7. [Step 5: Configure MySQL Database](#step-5-configure-mysql-database)
8. [Step 6: Upload the System Code](#step-6-upload-the-system-code)
9. [Step 7: Configure the Application](#step-7-configure-the-application)
10. [Step 8: Configure Nginx Web Server](#step-8-configure-nginx-web-server)
11. [Step 9: Run Database Migrations](#step-9-run-database-migrations)
12. [Step 10: Configure Firewall & Security](#step-10-configure-firewall--security)
13. [Step 11: Post-Deployment Verification](#step-11-post-deployment-verification)
14. [Ongoing Maintenance](#ongoing-maintenance)
15. [Troubleshooting](#troubleshooting)

---

## Overview

This guide provides **step-by-step instructions** to deploy the AWARDEE Sales Excellence Recognition System to **Oracle Cloud Infrastructure** using the **Always Free tier** (free forever). The system runs on a full virtual machine with Nginx, PHP 8.2, and MySQL — giving you complete control, persistent storage for sessions/uploads, and reliable performance.

### Architecture

```
Internet
   │
   ▼
Oracle Cloud OCI (Always Free VM)
   ├── Nginx (Port 80/443) ──► public/
   ├── PHP 8.2-FPM ────────────┘
   ├── MySQL 8 (awardee_system)
   └── Persistent Disk (sessions, uploads, logs)
```

---

## Prerequisites

Before you begin, make sure you have:

- ✅ A valid **email address** (for Oracle account)
- ✅ A **credit/debit card** (required by Oracle for identity verification — **NOT charged** if you stay within Free Tier limits)
- ✅ An **SSH key pair** (or you can generate one during VM creation)
- ✅ Your system code is ready on your local machine (`/Users/jhonarvin/Desktop/AWARDEE/awardee_app`)

---

## Step 1: Create an Oracle Cloud Free Tier Account

1. Go to **https://signup.cloud.oracle.com**
2. Enter your details:
   - **Country:** Philippines
   - **Name, Email, Password** (as required)
3. Select **"Oracle Cloud Free Tier"** (Always Free)
4. Enter your **credit/debit card** for verification
   > ⚠️ Oracle will place a temporary hold (~$1) but will NOT charge you. It is released within a few days.
5. Verify via email/SMS OTP
6. Wait for account activation (usually **1–5 minutes**, sometimes up to an hour)

---

## Step 2: Create a Compute Instance (VM)

1. Log in to the **Oracle Cloud Console**: https://cloud.oracle.com
2. In the top menu, select your **Home Region** (e.g., `ap-singapore-1` or `ap-tokyo-1` for nearest latency)
3. Go to: **Menu → Compute → Instances**
4. Click **"Create instance"**
5. Configure the instance:

   | Setting | Value |
   |---------|-------|
   | **Name** | `awardee-prod` |
   | **Image** | **Ubuntu 22.04** (Always Free eligible) |
   | **Shape** | `VM.Standard.E2.1.Micro` (1 OCPU, 1GB RAM) — Always Free |
   | **Boot volume** | 50 GB (within the 200GB free allowance) |
   | **Networking** | Select existing VCN (auto-created) or create new |
   | **Public IPv4** | ✅ Assign a public IPv4 address |
   | **SSH keys** | Paste your public key (see note below) |

6. **SSH Key Options:**
   - If you already have a key pair, paste the **public key**
   - If you don't have one, generate it first:
     ```bash
     ssh-keygen -t rsa -b 4096 -f ~/.ssh/oracle_key
     ```
   - Then copy the public key:
     ```bash
     cat ~/.ssh/oracle_key.pub
     ```
   - Paste that into Oracle's SSH key field

7. Click **"Create"** and wait for the instance to be **Provisioning → Running** (1–3 minutes)
8. Note down the **Public IP address** of your instance

---

## Step 3: Connect to Your Server via SSH

Open a terminal on your local machine and connect:

```bash
ssh -i ~/.ssh/oracle_key ubuntu@YOUR_SERVER_PUBLIC_IP
```

> Example: `ssh -i ~/.ssh/oracle_key ubuntu@152.67.123.45`

**First time connecting?** Type `yes` when asked to accept the host key.

**Verify the OS:**
```bash
cat /etc/os-release
```

---

## Step 4: Install Required Software

Run these commands **one at a time** on your server:

### 4.1 Update the system
```bash
sudo apt update
sudo apt upgrade -y
```

### 4.2 Install Nginx
```bash
sudo apt install -y nginx
```

### 4.3 Install PHP 8.2 & required extensions
```bash
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
```

```bash
sudo apt install -y php8.2-fpm php8.2-mysql \
  php8.2-curl php8.2-gd php8.2-intl php8.2-mbstring \
  php8.2-xml php8.2-zip php8.2-bcmath php8.2-cli \
  php8.2-common php8.2-json php8.2-opcache
```

Verify PHP:
```bash
php -v
```
You should see `PHP 8.2.x`.

### 4.4 Install MySQL Server
```bash
sudo apt install -y mysql-server
```

Secure MySQL (set root password):
```bash
sudo mysql_secure_installation
```
- Set a **strong root password** (write it down!)
- Answer `Y` to the other security prompts

### 4.5 Install Composer
```bash
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Verify:
```bash
composer --version
```

### 4.6 Install Git
```bash
sudo apt install -y git
```

---

## Step 5: Configure MySQL Database

### 5.1 Create the database and user

```bash
sudo mysql -u root -p
```

Enter your MySQL root password. Then run these SQL commands:

```sql
CREATE DATABASE awardee_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'awardee_user'@'localhost' IDENTIFIED BY 'CHANGE_THIS_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON awardee_system.* TO 'awardee_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

> ⚠️ Replace `CHANGE_THIS_STRONG_PASSWORD` with a real strong password.

### 5.2 Verify the connection

```bash
mysql -u awardee_user -p awardee_system
```

Enter the password. Type `EXIT;` when done. If it connects, your DB is ready.

---

## Step 6: Upload the System Code

### Option A: Via GitHub (Recommended)

If your code is on GitHub:

```bash
cd /var/www
sudo git clone https://github.com/arvinMbacsal/awardee.git awardee
```

> If the repo is private, use a personal access token or SSH URL.

### Option B: Via SCP from your local machine

On your **local machine** (not the server), run:

```bash
cd /Users/jhonarvin/Desktop/AWARDEE
scp -r -i ~/.ssh/oracle_key awardee_app ubuntu@YOUR_SERVER_PUBLIC_IP:/tmp/awardee
```

Then on the server:
```bash
sudo mkdir -p /var/www
sudo mv /tmp/awardee /var/www/awardee
```

### 6.1 Set correct ownership & permissions

```bash
cd /var/www/awardee
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 writable
sudo chmod -R 775 public/uploads
```

---

## Step 7: Configure the Application

### 7.1 Install Composer dependencies

```bash
cd /var/www/awardee
sudo -u www-data composer install --no-dev --optimize-autoloader
```

### 7.2 Create the environment file

```bash
sudo cp .env.example .env
```

If `.env.example` doesn't exist, create `.env` manually:

```bash
sudo nano /var/www/awardee/.env
```

Paste this content, updating the values:

```ini
#--------------------------------------------------------------------
# AWARDEE System Environment Configuration
#--------------------------------------------------------------------

CI_ENVIRONMENT = production

app.baseURL = 'http://YOUR_SERVER_PUBLIC_IP/'

database.default.hostname = 127.0.0.1
database.default.database = awardee_system
database.default.username = awardee_user
database.default.password = CHANGE_THIS_STRONG_PASSWORD
database.default.DBDriver = MySQLi
database.default.port = 3306
database.default.DBPrefix =
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_unicode_ci

encryption.key = YOUR_32_BYTE_ENCRYPTION_KEY
```

### 7.3 Generate the encryption key

```bash
php -r "echo bin2hex(random_bytes(16));"
```

Copy the output and put it after `encryption.key =`.

### 7.4 Set file permissions for `.env`

```bash
sudo chown www-data:www-data /var/www/awardee/.env
sudo chmod 600 /var/www/awardee/.env
```

---

## Step 8: Configure Nginx Web Server

### 8.1 Create the Nginx site configuration

```bash
sudo nano /etc/nginx/sites-available/awardee
```

Paste the following:

```nginx
server {
    listen 80;
    server_name YOUR_SERVER_PUBLIC_IP;

    root /var/www/awardee/public;
    index index.php index.html;

    charset utf-8;

    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(?:css|js|png|jpg|jpeg|gif|ico|svg|webp|woff|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    location ^~ /uploads/ {
        alias /var/www/awardee/public/uploads/;
        try_files $uri =404;
    }
}
```

> Replace `YOUR_SERVER_PUBLIC_IP` with your actual public IP.

### 8.2 Enable the site

```bash
sudo ln -s /etc/nginx/sites-available/awardee /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default
```

### 8.3 Test the Nginx configuration

```bash
sudo nginx -t
```

You should see:
```
nginx: configuration file /etc/nginx/nginx.conf test is successful
```

### 8.4 Start Nginx

```bash
sudo systemctl enable nginx
sudo systemctl restart nginx
```

Check status:
```bash
sudo systemctl status nginx
```

---

## Step 9: Run Database Migrations

### 9.1 Run the migrations

```bash
cd /var/www/awardee
sudo -u www-data php spark migrate
```

You should see output like:
```
Running all new migrations...
...  (each migration runs successfully)
Done.
```

### 9.2 Run the seeders (create admin user & reference data)

```bash
sudo -u www-data php spark db:seed DatabaseSeeder
```

This creates:
- The default admin user (check the seeder file for credentials)
- Reference data (regions, stores, dealers, products)

### 9.3 Verify the tables exist

```bash
mysql -u awardee_user -p awardee_system -e "SHOW TABLES;"
```

---

## Step 10: Configure Firewall & Security

### 10.1 Oracle Cloud Security List (OCI Console)

1. Go to **Menu → Networking → Virtual Cloud Networks**
2. Click your VCN → **Security Lists** → Default Security List
3. **Add Ingress Rules:**
   - Source: `0.0.0.0/0`, IP Protocol: **TCP**, Destination Port: **80** (HTTP)
   - Source: `0.0.0.0/0`, IP Protocol: **TCP**, Destination Port: **443** (HTTPS — optional)
   - Source: `0.0.0.0/0`, IP Protocol: **TCP**, Destination Port: **22** (SSH — should already exist)

### 10.2 Ubuntu UFW Firewall (on the server)

```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable
```

Check status:
```bash
sudo ufw status
```

---

## Step 11: Post-Deployment Verification

### 11.1 Open the app in your browser

```
http://YOUR_SERVER_PUBLIC_IP/
```

You should see the AWARDEE login page.

### 11.2 Verify the health endpoint

```bash
curl http://YOUR_SERVER_PUBLIC_IP/health.php
```

### 11.3 Login as admin

- Use the admin credentials from the seeder (see `app/Database/Seeds/AdminUserSeeder.php`)
- Verify you can log in and the session persists (refresh the page — you should stay logged in)

### 11.4 Test a photo upload

- Go to Data Entry and upload a photo
- Refresh the page — the photo should still display (persistent storage works ✓)

---

## Ongoing Maintenance

### Update the application from GitHub

```bash
cd /var/www/awardee
sudo -u www-data git pull origin main
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data php spark migrate
sudo systemctl reload nginx
```

### Update the server packages

```bash
sudo apt update && sudo apt upgrade -y
```

### View application logs

```bash
tail -f /var/www/awardee/writable/logs/*.log
```

### View Nginx error logs

```bash
sudo tail -f /var/log/nginx/error.log
```

### Restart PHP-FPM

```bash
sudo systemctl restart php8.2-fpm
```

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| **Blank page / 502 Bad Gateway** | Check PHP-FPM is running: `sudo systemctl status php8.2-fpm` |
| **404 on all pages** | Check Nginx root path is `/var/www/awardee/public`; reload Nginx |
| **Database connection error** | Verify `.env` credentials; test: `mysql -u awardee_user -p awardee_system` |
| **Uploads not working** | Check permissions: `sudo chmod -R 775 /var/www/awardee/public/uploads` |
| **Login doesn't persist** | Check session dir: `sudo chmod -R 775 /var/www/awardee/writable/session` |
| **Migration failed** | View error: `cd /var/www/awardee && sudo -u www-data php spark migrate -d` |
| **Can't reach site** | Check OCI Security List port 80; check `sudo ufw status` |
| **Slow page loads** | Consider enabling Redis or upgrading to Ampere A1 shape (also free) |
| **Certificate error** | Install Let's Encrypt: `sudo apt install -y certbot python3-certbot-nginx && sudo certbot --nginx` |
| **Route not found** | Run `sudo -u www-data php spark cache:clear` |

---

## Cost Summary

| Item | Cost |
|------|------|
| Oracle Cloud Compute VM (Always Free) | **₱0.00** |
| MySQL on same VM | **₱0.00** |
| 50GB Block Storage (within free 200GB) | **₱0.00** |
| Data transfer (within free 10TB/month) | **₱0.00** |
| **Total Monthly Cost** | **₱0.00** |

---

## Quick Reference Cheat Sheet

```bash
# Deploy the app to /var/www/awardee
cd /var/www/awardee
sudo chown -R www-data:www-data .
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data php spark migrate
sudo -u www-data php spark db:seed DatabaseSeeder
sudo systemctl restart nginx
```

---

**Guide Version:** 1.0  
**Target Platform:** Oracle Cloud Infrastructure Always Free Tier  
**Prepared For:** AWARDEE Sales Excellence Recognition System
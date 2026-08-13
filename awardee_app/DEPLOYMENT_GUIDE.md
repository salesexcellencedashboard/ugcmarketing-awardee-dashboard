# AWARDEE System - Deployment Guide

## Overview
This guide covers deploying the AWARDEE Performance Management System to production environments (Oracle Database).

---

## System Requirements

### Software Requirements
- **PHP**: 8.1+ (8.5+ recommended)
- **Database**: MySQL 8.0+ OR Oracle Database 19c+
- **Web Server**: Apache 2.4+, Nginx 1.20+, or similar
- **Composer**: 2.0+

### PHP Extensions Required
- `php-pdo`
- `php-pdo-mysql` (for MySQL)
- `php-oci8` (for Oracle - optional, only if using Oracle)
- `php-curl`
- `php-gd`
- `php-json`
- `php-mbstring`

---

## Local Development Setup

### 1. Installation
```bash
cd /path/to/awardee_app
composer install
```

### 2. Environment Configuration
```bash
# Copy the provided .env file (already configured for local development)
# The .env file is pre-configured for MySQL on localhost:3306
cat .env
```

**Current Local Configuration:**
```
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8084/'
database.default.hostname = 127.0.0.1
database.default.database = awardee_system
database.default.username = root
database.default.password = (empty)
database.default.DBDriver = MySQLi
database.default.port = 3306
```

### 3. Database Setup
```bash
# Run migrations
php spark migrate

# Seed initial data
php spark db:seed DatabaseSeeder
```

### 4. Start Development Server
```bash
php spark serve --host localhost --port 8084
```

Access at: `http://localhost:8084/login`
- **Username**: awardeeadmin
- **Password**: AwardeeAdmin2026!

---

## Production Deployment (Oracle)

### 1. Prepare .env.production File

Before deploying, update `.env.production` with your Oracle database credentials:

```bash
cp .env.production .env
```

**Update these values in .env:**
```ini
CI_ENVIRONMENT = production

app.baseURL = 'https://your-domain.com/'

# Oracle Database Configuration
database.default.hostname = oracle-server.your-domain.com
database.default.database = AWARDEE
database.default.username = awardee_user
database.default.password = your_secure_oracle_password
database.default.DBDriver = OCI8
database.default.port = 1521
database.default.charset = UTF8
```

### 2. Install PHP Oracle Extension
```bash
# For CentOS/RHEL
sudo yum install php-pecl-oci8

# For Ubuntu/Debian
sudo apt-get install php-oci8

# Verify installation
php -m | grep oci8
```

### 3. Configure Oracle Client

Install Oracle Instant Client on the application server:
```bash
# Download from Oracle's website:
# https://www.oracle.com/database/technologies/instant-client/linux-x86-64-downloads.html

# Extract and set environment variables
export LD_LIBRARY_PATH=/opt/oracle/instantclient_21_7:$LD_LIBRARY_PATH
export ORACLE_HOME=/opt/oracle/instantclient_21_7
```

### 4. Deploy Application
```bash
# Upload files to production server
rsync -av --exclude='vendor' --exclude='writable/logs' --exclude='writable/cache' \
    ./awardee_app/ user@production-server:/var/www/awardee_app/

# On production server:
cd /var/www/awardee_app
composer install --no-dev

# Set proper permissions
sudo chown -R www-data:www-data .
sudo chmod -R 755 app config public
sudo chmod -R 775 writable
```

### 5. Run Database Migrations (if needed)
```bash
# On production server
php spark migrate
php spark db:seed DatabaseSeeder  # Only for first deployment
```

### 6. Configure Web Server

**Apache Configuration (.htaccess):**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Redirect to public folder
    RewriteCond %{REQUEST_URI} ^/$ [OR]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

**Nginx Configuration:**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/awardee_app/public;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## Configuration Details

### Environment Variables (.env)

| Variable | Local | Production |
|----------|-------|-----------|
| `CI_ENVIRONMENT` | development | production |
| `app.baseURL` | http://localhost:8084/ | https://your-domain.com/ |
| `database.default.DBDriver` | MySQLi | OCI8 |
| `log.threshold` | 4 (debug) | 2 (warnings+errors) |

### Security Settings

**Important Security Measures:**
1. Set `CI_ENVIRONMENT = production` (disables debug toolbar)
2. Set strong passwords in .env
3. Configure firewall rules on Oracle database server
4. Use HTTPS/SSL certificates on production
5. Enable session storage in database (already configured)
6. Set file permissions: writable directory should be 775

### File Permissions

```bash
# Set correct permissions
sudo chmod 755 app config public
sudo chmod 775 writable writable/cache writable/logs writable/uploads writable/session
sudo chown -R www-data:www-data writable
```

---

## Troubleshooting

### Executive Dashboard Error
**Symptom**: Executive dashboard shows error when accessing.

**Solution**:
1. Verify all ExecutiveController methods exist:
   ```bash
   grep "public function" app/Controllers/ExecutiveController.php
   ```
2. Check database connectivity:
   ```bash
   php spark db:show
   ```
3. Verify database user has SELECT permissions on all tables

### Database Connection Issues
**Symptom**: "Cannot connect to database" error

**For MySQL:**
```bash
# Test connection
mysql -h 127.0.0.1 -u root -p awardee_system -e "SHOW TABLES;"
```

**For Oracle:**
```bash
# Test connection
sqlplus awardee_user@AWARDEE
```

### Resource Loading Issues (CSS/JS/Images)
**Symptom**: Resources not loading (404 errors)

**Solution**:
1. Verify `app.baseURL` in .env matches your domain
2. Check public folder permissions
3. Verify web server document root points to `public/` folder
4. Clear browser cache

---

## Maintenance

### Regular Backups
```bash
# MySQL Backup
mysqldump -u root -p awardee_system > backup_$(date +%Y%m%d).sql

# Oracle Backup (contact DBA)
# Usually handled by Oracle Enterprise Manager or RMAN
```

### Log Management
```bash
# View application logs
tail -f writable/logs/log-*.log

# Archive old logs
find writable/logs -name "log-*.log" -mtime +30 -delete
```

### Database Maintenance
```bash
# Show database status
php spark db:show

# Run any pending migrations
php spark migrate
```

---

## Support

For issues or questions:
1. Check application logs: `writable/logs/`
2. Review CodeIgniter documentation: https://codeigniter.com/user_guide/
3. Check database logs and error messages

---

## Version Information
- **Application**: AWARDEE Performance Management System
- **CodeIgniter**: 4.7.3
- **Last Updated**: 2026-08-13
- **Status**: Production Ready ✅

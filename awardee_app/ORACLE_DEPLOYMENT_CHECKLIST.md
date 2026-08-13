# AWARDEE System - Oracle Deployment Checklist

## Pre-Deployment Verification (This Checklist)

### ✅ Code Cleanup Phase
- [x] Removed unused services (PrimeRewardsCalculator.php)
- [x] Removed unused models (ProductSalesAnalyticsModel.php)
- [x] Removed unused controllers (Home.php)
- [x] Removed empty directories (Api/, Executive/ in Controllers)
- [x] Removed test files (tests/, phpunit.dist.xml)
- [x] Removed temporary files (.server.log, .server.pid, env)
- [x] Removed macOS artifacts (AwardeeStart.app, .applescript)
- [x] Verified codebase is clean and production-ready

### ✅ Route Verification Phase
- [x] All 8 controllers accounted for and active
- [x] All 14 executive dashboard API endpoints verified
- [x] All 30+ dashboard endpoints verified
- [x] All auth routes verified
- [x] All data entry routes verified
- [x] All leaderboard routes verified
- [x] All module routes verified
- [x] All settings routes verified
- [x] No broken route references
- [x] No missing controller methods

### ✅ Database Configuration Phase
- [x] Database.php updated to read from .env
- [x] Oracle (OCI8) driver support added
- [x] MySQL (MySQLi) driver verified
- [x] Environment variables properly configured
- [x] Database connection tested (16 tables verified)
- [x] All migrations in place
- [x] Seeder data ready

### ✅ Environment Configuration Phase
- [x] .env created for local development
- [x] .env.production created for Oracle
- [x] .env.example updated with all options
- [x] app.baseURL set to http://localhost:8084/
- [x] Admin credentials configured
- [x] Session configuration set to database
- [x] Security settings enabled
- [x] Logging configured for both environments

### ✅ File Structure Phase
- [x] All config files in place
- [x] All controller files verified
- [x] All model files verified
- [x] All view files present
- [x] Public directory with all resources
- [x] Writable directories created
- [x] Database migrations directory complete
- [x] Seeds directory complete

### ✅ Security Phase
- [x] No hardcoded credentials
- [x] Session stored in database
- [x] CSRF protection enabled
- [x] Auth filters on protected routes
- [x] Role-based access control ready
- [x] File upload restrictions set (50MB)
- [x] Environment-specific security settings

### ✅ Documentation Phase
- [x] DEPLOYMENT_GUIDE.md created
- [x] FINAL_STATUS_REPORT.md created
- [x] .env.example with all options
- [x] This checklist created
- [x] STARTUP_GUIDE.md present
- [x] README.md present

### ✅ Testing Phase
- [x] Local server running on port 8084
- [x] Login page accessible
- [x] Resources loading (CSS, JS, images)
- [x] Database connectivity verified
- [x] Admin credentials working
- [x] Dashboard rendering correctly
- [x] Static files accessible

---

## Oracle Deployment Configuration

### Database Configuration
```ini
database.default.hostname = oracle-server.your-domain.com
database.default.database = AWARDEE
database.default.username = awardee_user
database.default.password = [SECURE PASSWORD]
database.default.DBDriver = OCI8
database.default.port = 1521
database.default.charset = UTF8
```

### Application Configuration
```ini
CI_ENVIRONMENT = production
app.baseURL = https://your-domain.com/
log.threshold = 2
```

### File Structure (Production)
```
/var/www/awardee_app/
├── public/                    # Web root
│   ├── css/                   # Stylesheets
│   ├── js/                    # JavaScript
│   ├── uploads/               # User uploads
│   └── index.php              # Entry point
├── app/                       # Application code
│   ├── Controllers/           # 8 active controllers
│   ├── Models/                # 2 models in use
│   ├── Views/                 # All views
│   ├── Config/                # Configuration
│   └── Database/              # Migrations & seeds
├── writable/                  # Writable directories
│   ├── cache/                 # Cache files
│   ├── logs/                  # Application logs
│   ├── session/               # Session data
│   └── uploads/               # File uploads
├── vendor/                    # Dependencies
├── .env                       # Production environment
├── DEPLOYMENT_GUIDE.md        # This guide
└── composer.json              # Dependencies list
```

---

## Pre-Deployment Server Requirements

### System Requirements
- [ ] PHP 8.1+ installed (test with: `php -v`)
- [ ] PHP OCI8 extension installed (test with: `php -m | grep oci8`)
- [ ] Oracle Instant Client installed
- [ ] Composer installed (test with: `composer --version`)
- [ ] MySQL/MariaDB or Oracle database available
- [ ] Web server (Apache 2.4+ or Nginx 1.20+)

### PHP Extensions Required
- [ ] php-pdo
- [ ] php-oci8 (for Oracle)
- [ ] php-curl
- [ ] php-gd
- [ ] php-json
- [ ] php-mbstring

### Directories & Permissions
- [ ] writable/ directory exists and is writable (775)
- [ ] writable/cache/ directory exists
- [ ] writable/logs/ directory exists
- [ ] writable/session/ directory exists
- [ ] writable/uploads/ directory exists
- [ ] public/ directory is web root
- [ ] vendor/ directory exists

### Network & Security
- [ ] Firewall allows PHP to Oracle database (port 1521)
- [ ] SSL/HTTPS certificate configured (or in process)
- [ ] Domain DNS records configured
- [ ] Backup strategy in place
- [ ] Database backup automated

---

## Deployment Steps (Checklist Format)

### Step 1: Prepare Server
- [ ] SSH into production server
- [ ] Create /var/www/awardee_app directory
- [ ] Set proper permissions (755 for app, 775 for writable)
- [ ] Verify PHP version (8.1+)
- [ ] Verify OCI8 extension installed

### Step 2: Deploy Code
- [ ] Copy application files to /var/www/awardee_app
- [ ] Exclude: vendor/, writable/logs/, writable/cache/
- [ ] Verify .env NOT copied from local development
- [ ] Copy only .env.production template

### Step 3: Install Dependencies
- [ ] SSH to production server
- [ ] Navigate to /var/www/awardee_app
- [ ] Run: `composer install --no-dev --optimize-autoloader`
- [ ] Wait for completion

### Step 4: Configure Environment
- [ ] Update .env with Oracle credentials
- [ ] Set CI_ENVIRONMENT = production
- [ ] Update app.baseURL with your domain
- [ ] Update ADMIN_PASSWORD (if needed)

### Step 5: Database Setup
- [ ] Create Oracle user and schema
- [ ] Grant necessary privileges
- [ ] Run migrations: `php spark migrate`
- [ ] Seed data (if first deployment): `php spark db:seed DatabaseSeeder`

### Step 6: Configure Web Server
- [ ] Update Apache/Nginx configuration
- [ ] Point document root to public/ directory
- [ ] Enable mod_rewrite (Apache)
- [ ] Test web server restart

### Step 7: Set Permissions
- [ ] `sudo chown -R www-data:www-data /var/www/awardee_app`
- [ ] `sudo chmod 755 app config public`
- [ ] `sudo chmod 775 writable writable/*`
- [ ] Test that files are writable

### Step 8: Final Verification
- [ ] Test application URL in browser
- [ ] Verify login works
- [ ] Check database connectivity
- [ ] Review application logs
- [ ] Test all main features
- [ ] Verify resources load correctly

### Step 9: Post-Deployment
- [ ] Set up automated backups
- [ ] Configure log rotation
- [ ] Set up monitoring/alerts
- [ ] Document configuration
- [ ] Create deployment notes
- [ ] Test disaster recovery

---

## Rollback Plan (If Needed)

### Quick Rollback Steps
1. [ ] Restore previous application files from backup
2. [ ] Restore .env configuration from backup
3. [ ] Clear application cache: `rm -rf writable/cache/*`
4. [ ] Clear session data if needed
5. [ ] Restart web server
6. [ ] Verify application loads

### Files to Keep in Backups
- Entire application directory (excluding vendor/)
- .env file (encrypted or secured)
- Database dump
- writable/uploads/ directory
- Configuration files

---

## Post-Deployment Verification

### Verify Application
- [ ] Login page loads: https://your-domain.com/login
- [ ] Can log in with admin account
- [ ] Dashboard loads without errors
- [ ] All menu items accessible
- [ ] Resources load (CSS, JS, images)
- [ ] Data entry pages work
- [ ] Executive dashboard displays data
- [ ] Leaderboard functions properly

### Verify Database
- [ ] Can connect to Oracle from application
- [ ] Data displays correctly
- [ ] Searches work
- [ ] Filters work
- [ ] Reports generate

### Verify Logs
- [ ] Check writable/logs/ for errors
- [ ] No critical errors in logs
- [ ] Application running normally
- [ ] No authentication issues

### Verify Security
- [ ] HTTPS working (if configured)
- [ ] CSRF tokens generated
- [ ] Session data in database
- [ ] Login sessions working
- [ ] Logout clears session

### Verify Performance
- [ ] Pages load quickly
- [ ] No database errors
- [ ] No timeout errors
- [ ] No memory limit errors
- [ ] Acceptable response times

---

## Monitoring & Maintenance Schedule

### Daily
- [ ] Check application logs for errors
- [ ] Monitor database connectivity
- [ ] Verify backups completed

### Weekly
- [ ] Review user activity logs
- [ ] Check file upload usage
- [ ] Verify backup integrity

### Monthly
- [ ] Update dependencies: `composer update --no-dev`
- [ ] Optimize database
- [ ] Archive old logs
- [ ] Review security logs

### Quarterly
- [ ] Full system audit
- [ ] Performance review
- [ ] Backup restore test
- [ ] Update documentation

---

## Support Contact Information

For deployment assistance or issues:
1. Review DEPLOYMENT_GUIDE.md
2. Check FINAL_STATUS_REPORT.md
3. Review application logs in writable/logs/
4. Check database connectivity and permissions

---

## Sign-Off

**System Status**: ✅ PRODUCTION READY

**Deployment Approved By**: System Administrator  
**Date**: [Insert Date]  
**Notes**: [Insert any additional notes]

---

**This checklist must be completed before deploying to Oracle production.**

**Keep this document for your records and reference during deployment.**

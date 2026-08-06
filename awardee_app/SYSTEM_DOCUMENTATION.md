# AWARDEE System - Complete System Documentation

## Table of Contents
1. [System Overview](#1-system-overview)
2. [Technology Stack](#2-technology-stack)
3. [Database Architecture](#3-database-architecture)
4. [System Architecture](#4-system-architecture)
5. [Authentication & Authorization](#5-authentication--authorization)
6. [Modules & Features](#6-modules--features)
7. [API Endpoints](#7-api-endpoints)
8. [Data Entry System](#8-data-entry-system)
9. [Leaderboard System](#9-leaderboard-system)
10. [Executive Dashboard](#10-executive-dashboard)
11. [File Upload System](#11-file-upload-system)
12. [Deployment Guide](#12-deployment-guide)
13. [Troubleshooting](#13-troubleshooting)
14. [Security Considerations](#14-security-considerations)

---

## 1. System Overview

The **AWARDEE System** is a comprehensive performance management and awardee tracking platform built for PHINMA UGC. It manages three main award programs:

- **Sales Excellence Awardee** - Tracks individual sales performance
- **Top Branch Recognition** - Tracks branch/office performance
- **Elite Circle** - Tracks top performers by volume and contribution margin

The system provides:
- Data entry and management for all award programs
- Real-time leaderboard tracking with monthly rankings
- Executive analytics dashboard with KPIs and trends
- Photo upload and placement generation for awardees
- Excel export functionality
- User management and role-based access control

---

## 2. Technology Stack

| Component | Technology |
|-----------|-----------|
| **Framework** | CodeIgniter 4.7.3 (PHP MVC Framework) |
| **PHP Version** | PHP 8.2+ |
| **Database** | MySQL 8.0+ (via MySQLi driver) |
| **Frontend** | HTML5, CSS3, JavaScript (vanilla + Chart.js) |
| **Charts** | Chart.js (via CDN) |
| **Excel Export** | PhpSpreadsheet |
| **Image Processing** | html2canvas (client-side) |
| **Server** | Apache/Nginx with PHP-FPM |
| **Development Server** | PHP Built-in Server (`php spark serve`) |

---

## 3. Database Architecture

### 3.1 Database Configuration

The system uses **MySQL** as its primary database. Configuration is in `app/Config/Database.php` and `.env`:

```env
database.default.hostname = 127.0.0.1
database.default.database = awardee_system
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

### 3.2 Core Tables

#### Users Table (`users`)
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment ID |
| fullname | VARCHAR(150) | User's full name |
| username | VARCHAR(50) | Unique username |
| email | VARCHAR(120) | Unique email |
| password_hash | VARCHAR(255) | Bcrypt password hash |
| profile_pic | VARCHAR(255) | Profile picture path |
| role | ENUM | admin, management |
| status | ENUM | active, inactive |
| created_at | DATETIME | Creation timestamp |
| updated_at | DATETIME | Last update timestamp |

#### Sales Excellence Data (`sales_excellence_data`)
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment ID |
| uuid | VARCHAR(36) | Unique record identifier |
| region | VARCHAR(100) | Region name |
| name | VARCHAR(200) | Participant name |
| area | VARCHAR(200) | Area/territory |
| position | VARCHAR(200) | Job position |
| category | ENUM | attainment, margin |
| attainment_percent | DECIMAL(10,2) | Attainment percentage |
| actual_volume | DECIMAL(15,2) | Actual volume |
| budget | DECIMAL(15,2) | Budget amount |
| revenue | DECIMAL(15,2) | Revenue amount |
| actual_cm | DECIMAL(15,2) | Actual contribution margin |
| price_lf | DECIMAL(10,2) | Price per linear foot |
| margin | DECIMAL(10,2) | Margin percentage |
| growth | DECIMAL(10,2) | Growth percentage |
| sales_month | TINYINT | Month (1-12) |
| sales_year | SMALLINT | Year |
| photo | VARCHAR(255) | Photo filename |
| created_at | DATETIME | Creation timestamp |
| updated_at | DATETIME | Last update timestamp |

#### Top Branch Data (`top_branch_data`)
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment ID |
| uuid | VARCHAR(36) | Unique record identifier |
| region | VARCHAR(100) | Region name |
| sales_office | VARCHAR(200) | Sales office |
| name | VARCHAR(200) | Participant name |
| area | VARCHAR(200) | Area/territory |
| position | VARCHAR(200) | Job position |
| category | ENUM | growth, attainment, margin |
| growth_percent | DECIMAL(10,2) | Growth percentage |
| attainment_percent | DECIMAL(10,2) | Attainment percentage |
| actual | DECIMAL(15,2) | Actual value |
| budget | DECIMAL(15,2) | Budget amount |
| last_month | DECIMAL(15,2) | Last month value |
| current_month | DECIMAL(15,2) | Current month value |
| revenue | DECIMAL(15,2) | Revenue amount |
| sales_month | TINYINT | Month (1-12) |
| sales_year | SMALLINT | Year |
| photo | VARCHAR(255) | Photo filename |
| created_at | DATETIME | Creation timestamp |
| updated_at | DATETIME | Last update timestamp |

#### Elite Circle Data (`elite_circle_data`)
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment ID |
| uuid | VARCHAR(36) | Unique record identifier |
| region | VARCHAR(100) | Region name |
| company | VARCHAR(200) | Company name |
| name | VARCHAR(200) | Participant name |
| area | VARCHAR(200) | Area/territory |
| position | VARCHAR(200) | Job position |
| category | VARCHAR(50) | volume, margin |
| quantity_invoice | DECIMAL(15,2) | Quantity invoiced |
| gross_amount | DECIMAL(15,2) | Gross amount |
| volume | DECIMAL(15,2) | Volume |
| revenue | DECIMAL(15,2) | Revenue amount |
| sales_month | TINYINT | Month (1-12) |
| sales_year | SMALLINT | Year |
| photo | VARCHAR(255) | Photo filename |
| created_at | DATETIME | Creation timestamp |
| updated_at | DATETIME | Last update timestamp |

#### Elite Circle Summary (`elite_circle_summary`)
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment ID |
| uuid | VARCHAR(36) | Unique record identifier |
| quarter_year | VARCHAR(20) | e.g., Q1-2026 |
| region | VARCHAR(100) | Region name |
| top_volume_name | VARCHAR(200) | Top volume performer |
| top_volume_area | VARCHAR(200) | Top volume area |
| top_volume_position | VARCHAR(200) | Top volume position |
| top_volume_value | DECIMAL(15,2) | Top volume value |
| top_cm_name | VARCHAR(200) | Top CM performer |
| top_cm_area | VARCHAR(200) | Top CM area |
| top_cm_position | VARCHAR(200) | Top CM position |
| top_cm_value | DECIMAL(15,2) | Top CM value |
| total_volume | DECIMAL(15,2) | Total volume |
| total_cm | DECIMAL(15,2) | Total contribution margin |
| photo | VARCHAR(255) | Photo filename |
| generated_at | DATETIME | Generation timestamp |
| created_at | DATETIME | Creation timestamp |

#### Elite Circle Records (`elite_circle_records`)
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment ID |
| uuid | VARCHAR(36) | Unique record identifier |
| region | VARCHAR(100) | Region name |
| name | VARCHAR(200) | Participant name |
| area | VARCHAR(200) | Area/territory |
| position | VARCHAR(200) | Job position |
| category | ENUM | volume, cm |
| sales_month | TINYINT | Month (1-12) |
| sales_year | SMALLINT | Year |
| sales_volume | DECIMAL(15,2) | Sales volume |
| sales_cm | DECIMAL(15,2) | Sales contribution margin |
| photo | VARCHAR(255) | Photo filename |
| created_at | DATETIME | Creation timestamp |
| updated_at | DATETIME | Last update timestamp |

#### Sales Excellence Leaderboard (`sales_excellence_leaderboard`)
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment ID |
| uuid | VARCHAR(36) | Unique record identifier |
| name | VARCHAR(200) | Participant name |
| region | VARCHAR(100) | Region name |
| area | VARCHAR(200) | Area/territory |
| category | ENUM | attainment, margin |
| jan_rank - dec_rank | TINYINT | Monthly rank (0-3) |
| total_top | TINYINT | Total top-3 finishes |
| sales_year | SMALLINT | Year |
| created_at | DATETIME | Creation timestamp |
| updated_at | DATETIME | Last update timestamp |

#### Legacy Tables (for backward compatibility)
- `regions` - Region reference data
- `stores` - Store reference data
- `dealers` - Dealer reference data
- `sales_records` - Legacy sales records
- `awardees` - Legacy awardee records
- `grand_slam_awardees` - Legacy grand slam records
- `product_sales` - Legacy product sales data
- `activity_logs` - System activity logs
- `analytics_sessions` - Analytics session data
- `awardee_photos` - Awardee photo mappings

---

## 4. System Architecture

### 4.1 Directory Structure
```
awardee_app/
├── app/
│   ├── Config/          # Configuration files
│   ├── Controllers/     # MVC Controllers
│   ├── Database/
│   │   ├── Migrations/  # Database migrations
│   │   └── Seeds/       # Database seeders
│   ├── Filters/         # Auth & Role filters
│   ├── Helpers/         # Helper functions
│   ├── Models/          # MVC Models
│   ├── Services/        # Business logic services
│   └── Views/           # MVC Views (templates)
├── public/
│   ├── css/             # Stylesheets
│   ├── js/              # JavaScript files
│   └── uploads/         # Uploaded files
│       ├── photos/      # Participant photos
│       └── profiles/    # User profile pictures
├── writable/            # Writable files (logs, cache)
├── .env                 # Environment configuration
└── spark                # CLI tool
```

### 4.2 Controllers
| Controller | Purpose |
|-----------|---------|
| `AuthController` | Login, logout, password reset |
| `DashboardController` | Main dashboard with analytics |
| `DataEntryController` | CRUD for all data entry tables |
| `LeaderboardController` | Leaderboard calculations and display |
| `ExecutiveController` | Executive analytics dashboard |
| `AnalyticsController` | AI insights and photo management |
| `SettingsController` | User profile and admin management |
| `ModuleController` | Module skeleton pages |

### 4.3 Models
| Model | Purpose |
|-------|---------|
| `UserModel` | User data management |
| `DashboardAnalyticsModel` | Dashboard analytics queries |
| `ProductSalesAnalyticsModel` | Product sales analytics |

### 4.4 Services
| Service | Purpose |
|---------|---------|
| `PrimeRewardsCalculator` | Points calculation for rewards program |

---

## 5. Authentication & Authorization

### 5.1 Login Flow
1. User navigates to `/login`
2. Submits username/email + password
3. System validates credentials against `users` table
4. Password verified using `password_verify()`
5. Session created with user data
6. Session regenerated for security

### 5.2 Session Data
```php
[
    'user_id'    => 1,
    'fullname'   => 'Admin User',
    'username'   => 'admin',
    'email'      => 'admin@example.com',
    'role'       => 'admin',
    'isLoggedIn' => true,
]
```

### 5.3 Access Control
- **AuthFilter**: Applied to all protected routes
- **RoleFilter**: Applied to role-specific routes
- **Roles**: `admin`, `management`

### 5.4 Default Admin Account
The default admin account is created via the `AdminUserSeeder`:
- **Username**: `awardeeadmin`
- **Email**: `awardee.admin@gmail.com`
- **Password**: `12345678`

---

## 6. Modules & Features

### 6.1 Dashboard (`/dashboard`)
- KPI summary cards (total dealers, stores, regions, monthly sales)
- Monthly sales trend chart
- Regional performance chart
- Top dealers table
- Award distribution chart
- Smart insights panel

### 6.2 Data Entry (`/data-entry`)
- **Sales Excellence Awardee**: CRUD operations
- **Top Branch Recognition**: CRUD operations
- **Elite Circle**: CRUD operations
- Photo upload for each record
- Excel export for each dashboard type
- Clear data functionality

### 6.3 Leaderboard (`/leaderboard`)
- Grand Slam tracking for all three dashboards
- Monthly ranking per region
- Year selection
- Save leaderboard data
- Auto-calculation fallback

### 6.4 Executive Dashboard (`/executive`)
- KPI summary (revenue, growth, volume, CM)
- Performance trend charts
- Regional performance comparison
- Top 10 performers
- Achievement distribution
- Recent achievements
- Employee search and profiles
- AI insights
- Photo placement generation

### 6.5 Settings (`/settings`)
- Profile management
- Password change
- Profile picture upload
- Admin user management
- Admin registration

---

## 7. API Endpoints

### 7.1 Public Routes
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Redirect to login |
| GET/POST | `/login` | Login page/action |
| GET | `/logout` | Logout |
| GET/POST | `/forgot-password` | Password reset |

### 7.2 Data Entry API
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/data/se-list` | List Sales Excellence data |
| POST | `/data/se-create` | Create SE record |
| PUT/POST | `/data/se-update/{uuid}` | Update SE record |
| DELETE | `/data/se-delete/{uuid}` | Delete SE record |
| GET | `/data/tb-list` | List Top Branch data |
| POST | `/data/tb-create` | Create TB record |
| PUT/POST | `/data/tb-update/{uuid}` | Update TB record |
| DELETE | `/data/tb-delete/{uuid}` | Delete TB record |
| GET | `/data/ec-list` | List Elite Circle summary |
| POST | `/data/ec-create` | Create EC summary |
| PUT/POST | `/data/ec-update/{uuid}` | Update EC summary |
| DELETE | `/data/ec-delete/{uuid}` | Delete EC summary |
| GET | `/data/ec-records-list` | List EC records |
| POST | `/data/ec-records-create` | Create EC record |
| PUT | `/data/ec-records-update/{uuid}` | Update EC record |
| DELETE | `/data/ec-records-delete/{uuid}` | Delete EC record |
| GET | `/data/ec-data-list` | List Elite Circle data |
| POST | `/data/ec-data-create` | Create EC data |
| PUT/POST | `/data/ec-data-update/{uuid}` | Update EC data |
| DELETE | `/data/ec-data-delete/{uuid}` | Delete EC data |
| GET | `/data/dashboard` | Get all dashboard data |
| POST | `/data/generate-elite-circle` | Generate EC summary |
| POST | `/data/delete-elite-circle` | Delete all EC summary |
| GET | `/data/export/{type}` | Export to Excel |
| POST | `/data/clear-all` | Clear all data |
| POST | `/data/clear-type/{type}` | Clear by type |
| POST | `/data/upload-entry-photo` | Upload record photo |
| GET | `/data/get-entry-photo/{uuid}` | Get record photo |

### 7.3 Leaderboard API
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/leaderboard/api/se` | Grand Slam SE data |
| GET | `/leaderboard/api/tb` | Grand Slam TB data |
| GET | `/leaderboard/api/ec` | Grand Slam EC data |
| GET | `/leaderboard/api/ec-monthly` | EC monthly ranking |
| GET | `/leaderboard/api/years` | Available years |
| POST | `/leaderboard/api/save` | Save leaderboard |
| GET | `/leaderboard/api/leaderboard-data` | Get leaderboard data |

### 7.4 Executive Dashboard API
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/executive/api/all-data` | All dashboard data |
| GET | `/executive/api/kpi-summary` | KPI summary |
| GET | `/executive/api/performance-trend` | Performance trend |
| GET | `/executive/api/dashboard-comparison` | Dashboard comparison |
| GET | `/executive/api/regional-performance` | Regional performance |
| GET | `/executive/api/top-performers` | Top 10 performers |
| GET | `/executive/api/achievement-distribution` | Achievement distribution |
| GET | `/executive/api/recent-achievements` | Recent achievements |
| GET | `/executive/api/employee-search` | Search employees |
| GET | `/executive/api/participant-names` | Get participant names |
| GET | `/executive/api/employee-profile` | Get employee profile |
| GET | `/executive/api/participant-profile` | Get participant profile |
| GET | `/executive/api/available-months` | Available months |
| GET | `/executive/export` | Export data |

### 7.5 Analytics API
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/data/upload-photo` | Upload awardee photo |
| GET | `/data/get-photos` | Get awardee photos |
| GET | `/analytics/ai-insights` | AI insights |

---

## 8. Data Entry System

### 8.1 Sales Excellence Awardee
- **Categories**: `attainment`, `margin`
- **Key Metrics**: attainment %, actual volume, budget, revenue, actual CM, price/LF, margin %, growth %
- **Ranking**: Per-region ranking by attainment % (attainment category) or actual CM (margin category)

### 8.2 Top Branch Recognition
- **Categories**: `growth`, `attainment`, `margin`
- **Key Metrics**: growth %, attainment %, actual, budget, last month, current month, revenue
- **Ranking**: Per-region ranking by growth % or attainment %

### 8.3 Elite Circle
- **Categories**: `volume`, `margin`
- **Key Metrics**: quantity invoice, gross amount, volume, revenue
- **Ranking**: Per-region ranking by volume or gross amount

### 8.4 Elite Circle Summary Generation
The system can auto-generate quarterly summaries from SE and TB data:
1. Groups data by region
2. Identifies top volume performer (highest actual_volume)
3. Identifies top CM performer (highest actual_cm)
4. Calculates total volume and CM for the region
5. Stores in `elite_circle_summary` table

---

## 9. Leaderboard System

### 9.1 Ranking Logic
- **Sales Excellence**: Ranked per region per month by attainment % or actual CM
- **Top Branch**: Counts months with data (no ranking)
- **Elite Circle**: Ranked per region per month by volume or gross amount

### 9.2 Points System
- **Top 3 finishes**: 1 point per top-3 finish per month
- **Total Top**: Sum of all top-3 finishes across the year
- **Grand Slam**: Participant with highest total points

### 9.3 Save Leaderboard
The `saveLeaderboard` endpoint:
1. Processes SE data (attainment + margin categories)
2. Processes TB data (growth + attainment + margin categories)
3. Processes EC data (volume + margin categories)
4. Saves monthly ranks to leaderboard tables
5. Calculates total_top for each participant

---

## 10. Executive Dashboard

### 10.1 KPI Calculations
- **Total Revenue**: Sum of revenue from SE + TB + EC data
- **AVS Growth**: Average growth % from TB data
- **Combined Volume**: Sum of volume from EC + EC records
- **Combined CM**: Sum of gross amount from EC + EC records

### 10.2 Performance Trend
Monthly data for the selected year:
- SE Revenue by month
- TB Growth % by month
- EC Volume by month
- EC CM by month

### 10.3 Regional Performance
For each region (South Luzon, North & Central Luzon, Visayas, Mindanao):
- Revenue (current + previous month)
- Growth % (current + previous month)
- CM (current + previous month)
- Volume (current + previous month)

### 10.4 Top Performers
Calculated based on leaderboard points:
1. SE: From `sales_excellence_leaderboard` total_top
2. TB: Count of months with score data
3. EC: Count of top-3 finishes per region

---

## 11. File Upload System

### 11.1 Photo Upload
- **Location**: `public/uploads/photos/`
- **Format**: Random generated filename
- **Database**: Photo filename stored in record's `photo` column
- **Access**: `/uploads/photos/{filename}`

### 11.2 Profile Picture Upload
- **Location**: `public/uploads/profiles/`
- **Format**: `profile_{user_id}_{timestamp}.{ext}`
- **Allowed Types**: JPG, PNG, GIF, WebP
- **Max Size**: 2MB

### 11.3 Photo Placement Generation
The executive dashboard can generate placement images:
- Uses html2canvas to capture the photo square
- Supports PNG and JPG export
- Includes participant name, position, and category

---

## 12. Deployment Guide

### 12.1 Prerequisites
- PHP 8.2+
- MySQL 8.0+
- Composer
- Web server (Apache/Nginx)

### 12.2 Installation Steps

1. **Clone/Extract the project**
```bash
cd /var/www/html
# Extract or clone the project
```

2. **Install dependencies**
```bash
cd awardee_app
composer install --no-dev --optimize-autoloader
```

3. **Configure environment**
```bash
cp .env.example .env
# Edit .env with your database credentials
```

4. **Set database credentials in `.env`**
```env
CI_ENVIRONMENT = production
app.baseURL = 'http://your-domain.com/'
database.default.hostname = 127.0.0.1
database.default.database = awardee_system
database.default.username = your_user
database.default.password = your_password
database.default.DBDriver = MySQLi
```

5. **Create database**
```sql
CREATE DATABASE awardee_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

6. **Run migrations**
```bash
php spark migrate
```

7. **Seed default admin**
```bash
php spark db:seed AdminUserSeeder
```

8. **Set permissions**
```bash
chmod -R 755 writable/
chmod -R 755 public/uploads/
```

9. **Configure web server**
   - Point document root to `public/` directory
   - Enable URL rewriting (mod_rewrite for Apache)

10. **Set production environment**
```env
CI_ENVIRONMENT = production
```

### 12.3 Production Checklist
- [ ] Set `CI_ENVIRONMENT = production`
- [ ] Disable debug toolbar
- [ ] Set proper file permissions
- [ ] Enable HTTPS
- [ ] Configure proper error logging
- [ ] Set strong database credentials
- [ ] Change default admin password
- [ ] Configure backup strategy

---

## 13. Troubleshooting

### 13.1 Common Issues

**Database Connection Error**
- Check MySQL is running
- Verify credentials in `.env`
- Check database exists

**404 Page Not Found**
- Check URL structure
- Verify routes in `app/Config/Routes.php`
- Clear cache: `php spark cache:clear`

**CSRF Token Mismatch**
- Ensure CSRF token is included in forms
- Check CSRF exclusions in `app/Config/Filters.php`

**Photo Upload Fails**
- Check `public/uploads/photos/` directory exists
- Verify directory permissions (755)
- Check file size limits

**Excel Export Fails**
- Ensure PhpSpreadsheet is installed
- Check memory limits for large datasets

### 13.2 Logs
- Application logs: `writable/logs/`
- Debug toolbar: `writable/debugbar/`
- Session files: `writable/session/`

### 13.3 Cache
Clear all caches:
```bash
php spark cache:clear
```

---

## 14. Security Considerations

### 14.1 Implemented Security
- **Password Hashing**: Bcrypt via `password_hash()`
- **Session Regeneration**: On login
- **CSRF Protection**: Global filter with route exclusions
- **Input Validation**: Server-side validation for all inputs
- **SQL Injection Prevention**: Query Builder with parameter binding
- **XSS Protection**: Output escaping in views
- **File Upload Validation**: MIME type and size checks
- **Role-Based Access**: AuthFilter and RoleFilter

### 14.2 Best Practices
- Change default admin password immediately
- Use strong database credentials
- Keep PHP and dependencies updated
- Regular database backups
- Monitor application logs
- Use HTTPS in production
- Restrict file upload permissions

### 14.3 Security Notes
- API routes are CSRF-excluded for fetch-based operations
- Photo uploads are validated for type and size
- User passwords are never stored in plain text
- Session data is encrypted by default

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-08-06 | Initial production release |

---

*Documentation generated for the AWARDEE System - PHINMA UGC Performance Management Platform*
# AWARDEE System - Deployment Guide to Vercel

## Prerequisites
1. GitHub account: **arvinMbacsal** (configured)
2. Vercel account
3. Deployed database (MySQL/MariaDB)

## Deployment Steps

### Step 1: Create GitHub Repository
1. Go to [GitHub.com](https://github.com/new)
2. Create a new repository named `awardee-system`
3. Make it **Public** or **Private** (your choice)
4. Do NOT initialize with README (we already have one)

### Step 2: Push to GitHub

After creating the repository, run these commands in your terminal:

```bash
cd /Users/jhonarvin/Desktop/AWARDEE/awardee_app

# Add remote (replace USERNAME/REPO with your actual repo)
git remote add origin https://github.com/arvinMbacsal/awardee-system.git

# Rename branch to main (if needed)
git branch -M main

# Push to GitHub
git push -u origin main
```

### Step 3: Set Up Production Database

Before deploying to Vercel, you need a cloud-hosted database. Choose one:

**Option A: PlanetScale (Free tier available)**
1. Go to [PlanetScale.com](https://planetscale.com)
2. Create a new database
3. Get connection string: `mysql://[username]:[password]@[host]:3306/awardee_system`

**Option B: AWS RDS**
1. Create a MySQL database
2. Get connection details

**Option C: Other MySQL hosting (DigitalOcean, etc.)**

### Step 4: Connect Vercel to GitHub

1. Go to [Vercel Dashboard](https://vercel.com/dashboard)
2. Click "Add New..." → "Project"
3. Import your GitHub repository (arvinMbacsal/awardee-system)
4. Vercel will auto-detect the configuration

### Step 5: Configure Environment Variables in Vercel

In Vercel project settings, add these environment variables:

```
CI_ENVIRONMENT = production
DB_HOST = your-database-host
DB_NAME = awardee_system
DB_USER = your-db-username
DB_PASSWORD = your-db-password
DB_PORT = 3306
ENCRYPTION_KEY = your-32-char-hex-key
```

To generate `ENCRYPTION_KEY`:
```bash
php -r "echo bin2hex(random_bytes(16));"
```

### Step 6: Deploy

1. Vercel will automatically deploy when you push to main branch
2. Check Vercel dashboard for deployment status
3. Your app will be live at: `https://your-project-name.vercel.app`

## Post-Deployment

### Run Database Migrations
After deployment, run migrations on production database:

```bash
php spark migrate
```

### Update Base URL
After getting your Vercel domain, update `.env.production`:
```
app.baseURL = 'https://your-project-name.vercel.app/'
```

Then commit and push:
```bash
git add .env.production
git commit -m "Update production base URL"
git push
```

## Troubleshooting

### PHP Runtime Issue
If you see PHP errors, check:
- PHP version compatibility (requires PHP 8.2+)
- Composer dependencies: run `composer install --no-dev`

### Database Connection Error
- Verify DB credentials in Vercel env variables
- Check database server is accessible from Vercel (no IP restrictions)
- Test connection locally first

### Rewrite Rules Not Working
- The `vercel.json` handles routing to `public/index.php`
- If pages don't load, verify `app/Config/Routes.php` is correct

## Files Created for Deployment

1. **vercel.json** - Vercel configuration
2. **.env.production** - Production environment variables
3. **.vercelignore** - Files to ignore during deployment

## Support

For issues with:
- **CodeIgniter 4**: https://forum.codeigniter.com/
- **Vercel PHP**: https://vercel.com/docs/runtimes/php

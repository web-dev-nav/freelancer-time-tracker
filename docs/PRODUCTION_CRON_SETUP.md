# Production Cron Setup for tracker.codelone.com

## Server Setup

Your application is now hosted at: **https://tracker.codelone.com/**

This guide will help you set up automated backups on your production server.

## Step 1: Identify Your Server Environment

First, determine your hosting environment:

### Common Hosting Types:

1. **Shared Hosting** (cPanel, Plesk, etc.)
2. **VPS/Cloud Server** (DigitalOcean, AWS, Linode, etc.)
3. **Managed Laravel Hosting** (Laravel Forge, Ploi, etc.)

## Step 2: Setup Based on Hosting Type

### Option A: cPanel (Most Common Shared Hosting)

1. **Login to cPanel**
   - Go to your hosting control panel
   - Find "Cron Jobs" under "Advanced" section

2. **Add New Cron Job:**
   - **Common Settings:** Select "Once Per Minute (* * * * *)"
   - **Command:**
     ```bash
     cd /home/username/public_html/tracker && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
     ```

   **Replace:**
   - `/home/username/public_html/tracker` with your actual project path
   - `/usr/bin/php` with your PHP path (usually shown in cPanel)

3. **Click "Add New Cron Job"**

4. **Find Your Project Path:**
   - SSH into your server: `ssh username@tracker.codelone.com`
   - Run: `pwd` (shows current directory)
   - Usually: `/home/username/domains/tracker.codelone.com/public_html/` or similar

5. **Find Your PHP Path:**
   - In cPanel terminal or SSH: `which php`
   - Common paths:
     - `/usr/bin/php`
     - `/usr/local/bin/php`
     - `/opt/cpanel/ea-php82/root/usr/bin/php` (cPanel)

### Option B: VPS/Cloud Server (SSH Access)

1. **SSH into your server:**
   ```bash
   ssh root@tracker.codelone.com
   # or
   ssh username@tracker.codelone.com
   ```

2. **Edit crontab:**
   ```bash
   crontab -e
   ```

3. **Add this line:**
   ```bash
   * * * * * cd /var/www/tracker.codelone.com && php artisan schedule:run >> /dev/null 2>&1
   ```

   **Common production paths:**
   - `/var/www/tracker.codelone.com`
   - `/var/www/html/tracker.codelone.com`
   - `/home/username/tracker.codelone.com`
   - `/opt/tracker.codelone.com`

4. **Save and exit** (Ctrl+X, Y, Enter)

5. **Verify crontab:**
   ```bash
   crontab -l
   ```

### Option C: Laravel Forge

1. **Login to Laravel Forge**
2. **Go to your server → Your site → Scheduler**
3. **The scheduler is automatically configured!**
4. **No manual setup needed** ✅

### Option D: Plesk Control Panel

1. **Login to Plesk**
2. **Go to: Tools & Settings → Scheduled Tasks (Cron Jobs)**
3. **Add new task:**
   - **Command:**
     ```bash
     cd /var/www/vhosts/tracker.codelone.com/httpdocs && /usr/bin/php artisan schedule:run
     ```
   - **Run:** Every minute
   - **User:** Your web user (usually your domain name)
4. **Save**

## Step 3: Verify Setup

### Method 1: Check via SSH

```bash
# SSH into your server
ssh username@tracker.codelone.com

# Navigate to project
cd /path/to/your/project

# Check scheduled tasks
php artisan schedule:list

# Run scheduler manually to test
php artisan schedule:run

# Check if backups directory exists and is writable
ls -la storage/app/backups/
```

### Method 2: Check via cPanel File Manager

1. Navigate to: `storage/app/backups/`
2. Wait 1-2 days (until next scheduled backup)
3. Check for new backup files:
   - `project-*-backup.sql`
   - `database-full-*-backup.sql`

### Method 3: Manual Test

Run backups manually via SSH:

```bash
# SSH into server
ssh username@tracker.codelone.com

# Navigate to project
cd /path/to/your/project

# Test project backup
php artisan backup:projects

# Test database backup
php artisan backup:database

# Check results
ls -lh storage/app/backups/
```

## Step 4: Set Permissions (Important!)

```bash
# SSH into your server
ssh username@tracker.codelone.com

# Navigate to project
cd /path/to/your/project

# Set proper permissions
chmod -R 775 storage/app/backups
chown -R www-data:www-data storage/app/backups

# For cPanel (replace 'username' with your cPanel username)
chown -R username:username storage/app/backups

# Verify
ls -la storage/app/backups/
```

## Common Issues & Solutions

### Issue 1: "Permission Denied"

**Solution:**
```bash
chmod -R 775 storage/
chown -R www-data:www-data storage/
# Or for cPanel:
chown -R username:username storage/
```

### Issue 2: "PHP Command Not Found"

**Solution:** Use full PHP path in cron:
```bash
# Find PHP path
which php
# or
whereis php

# Update cron with full path
* * * * * cd /path/to/project && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

### Issue 3: "Database Connection Refused"

**Solution:**
1. Check `.env` file on production server
2. Ensure database credentials are correct
3. Clear config cache:
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

### Issue 4: Cron Runs But No Backups

**Solution:**
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Test commands manually
php artisan backup:projects --verbose
php artisan backup:database --verbose

# Check cron logs
grep CRON /var/log/syslog
# or for cPanel
tail -f /usr/local/cpanel/logs/cron_log
```

### Issue 5: Wrong Time Zone

**Solution:**
1. Check server timezone:
   ```bash
   date
   timedatectl  # For systemd systems
   ```

2. Update Laravel timezone in `config/app.php`:
   ```php
   'timezone' => 'America/Toronto',  // Or your timezone
   ```

3. Clear cache:
   ```bash
   php artisan config:cache
   ```

## Backup Schedule Summary

Once cron is set up, backups will run automatically:

| Task | Schedule | Time | Days |
|------|----------|------|------|
| **Project Backups** | Every 2 days | 2:00 AM | Sun, Tue, Thu, Sat |
| **Database Backup** | Every 2 days | 2:30 AM | Sun, Tue, Thu, Sat |
| **Cleanup Old Backups** | Automatic | After each backup | 30-day retention |

## Monitoring & Maintenance

### 1. Check Backup Files Regularly

Via SSH:
```bash
ls -lh storage/app/backups/
```

Via cPanel File Manager:
- Navigate to `storage/app/backups/`
- Check file dates and sizes

### 2. Set Up Email Notifications (Optional)

Edit `routes/console.php`:

```php
Schedule::command('backup:projects')
    ->dailyAt('02:00')
    ->days([0, 2, 4, 6])
    ->emailOutputOnFailure('your-email@example.com');

Schedule::command('backup:database')
    ->dailyAt('02:30')
    ->days([0, 2, 4, 6])
    ->emailOutputOnFailure('your-email@example.com');
```

Then configure mail settings in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tracker.codelone.com
MAIL_FROM_NAME="Time Tracker"
```

### 3. Monitor Disk Space

```bash
# Check disk usage
df -h

# Check backup directory size
du -sh storage/app/backups/
```

### 4. Test Restoration

**At least once a month**, test restoring a backup:

```bash
# Download a backup file
scp username@tracker.codelone.com:/path/to/project/storage/app/backups/database-full-*.sql ./

# Test restore on local/test database
mysql -u root -p test_database < database-full-*.sql
```

## Security Best Practices

1. **Protect Backup Directory:**
   ```bash
   # Add to .htaccess in storage/app/backups/
   echo "deny from all" > storage/app/backups/.htaccess
   ```

2. **Download Backups Off-site:**
   ```bash
   # Set up automated downloads (cron on your local machine)
   rsync -avz username@tracker.codelone.com:/path/to/backups/ /local/backup/folder/
   ```

3. **Encrypt Sensitive Backups:**
   ```bash
   # Encrypt backup file
   gpg -c storage/app/backups/database-full-*.sql
   ```

4. **Limit Access:**
   ```bash
   chmod 600 storage/app/backups/*.sql
   ```

## Quick Checklist

- [ ] Cron job added (every minute)
- [ ] Cron verified with `crontab -l`
- [ ] Permissions set on `storage/app/backups/` (775)
- [ ] Tested manual backup: `php artisan backup:database`
- [ ] Checked scheduled tasks: `php artisan schedule:list`
- [ ] Verified backup files exist in `storage/app/backups/`
- [ ] Set up off-site backup storage (optional)
- [ ] Configured email notifications (optional)
- [ ] Tested restoration once (recommended)

## Support & Troubleshooting

If you encounter issues:

1. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Check cron logs:**
   ```bash
   # Ubuntu/Debian
   grep CRON /var/log/syslog

   # CentOS/RHEL
   grep CRON /var/log/cron

   # cPanel
   tail -f /var/log/cron
   ```

3. **Run commands with verbose output:**
   ```bash
   php artisan backup:database --verbose
   php artisan schedule:run --verbose
   ```

4. **Check file permissions:**
   ```bash
   ls -la storage/app/backups/
   ```

## Need Help?

Create an issue with:
- Your hosting type (cPanel, VPS, etc.)
- Error messages from logs
- Output of `php artisan schedule:list`
- Server PHP version: `php -v`

---

**Your Backups Are Now Automated! 🎉**

Backups will be saved to: `/path/to/project/storage/app/backups/`

Access them via:
- SSH/FTP: `storage/app/backups/`
- Also available via the web interface: **Manage Projects → Backup Database** button

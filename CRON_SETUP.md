# Cron Job Setup Guide

## Quick Setup

The application has **2 automated backup tasks** scheduled to run every 2 days:
- **2:00 AM** - Individual project backups
- **2:30 AM** - Full database backup

## Setup Instructions

### For Linux/Unix (Ubuntu, Debian, CentOS, etc.)

1. **Open crontab editor:**
   ```bash
   crontab -e
   ```

2. **Add this line at the end:**
   ```
   * * * * * cd /mnt/d/github/freelancer-time-tracker && php artisan schedule:run >> /dev/null 2>&1
   ```

   **Important:** Replace `/mnt/d/github/freelancer-time-tracker` with your actual project path.

3. **Save and exit** (Press `Ctrl+X`, then `Y`, then `Enter` in nano)

4. **Verify crontab is set:**
   ```bash
   crontab -l
   ```

### For Windows (Task Scheduler)

1. **Open Task Scheduler** (Press `Win+R`, type `taskschd.msc`)

2. **Create Basic Task:**
   - Click "Create Basic Task" in right panel
   - Name: "Laravel Scheduler"
   - Description: "Runs Laravel scheduled tasks"
   - Click "Next"

3. **Set Trigger:**
   - Trigger: "Daily"
   - Start time: Any time (12:00 AM recommended)
   - Recur every: 1 day
   - Click "Next"

4. **Set Action:**
   - Action: "Start a program"
   - Program/script: `C:\php\php.exe` (or your PHP path)
   - Arguments: `artisan schedule:run`
   - Start in: `D:\github\freelancer-time-tracker` (your project path)
   - Click "Next" and "Finish"

5. **Edit Advanced Settings:**
   - Right-click the task → Properties
   - Triggers tab → Edit trigger
   - Check "Repeat task every" → Set to "1 minute"
   - For a duration of: "Indefinitely"
   - Click "OK"

### For macOS

1. **Create a LaunchAgent file:**
   ```bash
   nano ~/Library/LaunchAgents/com.laravel.scheduler.plist
   ```

2. **Add this content:**
   ```xml
   <?xml version="1.0" encoding="UTF-8"?>
   <!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
   <plist version="1.0">
   <dict>
       <key>Label</key>
       <string>com.laravel.scheduler</string>
       <key>ProgramArguments</key>
       <array>
           <string>/usr/local/bin/php</string>
           <string>/path/to/your/project/artisan</string>
           <string>schedule:run</string>
       </array>
       <key>RunAtLoad</key>
       <true/>
       <key>StartInterval</key>
       <integer>60</integer>
   </dict>
   </plist>
   ```

   **Replace** `/path/to/your/project` with your actual project path.

3. **Load the LaunchAgent:**
   ```bash
   launchctl load ~/Library/LaunchAgents/com.laravel.scheduler.plist
   ```

4. **Start it:**
   ```bash
   launchctl start com.laravel.scheduler
   ```

## Verification

### Check Scheduled Tasks
```bash
php artisan schedule:list
```

**Expected output:**
```
0 2 * * 0,2,4,6 ........ backup:projects ....... Next Due: X hours from now
0 2:30 * * 0,2,4,6 ...... backup:database ...... Next Due: X hours from now
```

### Test Scheduler Manually
```bash
php artisan schedule:run
```

### Run Backups Manually
```bash
# Backup all projects
php artisan backup:projects

# Backup entire database
php artisan backup:database
```

### Check Backup Files
```bash
ls -lh storage/app/backups/
```

You should see files like:
- `project-1-2025-10-08_020000-backup.sql`
- `database-full-2025-10-08_023000-backup.sql`

## Troubleshooting

### Cron not running (Linux)

1. **Check if cron service is running:**
   ```bash
   sudo service cron status
   ```

2. **Start cron if stopped:**
   ```bash
   sudo service cron start
   ```

3. **View cron logs:**
   ```bash
   grep CRON /var/log/syslog
   tail -f /var/log/syslog | grep CRON
   ```

### Permission Issues

```bash
# Make sure storage directory is writable
chmod -R 775 storage/app/backups
chown -R www-data:www-data storage/app/backups

# Or use your web server user
chown -R $USER:$USER storage/app/backups
```

### PHP Path Issues

Find your PHP path:
```bash
which php
```

Update crontab with full PHP path:
```
* * * * * cd /path/to/project && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

### Database Connection Issues

Make sure your `.env` file has correct database settings:
```bash
php artisan config:clear
php artisan config:cache
```

## Alternative: Run Scheduler Manually (Development)

For development/testing, you can run the scheduler manually:

```bash
php artisan schedule:work
```

This will run the scheduler every minute in the foreground.

## Backup Schedule Details

**Current Schedule:**
- **Frequency:** Every 2 days (Sunday, Tuesday, Thursday, Saturday)
- **Time:** 2:00 AM and 2:30 AM
- **Retention:** 30 days (auto-cleanup)
- **Location:** `storage/app/backups/`

**To change the schedule:**

Edit `routes/console.php`:

```php
// Daily at 3:00 AM
Schedule::command('backup:database')->dailyAt('03:00');

// Every Monday at 2:00 AM
Schedule::command('backup:database')->weeklyOn(1, '02:00');

// Twice daily (2 AM and 2 PM)
Schedule::command('backup:database')->twiceDaily(2, 14);

// Every hour
Schedule::command('backup:database')->hourly();
```

## Production Tips

1. **Email Notifications:**
   Add to `routes/console.php`:
   ```php
   Schedule::command('backup:database')
       ->dailyAt('02:30')
       ->emailOutputOnFailure('admin@example.com');
   ```

2. **Logging:**
   Check Laravel logs for backup status:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Monitor Disk Space:**
   ```bash
   df -h storage/app/backups/
   ```

4. **Off-site Backups:**
   Consider copying backups to cloud storage (S3, Dropbox, etc.) for redundancy.

5. **Test Restoration:**
   Regularly test restoring from backups to ensure they work.

## Security Notes

- Backup files contain sensitive data
- Ensure `storage/app/backups/` is not publicly accessible
- Consider encrypting backups for production
- Set proper file permissions (750 or 755 for directories, 640 or 644 for files)

## Support

For more details, see: `BACKUP_SETUP.md`

For Laravel scheduling documentation: https://laravel.com/docs/scheduling

# Cron Job Setup Guide

## Quick Setup

Run Laravel's scheduler **every minute** for invoice delivery, email schedules, and automatic backups.

Configure the daily full backup in **Settings → Backup schedule**: enable/disable it, choose a time in `APP_TIMEZONE`, and retain 7–365 days. The default is midnight with 30-day retention. Settings also shows the last successful backup, failures, and the scheduler heartbeat. If the heartbeat is missing or older than three minutes, check the host cron job.

After deploying these changes, run `php artisan migrate --force`, `php artisan optimize:clear`, and `php artisan view:cache`. The migration adds invoice retry fields. Interface assets are local and content-versioned; no frontend rebuild is required for the Blade interface.

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
* * * * * ........ backup:scheduled
* * * * * ........ invoices:send-scheduled
* * * * * ........ scheduler-heartbeat
```

### Test Scheduler Manually
```bash
php artisan schedule:run
```

### Run Backup Manually
```bash
# Backup entire database (includes all projects and time logs)
php artisan backup:database

# Optional: Backup a specific project only
php artisan backup:projects --project-id=1
```

### Check Backup Files
```bash
ls -lh storage/app/private/backups/
```

You should see files like:
- `database-full-2025-10-08_020000-backup.sql`

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
chmod -R 775 storage/app/private/backups
chown -R www-data:www-data storage/app/private/backups

# Or use your web server user
chown -R $USER:$USER storage/app/private/backups
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

- Full backups run once per day at the time saved in Settings, using `APP_TIMEZONE`.
- `backup:scheduled` checks every minute and catches up after a missed run. Failed attempts retry after 15 minutes.
- `backup:database` creates a backup immediately and remains available for manual use.
- Organization SQL exports keep their existing two-day schedule. Full backups already include every organization.
- Files use the private local disk (`storage/app/private/backups` by default). Earlier backups in `storage/app/backups` remain visible and downloadable.
- SQLite backups include a consistent database snapshot and an SQL export. Cleanup runs after successful backups.

## Invoice Delivery and Application Logs

Invoice delivery is checked every minute, without five-minute rounding. The selected time uses `APP_TIMEZONE`, independent of the browser's time zone. Failed deliveries remain scheduled and retry up to five attempts with increasing delays. Settings → Delivery history shows outcomes; the invoice displays failed/retrying state. Save a new delivery schedule after correcting email configuration to restart retries. No success state is stored before the mail transport accepts the message. Re-open and save any existing delivery schedules after deploying the timezone fix; old timestamps may have been stored in the browser/UTC time instead of application time.

Settings → Application logs selects a date, time range, and level. Each request reads at most 256 KB; use “Load older entries” for previous pages. Log channels rotate daily, retaining at least seven days (14 by default, configurable with `LOG_DAILY_DAYS`). Old `laravel.log` remains available as a separately selected legacy source. Historical daily files cannot be recreated for days that were never logged.

## Production Tips

1. **Email Notifications:**
   Add to `routes/console.php`:
   ```php
   Schedule::command('backup:scheduled')
       ->everyMinute()
       ->emailOutputOnFailure('admin@example.com');
   ```

2. **Logging:**
   Check Laravel logs for backup status:
   ```bash
   tail -f storage/logs/laravel-YYYY-MM-DD.log
   ```

3. **Monitor Disk Space:**
   ```bash
   df -h storage/app/private/backups/
   ```

4. **Off-site Backups:**
   Consider copying backups to cloud storage (S3, Dropbox, etc.) for redundancy.

5. **Test Restoration:**
   Regularly test restoring from backups to ensure they work.

## Security Notes

- Backup files contain sensitive data
- Ensure `storage/app/private/backups/` is not publicly accessible
- Consider encrypting backups for production
- Set proper file permissions (750 or 755 for directories, 640 or 644 for files)

## Support

For more details, see: `BACKUP_SETUP.md`

For Laravel scheduling documentation: https://laravel.com/docs/scheduling

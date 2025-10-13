# Automated Backup Setup

## Overview
The application includes an automated backup system with **two types of backups**:
1. **Project Backups** - Individual project backups (per-project SQL files)
2. **Full Database Backup** - Complete database backup (all tables and data)

Both backup types run automatically every 2 days.

## Features
- ✅ **Full database backup** (all tables, schema, and data)
- ✅ **Per-project backups** with complete project data
- ✅ Supports **SQLite**, **MySQL**, and **PostgreSQL**
- ✅ Includes database schema (CREATE TABLE statements)
- ✅ Generates restorable SQL files
- ✅ Automatically cleans up backups older than 30 days
- ✅ Runs every 2 days at 2:00 AM (projects) and 2:30 AM (database)
- ✅ Can backup specific projects manually
- ✅ Multiple fallback methods for different environments

## Backup Location
Backups are stored in: `storage/app/backups/`

### Filename Formats

**Project Backups:**
- Format: `project-{id}-{date}_{time}-backup.sql`
- Example: `project-1-2025-10-08_020000-backup.sql`

**Full Database Backups:**
- SQLite: `database-full-{date}_{time}-backup.sqlite` (copy) + `.sql` (dump)
- MySQL: `database-full-{date}_{time}-backup.sql`
- PostgreSQL: `database-full-{date}_{time}-backup.sql`

## Manual Backup Commands

### Full Database Backup (All Tables)
```bash
php artisan backup:database
```
This backs up **all tables** in the database including projects, time_logs, sessions, migrations, etc.

### Backup All Active Projects
```bash
php artisan backup:projects
```
This backs up each active project individually with its time logs.

### Backup Specific Project
```bash
php artisan backup:projects --project-id=1
```

## Automated Scheduling Setup

Both backup commands are scheduled to run automatically every 2 days:
- **2:00 AM** - Project backups (`backup:projects`)
- **2:30 AM** - Full database backup (`backup:database`)

Runs on: Sunday, Tuesday, Thursday, Saturday

### For Production (Linux/Unix)

1. **Add Laravel Scheduler to Crontab:**
   ```bash
   crontab -e
   ```

2. **Add this line:**
   ```
   * * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
   ```
   Replace `/path/to/your/project` with your actual project path.

3. **Save and exit**
   The scheduler will now run every minute and execute the backup command on the scheduled days.

### For Development (Local Testing)

Run the scheduler manually:
```bash
php artisan schedule:work
```

Or test the scheduled command directly:
```bash
php artisan schedule:run
```

### For Windows (Task Scheduler)

1. Open **Task Scheduler**
2. Create a new **Basic Task**
3. Set it to run daily
4. Action: **Start a program**
5. Program: `php.exe`
6. Arguments: `C:\path\to\project\artisan schedule:run`
7. Start in: `C:\path\to\project`

## Verification

### Check Scheduled Tasks
```bash
php artisan schedule:list
```

### View Recent Backups
```bash
ls -lh storage/app/backups/
```

### Test Backup Immediately
```bash
php artisan backup:projects
```

## Backup File Structure

### Project Backup Files (`project-*.sql`)

Each project backup SQL file contains:

1. **Header** - Project info, generation date, log count
2. **Database Schema** - CREATE TABLE statements for:
   - `projects` table
   - `time_logs` table with foreign keys
3. **Project Data** - INSERT statement for the project
4. **Time Logs Data** - INSERT statements for all time logs
5. **Summary** - Statistics and restoration instructions

### Full Database Backup Files (`database-full-*.sql`)

Each full database backup contains:

1. **Header** - Database name, generation date
2. **Complete Schema** - CREATE TABLE statements for ALL tables:
   - `projects`
   - `time_logs`
   - `sessions`
   - `cache`
   - `jobs`
   - `migrations`
   - And any other tables in your database
3. **All Data** - INSERT statements for every row in every table
4. **Indexes and Keys** - Foreign keys, unique constraints, indexes

**For SQLite**: Both `.sqlite` (binary copy) and `.sql` (text dump) files are created

## Restoration

### Restore Full Database Backup

**SQLite:**
```bash
# Option 1: Replace database file with backup copy
cp storage/app/backups/database-full-2025-10-08_020000-backup.sqlite database/database.sqlite

# Option 2: Import SQL dump
sqlite3 database/database.sqlite < storage/app/backups/database-full-2025-10-08_020000-backup.sql
```

**MySQL:**
```bash
# Method 1: Command line
mysql -u username -p database_name < storage/app/backups/database-full-2025-10-08_020000-backup.sql

# Method 2: phpMyAdmin
# 1. Select your database
# 2. Go to Import tab
# 3. Choose the SQL file
# 4. Click Go

# Method 3: MySQL Workbench
# 1. Open connection
# 2. Server > Data Import
# 3. Select the SQL file
# 4. Start Import
```

**PostgreSQL:**
```bash
psql -U username -d database_name -f storage/app/backups/database-full-2025-10-08_020000-backup.sql
```

### Restore Project Backup

To restore a single project:

```bash
mysql -u username -p database_name < storage/app/backups/project-1-2025-10-08_020000-backup.sql
```

This will restore/update the specific project and its time logs without affecting other data.

## Backup Retention

- Backups are automatically kept for **30 days**
- Older backups are deleted automatically during each backup run
- Project backups and database backups are cleaned up separately
- Manual backups are also subject to the 30-day retention policy

**Storage Usage:**
- Project backups: Small (~10-100 KB per project)
- Full database backups: Varies by database size
  - SQLite: Binary copy + SQL dump
  - MySQL/PostgreSQL: SQL dump only

## Troubleshooting

### Check if scheduler is working
```bash
php artisan schedule:test
```

### View scheduled commands
```bash
php artisan schedule:list
```

### Check backup logs
```bash
tail -f storage/logs/laravel.log
```

### Common Issues

1. **Permission denied on storage/app/backups**
   ```bash
   chmod -R 775 storage/app/backups
   chown -R www-data:www-data storage/app/backups
   ```

2. **Cron not running**
   - Verify crontab entry: `crontab -l`
   - Check cron logs: `grep CRON /var/log/syslog`

3. **Database connection errors**
   - Verify `.env` database settings
   - Test connection: `php artisan db:show`

4. **SQLite backup issues**
   - Ensure database file exists and is readable
   - Check `sqlite3` command availability: `which sqlite3`
   - Manual backup will fallback if sqlite3 is not available

5. **MySQL/PostgreSQL backup issues**
   - Ensure `mysqldump` or `pg_dump` is installed
   - Verify database credentials in `.env`
   - Manual PHP-based backup will be used as fallback

## Security Notes

- Backup files contain sensitive data
- Ensure `storage/app/backups/` is not publicly accessible
- Consider encrypting backups for production
- Regularly test restoration procedures
- Keep backups in multiple locations for redundancy

## Customization

### Change Backup Schedule

Edit `routes/console.php`:

```php
// Every day at 3:00 AM
Schedule::command('backup:projects')->dailyAt('03:00');

// Every Monday at 2:00 AM
Schedule::command('backup:projects')->weeklyOn(1, '02:00');

// Twice daily (2 AM and 2 PM)
Schedule::command('backup:projects')->twiceDaily(2, 14);
```

### Change Retention Period

Edit `app/Console/Commands/BackupProjects.php`, line 265:

```php
$cutoffDate = now()->subDays(60); // Keep for 60 days instead of 30
```

## Additional Features

### Email Notifications (Optional)

Add email notifications on backup failure:

```php
Schedule::command('backup:projects')
    ->dailyAt('02:00')
    ->days([0, 2, 4, 6])
    ->emailOutputOnFailure('admin@example.com');
```

### Slack Notifications (Optional)

```php
Schedule::command('backup:projects')
    ->dailyAt('02:00')
    ->days([0, 2, 4, 6])
    ->pingOnSuccess('https://hooks.slack.com/...')
    ->pingOnFailure('https://hooks.slack.com/...');
```

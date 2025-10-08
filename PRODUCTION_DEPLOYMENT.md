# Production Deployment Guide for tracker.codelone.com

## Issue: CSS Not Loading or Misaligned

If your site at https://tracker.codelone.com/ is not showing CSS properly, follow these steps:

## Quick Fix Steps

### Step 1: Update .env on Production Server

SSH into your server and update the `.env` file:

```bash
# SSH into server
ssh username@tracker.codelone.com

# Navigate to project
cd /path/to/your/project

# Edit .env
nano .env
```

**Update these lines:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tracker.codelone.com
```

**Save:** Press `Ctrl+X`, then `Y`, then `Enter`

### Step 2: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Then cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 3: Check .htaccess in public folder

Make sure `public/.htaccess` exists and contains:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]

    # Force HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

# Disable directory browsing
Options -Indexes

# Allow access to CSS, JS, images
<FilesMatch "\.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$">
    Header set Cache-Control "max-age=31536000, public"
</FilesMatch>
```

### Step 4: Verify File Permissions

```bash
# Set correct permissions
chmod -R 755 public/
chmod -R 755 public/css/
chmod -R 755 public/js/
chmod 644 public/css/timesheet/*.css
chmod 644 public/js/timesheet/*.js

# Set storage permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Step 5: Check Document Root

Your web server should point to the `public` folder:

**For cPanel:**
1. Go to cPanel → Domains → Domain Management
2. Click "Manage" next to tracker.codelone.com
3. Set "Document Root" to: `/home/username/tracker.codelone.com/public`
4. Save

**For Apache Virtual Host:**
```apache
<VirtualHost *:80>
    ServerName tracker.codelone.com
    DocumentRoot /var/www/tracker.codelone.com/public

    <Directory /var/www/tracker.codelone.com/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**For Nginx:**
```nginx
server {
    listen 80;
    server_name tracker.codelone.com;
    root /var/www/tracker.codelone.com/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### Step 6: Test Asset Loading

Visit these URLs in your browser:
- https://tracker.codelone.com/css/timesheet/main.css
- https://tracker.codelone.com/js/timesheet/index.js

**If you get 404 errors:**
- Document root is wrong (not pointing to `public` folder)
- Files are missing
- Permissions are wrong

**If you see the files:**
- Check browser console (F12) for errors
- CSS imports might be failing

## Alternative: Inline All CSS (Quick Fix)

If CSS imports are not working, combine all CSS into one file:

### On your local machine:

```bash
cd public/css/timesheet/

# Combine all CSS files
cat variables.css layout.css navigation.css dashboard.css forms.css history.css modals.css project-selector.css projects.css reports.css tracker.css utilities.css > main-combined.css

# Upload to server
```

### Update the view to use combined CSS:

Edit `resources/views/timesheet/index.blade.php`:

```php
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/timesheet/main-combined.css') }}">
@endpush
```

## Complete Deployment Checklist

- [ ] Update `.env` with production settings
- [ ] Set `APP_URL=https://tracker.codelone.com`
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Clear all caches
- [ ] Set proper file permissions (755 for dirs, 644 for files)
- [ ] Verify document root points to `public` folder
- [ ] Check `.htaccess` exists and is correct
- [ ] Test CSS/JS URLs directly in browser
- [ ] Upload all files to server
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed database if needed: `php artisan db:seed`
- [ ] Test the application
- [ ] Set up SSL certificate (if not already)
- [ ] Set up cron job for backups

## SSL Certificate Setup

If you don't have HTTPS yet:

### Using Let's Encrypt (Free)

**cPanel:**
1. Go to Security → SSL/TLS Status
2. Click "Run AutoSSL" for tracker.codelone.com

**Command Line (Certbot):**
```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d tracker.codelone.com
```

## Testing in Browser

1. **Open browser console** (F12 → Console)
2. **Check for errors:**
   - Look for 404 errors (files not found)
   - Look for CORS errors
   - Look for CSS import errors

3. **Check Network tab:**
   - See which files are loading
   - Check status codes (should be 200)
   - Verify correct paths

## Common Issues & Solutions

### Issue 1: "Mixed Content" Warning
**Cause:** HTTP resources on HTTPS page

**Solution:**
- Update `APP_URL` to use `https://`
- Force HTTPS in `.htaccess`

### Issue 2: CSS Files Load But Styles Don't Apply
**Cause:** CSS imports failing, MIME type issues

**Solution:**
```bash
# Add to .htaccess
AddType text/css .css
AddType application/javascript .js
```

### Issue 3: 500 Internal Server Error
**Cause:** Permission issues, .htaccess issues

**Solution:**
```bash
chmod 644 .htaccess
chmod 755 public/
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Issue 4: Blank White Page
**Cause:** PHP errors, missing files

**Solution:**
```bash
# Check error logs
tail -f storage/logs/laravel.log

# Enable debug temporarily
APP_DEBUG=true in .env
```

### Issue 5: Assets 404 Not Found
**Cause:** Wrong document root

**Solution:**
- Verify document root points to `public` folder
- Check file paths are correct
- Verify files were uploaded

## Manual File Upload Steps

If using FTP/SFTP:

1. **Upload all files EXCEPT:**
   - `.env` (create new one on server)
   - `node_modules/`
   - `vendor/` (run `composer install` on server)
   - `.git/`

2. **On server, run:**
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Contact Support

If issues persist:

1. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Check web server error logs:**
   ```bash
   # Apache
   tail -f /var/log/apache2/error.log

   # Nginx
   tail -f /var/log/nginx/error.log

   # cPanel
   tail -f ~/public_html/error_log
   ```

3. **Provide this info:**
   - Exact error message
   - Browser console errors (F12)
   - Laravel log errors
   - Web server (Apache/Nginx)
   - Hosting type (shared/VPS)

## Need More Help?

Visit in browser: https://tracker.codelone.com/

Expected: See the time tracker interface with proper styling

If not working: Share error messages from browser console (F12)

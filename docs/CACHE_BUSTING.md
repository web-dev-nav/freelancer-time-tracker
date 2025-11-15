# Cache Busting Implementation

## Overview

This application implements intelligent cache busting for JavaScript and CSS files using file modification timestamps. This ensures browsers only re-download assets when they actually change, providing optimal performance and caching behavior.

## How It Works

### The Problem with `time()`

Previously, assets were versioned using:
```php
<script src="{{ asset('js/timesheet/index.js') }}?v={{ time() }}"></script>
```

**Issues:**
- `time()` generates a new timestamp on **every page load**
- Browsers are forced to re-download files even if unchanged
- No caching benefit
- Increased bandwidth and slower page loads

### The Solution: `asset_version()`

The new `asset_version()` helper function uses file modification time:
```php
<script src="{{ asset_version('js/timesheet/index.js') }}"></script>
```

**Benefits:**
- Version only changes when file is **actually modified**
- Browsers cache files until they change
- Reduced bandwidth and faster page loads
- Automatic cache invalidation when deploying updates

## Implementation Details

### 1. Helper Function

Located in: `app/Providers/AppServiceProvider.php`

```php
function asset_version($path)
{
    $publicPath = public_path($path);

    // Use file modification time as version
    if (file_exists($publicPath)) {
        $version = filemtime($publicPath);
        return asset($path) . '?v=' . $version;
    }

    // Fallback to app version
    $version = config('app.version', time());
    return asset($path) . '?v=' . $version;
}
```

### 2. Blade Directive

A Blade directive is also available:
```blade
@asset_version('js/timesheet/index.js')
```

### 3. Updated Templates

All Blade templates now use `asset_version()`:

**resources/views/layouts/app.blade.php:**
```blade
<link rel="stylesheet" href="{{ asset_version('css/app-theme.css') }}">
```

**resources/views/timesheet/index.blade.php:**
```blade
<link rel="stylesheet" href="{{ asset_version('css/timesheet/main.css') }}">
<script type="module" src="{{ asset_version('js/timesheet/index.js') }}"></script>
```

**resources/views/settings/index.blade.php:**
```blade
<link rel="stylesheet" href="{{ asset_version('css/timesheet/main.css') }}">
<script type="module" src="{{ asset_version('js/settings/index.js') }}"></script>
```

## ES6 Module Imports

### Automatic Versioning

The application uses ES6 modules with import statements:

**public/js/timesheet/index.js:**
```javascript
import * as State from './state.js';
import * as Utils from './utils.js';
import * as Dashboard from './dashboard.js';
// ... more imports
```

**Important:** When you version the entry point (`index.js`), all imported modules are automatically versioned by the browser. You don't need to add version parameters to each import.

**How it works:**
1. Browser loads `index.js?v=1731648000`
2. Browser sees `import './state.js'`
3. Browser treats imported modules as part of the same cache group
4. When `index.js` version changes, all imports are re-fetched

## JavaScript Files in Project

### Entry Points (Versioned in Blade)
- `public/js/timesheet/index.js` - Main timesheet application
- `public/js/settings/index.js` - Settings page

### Modules (Auto-versioned via imports)
- `public/js/timesheet/state.js` - State management
- `public/js/timesheet/utils.js` - Utility functions
- `public/js/timesheet/tracker.js` - Time tracking
- `public/js/timesheet/history.js` - History management
- `public/js/timesheet/dashboard.js` - Dashboard stats
- `public/js/timesheet/reports.js` - Report generation
- `public/js/timesheet/projects.js` - Project management
- `public/js/timesheet/invoices.js` - Invoice management
- `public/js/timesheet/backups.js` - Backup functionality
- `public/js/timesheet/settings.js` - Settings modal
- `public/js/timesheet/app.js` - App-level functions

## CSS Files

All CSS files use `asset_version()`:
- `public/css/app-theme.css`
- `public/css/timesheet/main.css`

## Version Behavior

### Development
When you modify a file:
1. Save file (e.g., `dashboard.js`)
2. File modification time updates automatically
3. Next page load gets new version
4. Browser downloads fresh copy
5. Changes are immediately visible

### Production
1. Deploy new files to server
2. File modification times update
3. Users automatically get latest version
4. No manual cache clearing needed

## Deployment Checklist

When deploying:

1. **Upload Files**: Transfer files to server
   ```bash
   git pull origin main
   # or
   scp -r public/js/ user@server:/path/to/public/js/
   ```

2. **Verify Permissions**: Ensure files are readable
   ```bash
   chmod -R 644 public/js/*.js
   chmod -R 644 public/css/*.css
   ```

3. **Clear Laravel Cache** (optional but recommended)
   ```bash
   php artisan config:clear
   php artisan view:clear
   ```

4. **Test**: Verify new versions are loaded
   - Open browser developer tools
   - Check Network tab
   - Verify `?v=` parameter changed
   - Confirm new code is running

## Troubleshooting

### Files Not Updating

**Problem:** Changes not appearing in browser

**Solutions:**
1. **Hard refresh**: Ctrl+Shift+R (Windows) / Cmd+Shift+R (Mac)
2. **Check file timestamp**:
   ```bash
   stat public/js/timesheet/index.js
   ```
3. **Verify helper is loaded**:
   ```bash
   php artisan tinker
   >>> asset_version('js/timesheet/index.js')
   ```

### Old Version Still Loading

**Problem:** Browser still using old version

**Causes:**
- Browser cache override (rare)
- CDN/proxy caching (if using)
- Service worker caching (if implemented)

**Solutions:**
1. **Clear browser cache completely**
2. **Check server file timestamp**:
   ```bash
   ls -la public/js/timesheet/index.js
   ```
3. **Force version bump**:
   ```bash
   touch public/js/timesheet/index.js
   ```

### Permission Errors

**Problem:** `filemtime()` returns false

**Solution:**
```bash
# Fix file permissions
chmod -R 755 public/js
chmod -R 755 public/css

# Fix ownership
chown -R www-data:www-data public/js
chown -R www-data:www-data public/css
```

## Best Practices

### 1. Always Use asset_version()

**DO:**
```blade
<script src="{{ asset_version('js/app.js') }}"></script>
<link href="{{ asset_version('css/styles.css') }}">
```

**DON'T:**
```blade
<script src="{{ asset('js/app.js') }}?v={{ time() }}"></script>
<script src="/js/app.js"></script>
```

### 2. Don't Version Imports

**DO:**
```javascript
// index.js
import * as Utils from './utils.js';  // No version needed
```

**DON'T:**
```javascript
// index.js
import * as Utils from './utils.js?v=123';  // Wrong! Breaks ES6 modules
```

### 3. Version Entry Points Only

Only add versions to files loaded directly in HTML:
- ✅ `index.js` (loaded in Blade template)
- ✅ `settings/index.js` (loaded in Blade template)
- ❌ `state.js` (imported by index.js - auto-versioned)
- ❌ `utils.js` (imported by index.js - auto-versioned)

### 4. Use config('app.version') for Fallback

Add to `.env`:
```env
APP_VERSION=1.0.0
```

Update `config/app.php`:
```php
'version' => env('APP_VERSION', '1.0.0'),
```

This provides a fallback version if file doesn't exist.

## Advanced: Build Pipeline Integration

For production builds with compilation (Webpack, Vite, etc.):

### Option 1: Mix Manifest
If using Laravel Mix:
```blade
<script src="{{ mix('js/app.js') }}"></script>
```

### Option 2: Vite Manifest
If using Vite:
```blade
@vite(['resources/js/app.js'])
```

**Note:** This project currently uses vanilla ES6 modules without a build step, so `asset_version()` is the appropriate solution.

## Summary

- ✅ **Smart caching**: Files cached until modified
- ✅ **Auto-versioning**: ES6 imports automatically versioned
- ✅ **Production-ready**: Works seamlessly in deployment
- ✅ **Developer-friendly**: No manual version management
- ✅ **Performance**: Reduced bandwidth and faster loads

The cache busting system is now fully implemented and ready for production use!

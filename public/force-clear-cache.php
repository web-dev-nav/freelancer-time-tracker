<?php
/**
 * FORCE Cache Clear - Directly deletes cache files
 * Upload to public/force-clear-cache.php
 * DELETE THIS FILE after using it!
 */

echo "<h1>FORCE Cache Clearing Tool</h1>";
echo "<pre>";

$basePath = dirname(__DIR__);

// Files to delete
$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes.php',
    'bootstrap/cache/services.php',
    'bootstrap/cache/packages.php',
    'bootstrap/cache/events.php',
];

echo "=== Deleting Cache Files ===\n";
foreach ($cacheFiles as $file) {
    $path = $basePath . '/' . $file;
    if (file_exists($path)) {
        if (unlink($path)) {
            echo "✓ Deleted: $file\n";
        } else {
            echo "✗ Failed to delete: $file\n";
        }
    } else {
        echo "- Not found: $file (OK)\n";
    }
}

// Clear storage cache files
echo "\n=== Clearing Storage Cache ===\n";
$storageCachePath = $basePath . '/storage/framework/cache';
if (is_dir($storageCachePath)) {
    $files = glob($storageCachePath . '/data/*');
    $deleted = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $deleted++;
        }
    }
    echo "✓ Deleted $deleted cache files from storage\n";
}

// Clear view cache
echo "\n=== Clearing View Cache ===\n";
$viewCachePath = $basePath . '/storage/framework/views';
if (is_dir($viewCachePath)) {
    $files = glob($viewCachePath . '/*');
    $deleted = 0;
    foreach ($files as $file) {
        if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            unlink($file);
            $deleted++;
        }
    }
    echo "✓ Deleted $deleted view cache files\n";
}

// Verify .env file
echo "\n=== Verifying .env ===\n";
$envPath = $basePath . '/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    if (preg_match('/APP_TIMEZONE=(.+)/', $envContent, $matches)) {
        $timezone = trim($matches[1]);
        echo "APP_TIMEZONE in .env: $timezone\n";
        if ($timezone !== 'UTC') {
            echo "✗ WARNING: .env still has APP_TIMEZONE=$timezone\n";
            echo "  It should be: APP_TIMEZONE=UTC\n";
        } else {
            echo "✓ .env is correct!\n";
        }
    }
}

// Check config/app.php default
echo "\n=== Checking config/app.php ===\n";
$configPath = $basePath . '/config/app.php';
if (file_exists($configPath)) {
    $configContent = file_get_contents($configPath);
    if (preg_match("/'timezone'\s*=>\s*env\('APP_TIMEZONE',\s*'([^']+)'\)/", $configContent, $matches)) {
        $defaultTz = $matches[1];
        echo "Default fallback timezone: $defaultTz\n";
        if ($defaultTz !== 'UTC') {
            echo "⚠ The config file has a non-UTC default: $defaultTz\n";
            echo "  This means if APP_TIMEZONE is not in .env, it uses: $defaultTz\n";
        }
    }
}

echo "\n=== DONE! ===\n";
echo "All cache files have been forcefully deleted.\n\n";

echo "IMPORTANT NEXT STEPS:\n";
echo "1. DELETE this file (force-clear-cache.php) NOW for security!\n";
echo "2. Restart your web server (Apache/Nginx/PHP-FPM)\n";
echo "3. Visit diagnostic.php again to verify App Timezone shows UTC\n";
echo "4. Hard refresh browser (Ctrl+Shift+F5)\n";
echo "5. Test creating a time entry\n";

echo "</pre>";
?>

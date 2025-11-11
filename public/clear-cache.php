<?php
/**
 * Cache Clearing Tool
 * Upload to public/clear-cache.php and access via browser
 * DELETE THIS FILE after using it for security!
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h1>Cache Clearing Tool</h1>";
echo "<pre>";

// Clear config cache
echo "Clearing config cache...\n";
Artisan::call('config:clear');
echo Artisan::output();

// Clear application cache
echo "\nClearing application cache...\n";
Artisan::call('cache:clear');
echo Artisan::output();

// Clear view cache
echo "\nClearing view cache...\n";
Artisan::call('view:clear');
echo Artisan::output();

// Cache the config again with new values
echo "\nCaching config with new values...\n";
Artisan::call('config:cache');
echo Artisan::output();

echo "\n=== DONE! ===\n";
echo "All caches cleared and config re-cached.\n\n";

// Verify the change
echo "Verifying APP_TIMEZONE...\n";
echo "Current value: " . config('app.timezone') . "\n";

if (config('app.timezone') === 'UTC') {
    echo "✓ SUCCESS! APP_TIMEZONE is now UTC\n";
    echo "\nNext steps:\n";
    echo "1. DELETE this file (clear-cache.php) for security\n";
    echo "2. Hard refresh your browser (Ctrl+Shift+F5)\n";
    echo "3. Test creating a time entry\n";
} else {
    echo "✗ Still showing: " . config('app.timezone') . "\n";
    echo "You may need to restart your web server.\n";
}

echo "</pre>";
?>

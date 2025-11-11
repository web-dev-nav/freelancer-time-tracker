<?php
/**
 * Timezone Diagnostic Tool
 * Upload this to public/diagnostic.php on your server
 * Access via: yourdomain.com/diagnostic.php
 */

echo "<h1>Timezone Configuration Diagnostic</h1>";
echo "<pre>";

echo "=== 1. PHP Settings ===\n";
echo "PHP Timezone: " . date_default_timezone_get() . "\n";
echo "Current PHP Time: " . date('Y-m-d H:i:s T') . "\n\n";

echo "=== 2. Laravel Config (from .env) ===\n";
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    preg_match('/APP_TIMEZONE=(.+)/', $envContent, $matches);
    echo "APP_TIMEZONE: " . ($matches[1] ?? 'NOT SET') . "\n";

    preg_match('/DB_CONNECTION=(.+)/', $envContent, $dbMatches);
    echo "DB_CONNECTION: " . ($dbMatches[1] ?? 'NOT SET') . "\n";
} else {
    echo ".env file not found!\n";
}
echo "\n";

echo "=== 3. Laravel Application Config ===\n";
require_once dirname(__DIR__) . '/vendor/autoload.php';
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "App Timezone: " . config('app.timezone') . "\n";
echo "DB Connection: " . config('database.default') . "\n";
echo "DB Timezone: " . (config('database.connections.mysql.timezone') ?? 'NOT SET') . "\n\n";

echo "=== 4. Carbon Test ===\n";
use Carbon\Carbon;
$testTime = Carbon::createFromFormat('Y-m-d H:i', '2025-11-11 12:50', 'America/Toronto')->setTimezone('UTC');
echo "Input: 2025-11-11 12:50 Toronto\n";
echo "Converted to UTC: " . $testTime->toDateTimeString() . "\n";
echo "Expected: 2025-11-11 17:50:00 UTC\n";
echo "Match: " . ($testTime->toDateTimeString() === '2025-11-11 17:50:00' ? 'YES ✓' : 'NO ✗') . "\n\n";

echo "=== 5. File Modification Times ===\n";
$files = [
    'config/database.php',
    'public/js/timesheet/history.js',
    '.env'
];
foreach ($files as $file) {
    $path = dirname(__DIR__) . '/' . $file;
    if (file_exists($path)) {
        echo "$file: " . date('Y-m-d H:i:s', filemtime($path)) . "\n";
    } else {
        echo "$file: NOT FOUND\n";
    }
}
echo "\n";

echo "=== 6. Database Config Check ===\n";
$dbConfig = config('database.connections.mysql');
echo "Host: " . $dbConfig['host'] . "\n";
echo "Database: " . $dbConfig['database'] . "\n";
echo "Timezone setting: " . ($dbConfig['timezone'] ?? 'NOT SET') . "\n\n";

echo "=== RECOMMENDATIONS ===\n";
$issues = [];

if (config('app.timezone') !== 'UTC') {
    $issues[] = "❌ APP_TIMEZONE should be 'UTC', currently: " . config('app.timezone');
}

if (!isset($dbConfig['timezone']) || $dbConfig['timezone'] !== '+00:00') {
    $issues[] = "❌ Database timezone should be '+00:00', currently: " . ($dbConfig['timezone'] ?? 'NOT SET');
}

if (empty($issues)) {
    echo "✓ All settings are correct!\n";
    echo "If you're still having issues:\n";
    echo "1. Clear cache: php artisan config:clear\n";
    echo "2. Restart web server\n";
    echo "3. Hard refresh browser (Ctrl+Shift+F5)\n";
} else {
    echo "Issues found:\n";
    foreach ($issues as $issue) {
        echo $issue . "\n";
    }
    echo "\nFix these issues and run: php artisan config:clear\n";
}

echo "</pre>";
?>

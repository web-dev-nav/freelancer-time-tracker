<?php
/**
 * Comprehensive Hostinger Diagnostic
 * This will identify the EXACT root cause
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Hostinger Comprehensive Diagnostic</h1>";
echo "<style>body{font-family:monospace;background:#1e1e1e;color:#d4d4d4;padding:20px;}h1,h2{color:#4ec9b0;}pre{background:#252526;padding:15px;border-left:3px solid #007acc;}.error{color:#f48771;}.success{color:#4ec9b0;}.warning{color:#dcdcaa;}</style>";
echo "<pre>";

$basePath = dirname(__DIR__);

echo "=== 1. SERVER ENVIRONMENT ===\n";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n";
echo "System Timezone: " . date_default_timezone_get() . "\n";
echo "Current Server Time: " . date('Y-m-d H:i:s T') . "\n";
echo "\n";

echo "=== 2. PHP TIMEZONE SETTINGS ===\n";
echo "date.timezone (php.ini): " . (ini_get('date.timezone') ?: 'Not Set') . "\n";
echo "PHP Default Timezone: " . date_default_timezone_get() . "\n";
echo "\n";

echo "=== 3. MYSQL/DATABASE CHECK ===\n";
try {
    require_once $basePath . '/vendor/autoload.php';
    $app = require_once $basePath . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $pdo = DB::connection()->getPdo();
    echo "<span class='success'>✓ Database connection successful</span>\n";

    // Check MySQL timezone
    $stmt = $pdo->query("SELECT @@session.time_zone as session_tz, @@global.time_zone as global_tz, NOW() as mysql_now");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "MySQL Session Timezone: " . $result['session_tz'] . "\n";
    echo "MySQL Global Timezone: " . $result['global_tz'] . "\n";
    echo "MySQL Current Time: " . $result['mysql_now'] . "\n";

} catch (Exception $e) {
    echo "<span class='error'>✗ Database Error: " . $e->getMessage() . "</span>\n";
}
echo "\n";

echo "=== 4. LARAVEL CONFIGURATION ===\n";
// Check .env file
$envPath = $basePath . '/.env';
$envTimezone = 'NOT FOUND';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    if (preg_match('/APP_TIMEZONE=(.+)/', $envContent, $matches)) {
        $envTimezone = trim($matches[1]);
    }
}
echo ".env APP_TIMEZONE: " . $envTimezone . "\n";

// Check cached config
echo "Cached config('app.timezone'): " . config('app.timezone') . "\n";
echo "Cached config('database.connections.mysql.timezone'): " . (config('database.connections.mysql.timezone') ?: 'NOT SET') . "\n";

// Check if they match
if ($envTimezone !== config('app.timezone')) {
    echo "<span class='error'>✗ MISMATCH! .env and cached config don't match!</span>\n";
} else {
    echo "<span class='success'>✓ .env and config match</span>\n";
}
echo "\n";

echo "=== 5. CACHE FILES CHECK ===\n";
$cacheFiles = [
    'bootstrap/cache/config.php' => 'Config Cache',
    'bootstrap/cache/routes.php' => 'Routes Cache',
    'bootstrap/cache/services.php' => 'Services Cache',
];

foreach ($cacheFiles as $file => $name) {
    $path = $basePath . '/' . $file;
    if (file_exists($path)) {
        $mtime = filemtime($path);
        echo "<span class='warning'>⚠ $name EXISTS</span> (Modified: " . date('Y-m-d H:i:s', $mtime) . ")\n";
    } else {
        echo "<span class='success'>✓ $name: Not cached</span>\n";
    }
}
echo "\n";

echo "=== 6. OPCACHE STATUS ===\n";
if (function_exists('opcache_get_status')) {
    $opcache = opcache_get_status(false);
    if ($opcache !== false && $opcache['opcache_enabled']) {
        echo "<span class='warning'>⚠ OPcache is ENABLED</span>\n";
        echo "Cached Scripts: " . $opcache['opcache_statistics']['num_cached_scripts'] . "\n";
        echo "Cache Full: " . ($opcache['cache_full'] ? 'YES' : 'NO') . "\n";
        echo "Last Restart: " . date('Y-m-d H:i:s', $opcache['opcache_statistics']['start_time']) . "\n";
    } else {
        echo "OPcache is disabled\n";
    }
} else {
    echo "OPcache not available\n";
}
echo "\n";

echo "=== 7. ACTUAL DATABASE TEST ===\n";
echo "Testing actual time storage and retrieval...\n\n";

use Carbon\Carbon;
use App\Models\TimeLog;

// Test 1: Create with Toronto time
$testDate = date('Y-m-d');
$testClockIn = '12:50';
$testClockOut = '17:30';

echo "Input: $testDate $testClockIn to $testClockOut (Toronto time)\n";

// Simulate what the controller does
$clockInCarbon = Carbon::createFromFormat('Y-m-d H:i', $testDate . ' ' . $testClockIn, 'America/Toronto')
                       ->setTimezone('UTC');
$clockOutCarbon = Carbon::createFromFormat('Y-m-d H:i', $testDate . ' ' . $testClockOut, 'America/Toronto')
                        ->setTimezone('UTC');

echo "After Carbon conversion:\n";
echo "  Clock In UTC: " . $clockInCarbon->toDateTimeString() . "\n";
echo "  Clock Out UTC: " . $clockOutCarbon->toDateTimeString() . "\n";

// Try to insert
try {
    $testLog = TimeLog::create([
        'session_id' => 'diagnostic-test-' . time(),
        'clock_in' => $clockInCarbon,
        'clock_out' => $clockOutCarbon,
        'total_minutes' => 280,
        'work_description' => 'Diagnostic test entry',
        'status' => 'completed'
    ]);

    echo "<span class='success'>✓ Test entry created (ID: {$testLog->id})</span>\n";

    // Retrieve it fresh
    $fresh = TimeLog::find($testLog->id);
    echo "\nRetrieved from database:\n";
    echo "  Raw clock_in from DB: " . $fresh->getAttributes()['clock_in'] . "\n";
    echo "  Raw clock_out from DB: " . $fresh->getAttributes()['clock_out'] . "\n";
    echo "  Cast clock_in (Carbon): " . $fresh->clock_in->toDateTimeString() . " " . $fresh->clock_in->timezoneName . "\n";
    echo "  Cast clock_out (Carbon): " . $fresh->clock_out->toDateTimeString() . " " . $fresh->clock_out->timezoneName . "\n";

    // Check what API would return
    echo "\nWhat API returns (with accessors):\n";
    echo "  clock_in_time accessor: " . $fresh->clock_in_time . "\n";
    echo "  clock_out_time accessor: " . $fresh->clock_out_time . "\n";

    // Calculate expected
    $expectedClockIn = Carbon::createFromFormat('Y-m-d H:i', $testDate . ' ' . $testClockIn, 'America/Toronto');
    $offset = $expectedClockIn->offsetHours;
    echo "\nExpected behavior:\n";
    echo "  Toronto offset: UTC{$offset}\n";
    echo "  Expected UTC time: " . $expectedClockIn->copy()->setTimezone('UTC')->format('H:i') . "\n";
    echo "  Expected display (Toronto): $testClockIn\n";

    // Compare
    if ($fresh->clock_in_time === $testClockIn) {
        echo "<span class='success'>✓✓✓ PERFECT! Times match!</span>\n";
    } else {
        echo "<span class='error'>✗✗✗ WRONG! Expected $testClockIn but got {$fresh->clock_in_time}</span>\n";
        $diff = Carbon::parse($testClockIn)->diffInHours(Carbon::parse($fresh->clock_in_time), false);
        echo "<span class='error'>Difference: {$diff} hours off</span>\n";
    }

    // Clean up
    $testLog->delete();
    echo "\n<span class='success'>✓ Test entry deleted</span>\n";

} catch (Exception $e) {
    echo "<span class='error'>✗ Database test failed: " . $e->getMessage() . "</span>\n";
}

echo "\n";
echo "=== 8. ROOT CAUSE ANALYSIS ===\n";

$issues = [];
$fixes = [];

// Check 1: Config cache mismatch
if ($envTimezone !== config('app.timezone')) {
    $issues[] = "Config cache doesn't match .env file";
    $fixes[] = "Run: php artisan config:clear (via SSH or create clear-cache script)";
}

// Check 2: MySQL timezone
if (isset($result)) {
    if ($result['session_tz'] === 'SYSTEM' || $result['global_tz'] === 'SYSTEM') {
        $issues[] = "MySQL is using SYSTEM timezone (not UTC)";
        $fixes[] = "Add 'timezone' => '+00:00' to config/database.php mysql connection";
    }
}

// Check 3: OPcache
if (isset($opcache) && $opcache !== false && $opcache['opcache_enabled']) {
    $issues[] = "OPcache is enabled and may be caching old config";
    $fixes[] = "Contact Hostinger support to restart PHP-FPM or disable OPcache temporarily";
}

// Check 4: Config cache exists
if (file_exists($basePath . '/bootstrap/cache/config.php')) {
    $issues[] = "Config cache file exists with old values";
    $fixes[] = "Delete bootstrap/cache/config.php file manually via FTP";
}

if (empty($issues)) {
    echo "<span class='success'>✓ No configuration issues found!</span>\n";
    echo "The application should work correctly.\n";
} else {
    echo "<span class='error'>ISSUES FOUND:</span>\n";
    foreach ($issues as $i => $issue) {
        echo ($i + 1) . ". <span class='error'>$issue</span>\n";
    }
    echo "\n<span class='warning'>REQUIRED FIXES:</span>\n";
    foreach ($fixes as $i => $fix) {
        echo ($i + 1) . ". $fix\n";
    }
}

echo "\n";
echo "=== 9. RECOMMENDED ACTION ===\n";
echo "Based on the above analysis:\n\n";

if (!empty($issues)) {
    echo "1. Apply the fixes listed above\n";
    echo "2. If config cache won't clear, manually delete via FTP:\n";
    echo "   - bootstrap/cache/config.php\n";
    echo "   - bootstrap/cache/services.php\n";
    echo "3. Contact Hostinger support if OPcache won't reset\n";
    echo "4. After fixes, refresh this page to verify\n";
} else {
    echo "All settings look correct!\n";
    echo "If still having issues, the problem may be in JavaScript.\n";
    echo "Check browser console for errors.\n";
}

echo "\n";
echo "</pre>";
echo "<p><strong>DELETE THIS FILE after reading results for security!</strong></p>";
?>

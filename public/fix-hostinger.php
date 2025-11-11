<?php
/**
 * HOSTINGER FIX SCRIPT
 * This will fix ALL identified issues
 * DELETE THIS FILE after running!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Hostinger Fix Script</h1>";
echo "<style>body{font-family:monospace;background:#1e1e1e;color:#d4d4d4;padding:20px;}h1,h2{color:#4ec9b0;}pre{background:#252526;padding:15px;border-left:3px solid #007acc;}.error{color:#f48771;}.success{color:#4ec9b0;}.warning{color:#dcdcaa;}</style>";
echo "<pre>";

$basePath = dirname(__DIR__);

echo "=== FIX 1: Delete Cache Files ===\n";
$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/services.php',
    'bootstrap/cache/packages.php',
    'bootstrap/cache/events.php',
    'bootstrap/cache/routes.php',
];

foreach ($cacheFiles as $file) {
    $path = $basePath . '/' . $file;
    if (file_exists($path)) {
        if (unlink($path)) {
            echo "<span class='success'>✓ Deleted: $file</span>\n";
        } else {
            echo "<span class='error'>✗ Failed to delete: $file</span>\n";
        }
    } else {
        echo "- Not found: $file (OK)\n";
    }
}

echo "\n=== FIX 2: Clear Storage Cache ===\n";
$storagePaths = [
    'storage/framework/cache/data',
    'storage/framework/views',
];

foreach ($storagePaths as $dir) {
    $path = $basePath . '/' . $dir;
    if (is_dir($path)) {
        $files = glob($path . '/*');
        $deleted = 0;
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== '.gitignore') {
                @unlink($file);
                $deleted++;
            }
        }
        echo "<span class='success'>✓ Cleared $deleted files from $dir</span>\n";
    }
}

echo "\n=== FIX 3: Verify .env File ===\n";
$envPath = $basePath . '/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    if (preg_match('/APP_TIMEZONE=(.+)/', $envContent, $matches)) {
        $currentTz = trim($matches[1]);
        echo "Current .env APP_TIMEZONE: $currentTz\n";

        if ($currentTz !== 'America/Toronto') {
            echo "<span class='warning'>⚠ Changing to America/Toronto...</span>\n";
            $envContent = preg_replace('/APP_TIMEZONE=.+/', 'APP_TIMEZONE=America/Toronto', $envContent);
            if (file_put_contents($envPath, $envContent)) {
                echo "<span class='success'>✓ Updated .env file</span>\n";
            } else {
                echo "<span class='error'>✗ Failed to update .env (do it manually via FTP)</span>\n";
            }
        } else {
            echo "<span class='success'>✓ .env already has correct value</span>\n";
        }
    }
}

echo "\n=== FIX 4: Update database.php Config ===\n";
$dbConfigPath = $basePath . '/config/database.php';
if (file_exists($dbConfigPath)) {
    $dbConfig = file_get_contents($dbConfigPath);

    // Check if timezone is already set
    if (strpos($dbConfig, "'timezone' => '+00:00'") !== false) {
        echo "<span class='success'>✓ database.php already has timezone setting</span>\n";
    } else {
        echo "<span class='warning'>⚠ Adding timezone to MySQL config...</span>\n";

        // Add timezone after 'engine' => null,
        $pattern = "/'engine' => null,/";
        $replacement = "'engine' => null,\n            'timezone' => '+00:00',";

        if (preg_match($pattern, $dbConfig)) {
            $dbConfig = preg_replace($pattern, $replacement, $dbConfig, 1);
            if (file_put_contents($dbConfigPath, $dbConfig)) {
                echo "<span class='success'>✓ Updated database.php</span>\n";
            } else {
                echo "<span class='error'>✗ Failed to update database.php</span>\n";
                echo "Manually add this line to config/database.php in the mysql connection:\n";
                echo "'timezone' => '+00:00',\n";
            }
        } else {
            echo "<span class='error'>✗ Could not find insertion point in database.php</span>\n";
            echo "Manually add 'timezone' => '+00:00', to the mysql connection array\n";
        }
    }
}

echo "\n=== FIX 5: Reset OPcache ===\n";
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "<span class='success'>✓ OPcache reset successful</span>\n";
    } else {
        echo "<span class='warning'>⚠ OPcache reset failed</span>\n";
        echo "You may need to contact Hostinger support to restart PHP-FPM\n";
    }
} else {
    echo "OPcache reset not available\n";
}

echo "\n=== FIX 6: Rebuild Config Cache ===\n";
require_once $basePath . '/vendor/autoload.php';

// Set correct environment
putenv('APP_TIMEZONE=America/Toronto');
$_ENV['APP_TIMEZONE'] = 'America/Toronto';
$_SERVER['APP_TIMEZONE'] = 'America/Toronto';

$app = require_once $basePath . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Clearing config...\n";
Artisan::call('config:clear');
echo trim(Artisan::output()) . "\n";

echo "Clearing cache...\n";
Artisan::call('cache:clear');
echo trim(Artisan::output()) . "\n";

echo "Clearing view cache...\n";
Artisan::call('view:clear');
echo trim(Artisan::output()) . "\n";

echo "\nRe-caching config...\n";
Artisan::call('config:cache');
echo trim(Artisan::output()) . "\n";

echo "\n=== VERIFICATION ===\n";
echo "Config app.timezone: " . config('app.timezone') . "\n";
echo "Config database timezone: " . (config('database.connections.mysql.timezone') ?? 'NOT SET') . "\n";

if (config('app.timezone') === 'America/Toronto') {
    echo "<span class='success'>✓✓✓ Config is now correct!</span>\n";
} else {
    echo "<span class='error'>✗ Config still wrong: " . config('app.timezone') . "</span>\n";
}

echo "\n=== DATABASE TEST ===\n";
use Carbon\Carbon;
use App\Models\TimeLog;

$testDate = date('Y-m-d');
$testClockIn = '12:50';

$clockInCarbon = Carbon::createFromFormat('Y-m-d H:i', $testDate . ' ' . $testClockIn, 'America/Toronto')
                       ->setTimezone('UTC');

try {
    $testLog = TimeLog::create([
        'session_id' => 'fix-test-' . time(),
        'clock_in' => $clockInCarbon,
        'clock_out' => $clockInCarbon->copy()->addMinutes(30),
        'total_minutes' => 30,
        'work_description' => 'Fix verification test',
        'status' => 'completed'
    ]);

    $fresh = TimeLog::find($testLog->id);

    echo "Input: $testClockIn (Toronto)\n";
    echo "Saved as UTC: " . $fresh->getAttributes()['clock_in'] . "\n";
    echo "Retrieved accessor: " . $fresh->clock_in_time . "\n";

    if ($fresh->clock_in_time === $testClockIn) {
        echo "<span class='success'>✓✓✓ PERFECT! Times now save and display correctly!</span>\n";
    } else {
        echo "<span class='error'>✗ Still wrong. Expected $testClockIn, got " . $fresh->clock_in_time . "</span>\n";
    }

    $testLog->delete();
} catch (Exception $e) {
    echo "<span class='error'>Test failed: " . $e->getMessage() . "</span>\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. <strong>DELETE THIS FILE (fix-hostinger.php) NOW!</strong>\n";
echo "2. Also delete: hostinger-diagnostic.php\n";
echo "3. Hard refresh your browser (Ctrl+Shift+F5)\n";
echo "4. Test creating a time entry with 12:50 to 17:30\n";
echo "5. It should now save correctly!\n";

echo "\n<span class='success'>All fixes have been applied!</span>\n";
echo "</pre>";
?>

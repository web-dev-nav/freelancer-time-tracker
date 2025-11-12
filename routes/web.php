<?php

// routes/web.php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TimeLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Debug route
Route::get('/debug-api', function() {
    return response()->json(['status' => 'Laravel API working', 'timestamp' => now()]);
});

// Cron job trigger route (for Hostinger and URL-based cron jobs)
Route::get('/cron/run/{token}', function($token) {
    // Simple security: check token matches APP_KEY hash
    $expectedToken = substr(md5(config('app.key')), 0, 16);

    if ($token !== $expectedToken) {
        abort(403, 'Unauthorized');
    }

    try {
        // Run Laravel scheduler
        \Illuminate\Support\Facades\Artisan::call('schedule:run');
        $output = \Illuminate\Support\Facades\Artisan::output();

        return response()->json([
            'status' => 'success',
            'message' => 'Scheduler executed successfully',
            'output' => $output,
            'timestamp' => now()->toDateTimeString()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'timestamp' => now()->toDateTimeString()
        ], 500);
    }
})->name('cron.run');

// Manual invoice reminder trigger (for testing)
Route::get('/cron/test-reminders/{token}', function($token) {
    $expectedToken = substr(md5(config('app.key')), 0, 16);

    if ($token !== $expectedToken) {
        abort(403, 'Unauthorized');
    }

    try {
        \Illuminate\Support\Facades\Artisan::call('invoices:send-reminders');
        $output = \Illuminate\Support\Facades\Artisan::output();

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice reminders command executed',
            'output' => $output,
            'timestamp' => now()->toDateTimeString()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'timestamp' => now()->toDateTimeString()
        ], 500);
    }
})->name('cron.test-reminders');

// Main timesheet interface
Route::get('/', [TimeLogController::class, 'index'])->name('timesheet.index');
Route::get('/timesheet', [TimeLogController::class, 'index'])->name('timesheet.dashboard');

// Settings page
Route::get('/settings', function() {
    return view('settings.index');
})->name('settings.index');

Route::get('/invoices/{invoice}/open/{token}.png', [InvoiceController::class, 'trackOpen'])
    ->name('invoices.track-open');

// Fallback route
Route::fallback(function () {
    return redirect()->route('timesheet.index');
});

<?php

// routes/web.php

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

// Main timesheet interface
Route::get('/', [TimeLogController::class, 'index'])->name('timesheet.index');
Route::get('/timesheet', [TimeLogController::class, 'index'])->name('timesheet.dashboard');


// Fallback route
Route::fallback(function () {
    return redirect()->route('timesheet.index');
});
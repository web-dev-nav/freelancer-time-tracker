<?php

use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Project management API routes
Route::prefix('projects')->name('projects.api.')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/active', [ProjectController::class, 'active'])->name('active');
    Route::get('/{id}', [ProjectController::class, 'show'])->name('show');
    Route::post('/', [ProjectController::class, 'store'])->name('store');
    Route::put('/{id}', [ProjectController::class, 'update'])->name('update');
    Route::post('/{id}/archive', [ProjectController::class, 'archive'])->name('archive');
    Route::post('/{id}/activate', [ProjectController::class, 'activate'])->name('activate');
    Route::delete('/{id}', [ProjectController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/stats', [ProjectController::class, 'stats'])->name('stats');
});

// Time tracking API routes
Route::prefix('timesheet')->name('timesheet.api.')->group(function () {

    // Session management
    Route::post('/clock-in', [TimeLogController::class, 'clockIn'])->name('clock-in');
    Route::post('/clock-out', [TimeLogController::class, 'clockOut'])->name('clock-out');
    Route::delete('/cancel-session', [TimeLogController::class, 'cancelSession'])->name('cancel-session');
    Route::get('/active-session', [TimeLogController::class, 'getActiveSession'])->name('active-session');

    // History and CRUD
    Route::get('/history', [TimeLogController::class, 'getHistory'])->name('history');
    Route::get('/logs/{id}', [TimeLogController::class, 'getLog'])->name('get-log');
    Route::put('/logs/{id}', [TimeLogController::class, 'updateLog'])->name('update-log');
    Route::delete('/logs/{id}', [TimeLogController::class, 'deleteLog'])->name('delete-log');

    // Reports and analytics
    Route::get('/report', [TimeLogController::class, 'generateReport'])->name('report');
    Route::get('/dashboard-stats', [TimeLogController::class, 'getDashboardStats'])->name('dashboard-stats');
    Route::get('/export-excel', [TimeLogController::class, 'exportExcel'])->name('export-excel');
});
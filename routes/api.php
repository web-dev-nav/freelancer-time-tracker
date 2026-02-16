<?php

use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Project management API routes
    Route::prefix('projects')->name('projects.api.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/active', [ProjectController::class, 'active'])->name('active');
        Route::get('/client-account', [ProjectController::class, 'clientAccount'])->middleware('author')->name('client-account');
        Route::get('/client-profiles', [ProjectController::class, 'clientProfiles'])->middleware('author')->name('client-profiles');
        Route::get('/{id}', [ProjectController::class, 'show'])->name('show');
        Route::get('/{id}/stats', [ProjectController::class, 'stats'])->name('stats');

        Route::post('/', [ProjectController::class, 'store'])->middleware('author')->name('store');
        Route::put('/{id}', [ProjectController::class, 'update'])->middleware('author')->name('update');
        Route::post('/{id}/archive', [ProjectController::class, 'archive'])->middleware('author')->name('archive');
        Route::post('/{id}/activate', [ProjectController::class, 'activate'])->middleware('author')->name('activate');
        Route::delete('/{id}', [ProjectController::class, 'destroy'])->middleware('author')->name('destroy');
        Route::get('/{id}/backup', [ProjectController::class, 'backup'])->middleware('author')->name('backup');
    });

    // Database backup routes (author only)
    Route::prefix('backups')->name('backups.')->middleware('author')->group(function () {
        Route::get('/', [DatabaseBackupController::class, 'index'])->name('index');
        Route::get('/create', [DatabaseBackupController::class, 'download'])->name('create');
        Route::get('/{filename}', [DatabaseBackupController::class, 'downloadFile'])->name('download');
        Route::delete('/{filename}', [DatabaseBackupController::class, 'deleteFile'])->name('delete');
    });

    // Legacy database backup route (for compatibility)
    Route::get('/database/backup', [DatabaseBackupController::class, 'download'])
        ->middleware('author')
        ->name('database.backup');

    // Time tracking API routes
    Route::prefix('timesheet')->name('timesheet.api.')->group(function () {

        // Session management (author only)
        Route::post('/clock-in', [TimeLogController::class, 'clockIn'])->middleware('author')->name('clock-in');
        Route::post('/clock-out', [TimeLogController::class, 'clockOut'])->middleware('author')->name('clock-out');
        Route::delete('/cancel-session', [TimeLogController::class, 'cancelSession'])->middleware('author')->name('cancel-session');
        Route::get('/active-session', [TimeLogController::class, 'getActiveSession'])->name('active-session');

        // History and CRUD
        Route::get('/history', [TimeLogController::class, 'getHistory'])->name('history');
        Route::post('/logs', [TimeLogController::class, 'createLog'])->middleware('author')->name('create-log');
        Route::get('/logs/{id}', [TimeLogController::class, 'getLog'])->name('get-log');
        Route::put('/logs/{id}', [TimeLogController::class, 'updateLog'])->middleware('author')->name('update-log');
        Route::delete('/logs/{id}', [TimeLogController::class, 'deleteLog'])->middleware('author')->name('delete-log');

        // Reports and analytics
        Route::get('/report', [TimeLogController::class, 'generateReport'])->name('report');
        Route::get('/dashboard-stats', [TimeLogController::class, 'getDashboardStats'])->name('dashboard-stats');
        Route::get('/export-excel', [TimeLogController::class, 'exportExcel'])->name('export-excel');
    });

    // Invoice management API routes
    Route::prefix('invoices')->name('invoices.api.')->group(function () {
        // List and statistics
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/stats', [InvoiceController::class, 'stats'])->name('stats');
        Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{id}/history', [InvoiceController::class, 'history'])->name('history');
        Route::get('/{id}/pdf/download', [InvoiceController::class, 'downloadPdf'])->name('pdf-download');
        Route::get('/{id}/pdf/preview', [InvoiceController::class, 'previewPdf'])->name('pdf-preview');

        // CRUD operations (author only)
        Route::get('/unbilled-logs', [InvoiceController::class, 'getUnbilledLogs'])->middleware('author')->name('unbilled-logs');
        Route::post('/', [InvoiceController::class, 'store'])->middleware(['author', 'throttle:10,1'])->name('store');
        Route::put('/{id}', [InvoiceController::class, 'update'])->middleware('author')->name('update');
        Route::delete('/{id}', [InvoiceController::class, 'destroy'])->middleware('author')->name('destroy');

        // Invoice items (author only)
        Route::post('/{id}/items', [InvoiceController::class, 'addItem'])->middleware('author')->name('add-item');
        Route::put('/{id}/items/{itemId}', [InvoiceController::class, 'updateItem'])->middleware('author')->name('update-item');
        Route::delete('/{id}/items/{itemId}', [InvoiceController::class, 'removeItem'])->middleware('author')->name('remove-item');

        // Actions (author only)
        Route::post('/{id}/send-email', [InvoiceController::class, 'sendEmail'])->middleware(['author', 'throttle:5,1'])->name('send-email');
        Route::post('/{id}/mark-as-paid', [InvoiceController::class, 'markAsPaid'])->middleware('author')->name('mark-as-paid');
        Route::post('/{id}/cancel', [InvoiceController::class, 'cancel'])->middleware('author')->name('cancel');
    });

    // Application settings routes (author only)
    Route::prefix('settings')->name('settings.api.')->middleware('author')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/', [SettingController::class, 'update'])->middleware('throttle:10,1')->name('update');
        Route::post('/test-email', [SettingController::class, 'testEmail'])->middleware('throttle:3,1')->name('test-email');
        Route::post('/flush-cache', [SettingController::class, 'flushCache'])->middleware('throttle:3,1')->name('flush-cache');
        Route::get('/debug-email', [SettingController::class, 'debugEmail'])->name('debug-email');
    });
});

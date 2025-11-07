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
    Route::get('/{id}/backup', [ProjectController::class, 'backup'])->name('backup');
});

// Database backup routes
Route::prefix('backups')->name('backups.')->group(function () {
    Route::get('/', [DatabaseBackupController::class, 'index'])->name('index');
    Route::get('/create', [DatabaseBackupController::class, 'download'])->name('create');
    Route::get('/{filename}', [DatabaseBackupController::class, 'downloadFile'])->name('download');
    Route::delete('/{filename}', [DatabaseBackupController::class, 'deleteFile'])->name('delete');
});

// Legacy database backup route (for compatibility)
Route::get('/database/backup', [DatabaseBackupController::class, 'download'])->name('database.backup');

// Time tracking API routes
Route::prefix('timesheet')->name('timesheet.api.')->group(function () {

    // Session management
    Route::post('/clock-in', [TimeLogController::class, 'clockIn'])->name('clock-in');
    Route::post('/clock-out', [TimeLogController::class, 'clockOut'])->name('clock-out');
    Route::delete('/cancel-session', [TimeLogController::class, 'cancelSession'])->name('cancel-session');
    Route::get('/active-session', [TimeLogController::class, 'getActiveSession'])->name('active-session');

    // History and CRUD
    Route::get('/history', [TimeLogController::class, 'getHistory'])->name('history');
    Route::post('/logs', [TimeLogController::class, 'createLog'])->name('create-log');
    Route::get('/logs/{id}', [TimeLogController::class, 'getLog'])->name('get-log');
    Route::put('/logs/{id}', [TimeLogController::class, 'updateLog'])->name('update-log');
    Route::delete('/logs/{id}', [TimeLogController::class, 'deleteLog'])->name('delete-log');

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
    Route::get('/unbilled-logs', [InvoiceController::class, 'getUnbilledLogs'])->name('unbilled-logs');

    // CRUD operations
    Route::post('/', [InvoiceController::class, 'store'])->name('store');
    Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
    Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
    Route::delete('/{id}', [InvoiceController::class, 'destroy'])->name('destroy');

    // Invoice items
    Route::post('/{id}/items', [InvoiceController::class, 'addItem'])->name('add-item');
    Route::delete('/{id}/items/{itemId}', [InvoiceController::class, 'removeItem'])->name('remove-item');

    // Actions
    Route::post('/{id}/send-email', [InvoiceController::class, 'sendEmail'])->name('send-email');
    Route::post('/{id}/mark-as-paid', [InvoiceController::class, 'markAsPaid'])->name('mark-as-paid');

    // PDF generation
    Route::get('/{id}/pdf/download', [InvoiceController::class, 'downloadPdf'])->name('pdf-download');
    Route::get('/{id}/pdf/preview', [InvoiceController::class, 'previewPdf'])->name('pdf-preview');
});

// Application settings routes
Route::prefix('settings')->name('settings.api.')->group(function () {
    Route::get('/', [SettingController::class, 'index'])->name('index');
    Route::post('/', [SettingController::class, 'update'])->name('update');
    Route::post('/test-email', [SettingController::class, 'testEmail'])->name('test-email');
    Route::get('/debug-email', [SettingController::class, 'debugEmail'])->name('debug-email');
});

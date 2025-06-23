<?php

use App\Http\Controllers\TimesheetController;

// Timesheet routes
Route::get('/timesheet', [TimesheetController::class, 'index'])->name('timesheet.index');

// AJAX routes for timesheet functionality
Route::post('/timesheet/entries', [TimesheetController::class, 'storeEntry'])->name('timesheet.store');
Route::get('/timesheet/entries', [TimesheetController::class, 'getEntries'])->name('timesheet.entries');
Route::put('/timesheet/entries/{entry}', [TimesheetController::class, 'updateEntry'])->name('timesheet.update');
Route::delete('/timesheet/entries/{entry}', [TimesheetController::class, 'deleteEntry'])->name('timesheet.delete');
Route::get('/timesheet/report', [TimesheetController::class, 'getTwoWeekReport'])->name('timesheet.report');
Route::get('/timesheet/export', [TimesheetController::class, 'exportExcel'])->name('timesheet.export');
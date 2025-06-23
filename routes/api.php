<?php
use App\Http\Controllers\Api\WorkEntryController;

Route::prefix('timesheet')->group(function () {
    Route::get('/entries', [WorkEntryController::class, 'index']);
    Route::post('/entries', [WorkEntryController::class, 'store']);
    Route::put('/entries/{workEntry}', [WorkEntryController::class, 'update']);
    Route::delete('/entries/{workEntry}', [WorkEntryController::class, 'destroy']);
    Route::get('/report', [WorkEntryController::class, 'getTwoWeekReport']);
    Route::get('/export', [WorkEntryController::class, 'exportExcel']);
});
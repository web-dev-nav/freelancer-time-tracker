<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule full database backup every 2 days at 2:00 AM
// This backs up the entire database including all projects and time logs
Schedule::command('backup:database')
    ->dailyAt('02:00')
    ->days([0, 2, 4, 6]) // Runs every other day (Sun, Tue, Thu, Sat)
    ->name('database-backup')
    ->onSuccess(function () {
        info('Full database backup completed successfully');
    })
    ->onFailure(function () {
        error_log('Full database backup failed');
    });

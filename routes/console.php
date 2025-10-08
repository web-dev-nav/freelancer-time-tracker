<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule automatic backups every 2 days at 2:00 AM
Schedule::command('backup:projects')
    ->dailyAt('02:00')
    ->days([0, 2, 4, 6]) // Runs every other day (Sun, Tue, Thu, Sat)
    ->name('project-backup')
    ->onSuccess(function () {
        info('Project backup completed successfully');
    })
    ->onFailure(function () {
        error_log('Project backup failed');
    });

// Schedule full database backup every 2 days at 2:30 AM (30 minutes after project backup)
Schedule::command('backup:database')
    ->dailyAt('02:30')
    ->days([0, 2, 4, 6]) // Runs every other day (Sun, Tue, Thu, Sat)
    ->name('database-backup')
    ->onSuccess(function () {
        info('Database backup completed successfully');
    })
    ->onFailure(function () {
        error_log('Database backup failed');
    });

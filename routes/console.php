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

// Schedule invoice payment reminders daily at 9:00 AM
// Checks for unpaid invoices due within 3 days or overdue
Schedule::command('invoices:send-reminders')
    ->dailyAt('09:00')
    ->name('invoice-reminders')
    ->onSuccess(function () {
        info('Invoice payment reminders sent successfully');
    })
    ->onFailure(function () {
        error_log('Invoice payment reminders failed');
    });

// Schedule sending of scheduled invoice emails every 5 minutes
// Checks for invoices with scheduled_send_at <= now
Schedule::command('invoices:send-scheduled')
    ->everyFiveMinutes()
    ->name('send-scheduled-invoices')
    ->onSuccess(function () {
        info('Scheduled invoices processed successfully');
    })
    ->onFailure(function () {
        error_log('Scheduled invoices processing failed');
    });

// Schedule daily activity report checks every 5 minutes.
// Command sends once per day after configured send time.
Schedule::command('activity:send-daily-summary')
    ->everyFiveMinutes()
    ->name('daily-activity-report')
    ->onSuccess(function () {
        info('Daily activity report scheduler check executed');
    })
    ->onFailure(function () {
        error_log('Daily activity report scheduler check failed');
    });

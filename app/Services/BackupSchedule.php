<?php

namespace App\Services;

use App\Models\Setting;
use Carbon\Carbon;

class BackupSchedule
{
    public function settings(): array
    {
        return [
            'enabled' => filter_var(Setting::getValue('backup_enabled', config('backup.enabled')), FILTER_VALIDATE_BOOLEAN),
            'time' => Setting::getValue('backup_time', config('backup.time')),
            'retention_days' => max(7, min(365, (int) Setting::getValue('backup_retention_days', config('backup.retention_days')))),
        ];
    }

    public function due(): bool
    {
        $settings = $this->settings();
        $lastSuccess = Setting::getValue('backup_last_success_at');
        $lastAttempt = Setting::getValue('backup_last_attempt_at');

        return $settings['enabled']
            && now()->format('H:i') >= $settings['time']
            && (! $lastSuccess || Carbon::parse($lastSuccess)->toDateString() !== now()->toDateString())
            && (! $lastAttempt || Carbon::parse($lastAttempt)->lte(now()->subMinutes(15)));
    }

    public function status(): array
    {
        $settings = $this->settings();
        $next = now()->setTimeFromTimeString($settings['time'])->startOfMinute();
        if (now()->gt($next)) {
            $next = $this->due() ? now()->addMinute()->startOfMinute() : $next->addDay();
        }
        $heartbeat = Setting::getValue('scheduler_last_seen_at');

        return $settings + [
            'timezone' => config('app.timezone'),
            'next_run_at' => $settings['enabled'] ? $next->toIso8601String() : null,
            'last_success_at' => Setting::getValue('backup_last_success_at'),
            'last_attempt_at' => Setting::getValue('backup_last_attempt_at'),
            'last_error' => Setting::getValue('backup_last_error'),
            'scheduler_last_seen_at' => $heartbeat,
            'scheduler_healthy' => $heartbeat && Carbon::parse($heartbeat)->gte(now()->subMinutes(3)),
        ];
    }
}

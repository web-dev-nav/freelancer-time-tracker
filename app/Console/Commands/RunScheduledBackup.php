<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\BackupSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class RunScheduledBackup extends Command
{
    protected $signature = 'backup:scheduled';

    protected $description = 'Run the configured daily backup, with catch-up and retry after failures';

    public function handle(BackupSchedule $schedule): int
    {
        $lock = Cache::lock('scheduled-database-backup', 3600);
        if (! $lock->get()) {
            return self::SUCCESS;
        }
        try {
            if (! $schedule->due()) {
                return self::SUCCESS;
            }
            Setting::setValue('backup_last_attempt_at', now()->toIso8601String());
            $exitCode = Artisan::call('backup:database');
            $this->output->write(Artisan::output());

            return $exitCode;
        } finally {
            $lock->release();
        }
    }
}

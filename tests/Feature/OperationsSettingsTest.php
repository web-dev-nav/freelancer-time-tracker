<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Services\ApplicationLogReader;
use App\Services\BackupSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OperationsSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_logs_read_a_bounded_page_and_preserve_matching_stack_traces(): void
    {
        $directory = sys_get_temp_dir().'/tracker-log-test-'.uniqid();
        mkdir($directory.'/logs', 0777, true);
        $oldPath = storage_path();
        $this->app->useStoragePath($directory);
        try {
            file_put_contents($directory.'/logs/laravel-2026-10-22.log', str_repeat("[2026-10-22 08:00:00] local.INFO: old entry\n", 20000)."[2026-10-22 10:25:00] local.ERROR: broken\n#0 stack context\n[2026-10-22 11:00:00] local.INFO: later\n");
            $reader = new ApplicationLogReader;
            $page = $reader->read(['date' => '2026-10-22', 'lines' => 50, 'level' => 'error', 'start_time' => '10:00', 'end_time' => '10:59']);
            $this->assertLessThanOrEqual(ApplicationLogReader::MAX_BYTES, $page['bytes_read']);
            $this->assertStringContainsString('#0 stack context', $page['content']);
            $this->assertStringNotContainsString('later', $page['content']);
            $this->assertTrue($page['has_more']);
            $older = $reader->read(['date' => '2026-10-22', 'lines' => 50, 'before' => $page['next_cursor']]);
            $this->assertLessThan($page['next_cursor'], $older['next_cursor']);
            $this->assertGreaterThanOrEqual(7, count($page['available_dates']));
        } finally {
            $this->app->useStoragePath($oldPath);
            unlink($directory.'/logs/laravel-2026-10-22.log');
            rmdir($directory.'/logs');
            rmdir($directory);
        }
    }

    public function test_backup_schedule_is_configurable_validated_and_catches_up_once_per_day(): void
    {
        $this->actingAs(User::where('role', 'author')->first());
        $this->putJson('/api/settings/backups', ['enabled' => true, 'time' => '08:30', 'retention_days' => 14])->assertOk();
        $this->putJson('/api/settings/backups', ['enabled' => true, 'time' => '25:00', 'retention_days' => 2])->assertUnprocessable();
        $this->travelTo(now()->setTime(12, 0));
        $schedule = app(BackupSchedule::class);
        $this->assertTrue($schedule->due());
        Setting::setValue('backup_last_success_at', now()->toIso8601String());
        $this->assertFalse($schedule->due());
        Setting::setValue('backup_last_success_at', now()->subDay()->toIso8601String());
        Setting::setValue('backup_last_attempt_at', now()->subMinutes(2)->toIso8601String());
        $this->assertFalse($schedule->due());
        $this->travel(16)->minutes();
        $this->assertTrue($schedule->due());
    }

    public function test_database_backup_uses_the_folder_visible_to_the_backup_screen(): void
    {
        Storage::fake('local');
        $path = tempnam(sys_get_temp_dir(), 'tracker-sqlite-');
        $pdo = new \PDO('sqlite:'.$path);
        $pdo->exec('CREATE TABLE preview (id INTEGER); INSERT INTO preview VALUES (1);');
        config(['database.connections.sqlite.database' => $path]);
        try {
            $this->artisan('backup:database')->assertExitCode(0);
            $files = Storage::disk('local')->files('backups');
            $this->assertCount(2, $files);
            $this->assertNotNull(Setting::getValue('backup_last_success_at'));
            $this->actingAs(User::where('role', 'author')->first())->getJson('/api/backups')->assertOk()->assertJsonCount(2, 'data');
        } finally {
            unlink($path);
        }
    }

    public function test_old_scheduler_backups_remain_visible_and_downloadable(): void
    {
        Storage::fake('local');
        $directory = sys_get_temp_dir().'/tracker-legacy-backup-'.uniqid();
        mkdir($directory.'/app/backups', 0777, true);
        file_put_contents($directory.'/app/backups/database-full-old-backup.sql', '-- legacy backup');
        $previous = storage_path();
        $this->app->useStoragePath($directory);
        try {
            $this->actingAs(User::where('role', 'author')->first());
            $this->getJson('/api/backups')->assertOk()->assertJsonFragment(['filename' => 'database-full-old-backup.sql']);
            $this->get('/api/backups/database-full-old-backup.sql')->assertDownload('database-full-old-backup.sql');
        } finally {
            $this->app->useStoragePath($previous);
            unlink($directory.'/app/backups/database-full-old-backup.sql');
            rmdir($directory.'/app/backups');
            rmdir($directory.'/app');
            rmdir($directory);
        }
    }
}

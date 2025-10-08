<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:projects {--project-id= : Backup specific project ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup all projects data to SQL files (runs every 2 days via scheduler)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting project backup...');

        // Get projects to backup
        if ($projectId = $this->option('project-id')) {
            $projects = Project::where('id', $projectId)->get();
            if ($projects->isEmpty()) {
                $this->error("Project with ID {$projectId} not found.");
                return 1;
            }
        } else {
            // Backup all active projects
            $projects = Project::active()->get();
        }

        if ($projects->isEmpty()) {
            $this->warn('No projects found to backup.');
            return 0;
        }

        $this->info("Found {$projects->count()} project(s) to backup.");

        $backupCount = 0;
        $errors = [];

        foreach ($projects as $project) {
            try {
                $this->line("Backing up: {$project->name}...");

                $timeLogs = $project->timeLogs()->orderBy('clock_in', 'asc')->get();
                $sql = $this->generateSqlBackup($project, $timeLogs);

                // Generate filename with timestamp
                $filename = sprintf(
                    'project-%s-%s-backup.sql',
                    $project->id,
                    now()->format('Y-m-d_His')
                );

                // Save to storage/app/backups
                Storage::put("backups/{$filename}", $sql);

                $this->info("✓ Backed up: {$project->name} ({$timeLogs->count()} time logs)");
                $backupCount++;
            } catch (\Exception $e) {
                $error = "✗ Failed to backup {$project->name}: {$e->getMessage()}";
                $this->error($error);
                $errors[] = $error;
            }
        }

        $this->newLine();
        $this->info("Backup complete! {$backupCount}/{$projects->count()} projects backed up successfully.");

        // Cleanup old backups (keep last 30 days)
        $this->cleanupOldBackups();

        if (!empty($errors)) {
            $this->newLine();
            $this->error('Errors occurred during backup:');
            foreach ($errors as $error) {
                $this->line($error);
            }
            return 1;
        }

        return 0;
    }

    /**
     * Generate SQL backup content for a project
     */
    private function generateSqlBackup($project, $timeLogs)
    {
        $sql = "-- ==================================================\n";
        $sql .= "-- Project Backup for: {$project->name}\n";
        $sql .= "-- Generated: " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- Total Time Logs: " . $timeLogs->count() . "\n";
        $sql .= "-- ==================================================\n\n";

        $sql .= "-- ==================================================\n";
        $sql .= "-- Database Schema\n";
        $sql .= "-- ==================================================\n\n";

        // Projects table schema
        $sql .= "CREATE TABLE IF NOT EXISTS `projects` (\n";
        $sql .= "  `id` bigint unsigned NOT NULL AUTO_INCREMENT,\n";
        $sql .= "  `name` varchar(255) NOT NULL,\n";
        $sql .= "  `client_name` varchar(255) DEFAULT NULL,\n";
        $sql .= "  `color` varchar(255) DEFAULT '#8b5cf6',\n";
        $sql .= "  `hourly_rate` decimal(8,2) DEFAULT NULL,\n";
        $sql .= "  `status` enum('active','archived') DEFAULT 'active',\n";
        $sql .= "  `description` text,\n";
        $sql .= "  `created_at` timestamp NULL DEFAULT NULL,\n";
        $sql .= "  `updated_at` timestamp NULL DEFAULT NULL,\n";
        $sql .= "  PRIMARY KEY (`id`),\n";
        $sql .= "  KEY `projects_status_index` (`status`),\n";
        $sql .= "  KEY `projects_status_created_at_index` (`status`,`created_at`)\n";
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

        // Time logs table schema
        $sql .= "CREATE TABLE IF NOT EXISTS `time_logs` (\n";
        $sql .= "  `id` bigint unsigned NOT NULL AUTO_INCREMENT,\n";
        $sql .= "  `session_id` varchar(255) NOT NULL,\n";
        $sql .= "  `project_id` bigint unsigned DEFAULT NULL,\n";
        $sql .= "  `status` enum('active','completed','cancelled') DEFAULT 'active',\n";
        $sql .= "  `clock_in` timestamp NOT NULL,\n";
        $sql .= "  `clock_out` timestamp NULL DEFAULT NULL,\n";
        $sql .= "  `total_minutes` int DEFAULT NULL,\n";
        $sql .= "  `work_description` text,\n";
        $sql .= "  `project_name` varchar(255) DEFAULT NULL,\n";
        $sql .= "  `ip_address` varchar(255) DEFAULT NULL,\n";
        $sql .= "  `user_agent` varchar(255) DEFAULT NULL,\n";
        $sql .= "  `created_at` timestamp NULL DEFAULT NULL,\n";
        $sql .= "  `updated_at` timestamp NULL DEFAULT NULL,\n";
        $sql .= "  PRIMARY KEY (`id`),\n";
        $sql .= "  UNIQUE KEY `time_logs_session_id_unique` (`session_id`),\n";
        $sql .= "  KEY `time_logs_status_clock_in_index` (`status`,`clock_in`),\n";
        $sql .= "  KEY `time_logs_clock_in_index` (`clock_in`),\n";
        $sql .= "  KEY `time_logs_session_id_index` (`session_id`),\n";
        $sql .= "  KEY `time_logs_project_id_index` (`project_id`),\n";
        $sql .= "  CONSTRAINT `time_logs_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL\n";
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

        $sql .= "-- ==================================================\n";
        $sql .= "-- Project Data\n";
        $sql .= "-- ==================================================\n\n";

        // Project table insert
        $sql .= "INSERT INTO `projects` (`id`, `name`, `client_name`, `color`, `hourly_rate`, `status`, `description`, `created_at`, `updated_at`) VALUES\n";
        $sql .= sprintf(
            "(%d, %s, %s, %s, %s, %s, %s, %s, %s)\n",
            $project->id,
            $this->sqlEscape($project->name),
            $this->sqlEscape($project->client_name),
            $this->sqlEscape($project->color),
            $project->hourly_rate ? "'{$project->hourly_rate}'" : 'NULL',
            $this->sqlEscape($project->status),
            $this->sqlEscape($project->description),
            $this->sqlEscape($project->created_at),
            $this->sqlEscape($project->updated_at)
        );
        $sql .= "ON DUPLICATE KEY UPDATE\n";
        $sql .= "  `name` = VALUES(`name`),\n";
        $sql .= "  `client_name` = VALUES(`client_name`),\n";
        $sql .= "  `color` = VALUES(`color`),\n";
        $sql .= "  `hourly_rate` = VALUES(`hourly_rate`),\n";
        $sql .= "  `status` = VALUES(`status`),\n";
        $sql .= "  `description` = VALUES(`description`),\n";
        $sql .= "  `updated_at` = VALUES(`updated_at`);\n\n";

        // Time logs
        if ($timeLogs->count() > 0) {
            $sql .= "-- ==================================================\n";
            $sql .= "-- Time Logs Data\n";
            $sql .= "-- ==================================================\n\n";

            $sql .= "INSERT INTO `time_logs` (`id`, `session_id`, `project_id`, `status`, `clock_in`, `clock_out`, `work_description`, `project_name`, `total_minutes`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES\n";

            $values = [];
            foreach ($timeLogs as $log) {
                $values[] = sprintf(
                    "(%d, %s, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                    $log->id,
                    $this->sqlEscape($log->session_id),
                    $log->project_id,
                    $this->sqlEscape($log->status),
                    $this->sqlEscape($log->clock_in),
                    $this->sqlEscape($log->clock_out),
                    $this->sqlEscape($log->work_description),
                    $this->sqlEscape($log->project_name),
                    $log->total_minutes ? "'{$log->total_minutes}'" : 'NULL',
                    $this->sqlEscape($log->ip_address),
                    $this->sqlEscape($log->user_agent),
                    $this->sqlEscape($log->created_at),
                    $this->sqlEscape($log->updated_at)
                );
            }

            $sql .= implode(",\n", $values) . "\n";
            $sql .= "ON DUPLICATE KEY UPDATE\n";
            $sql .= "  `status` = VALUES(`status`),\n";
            $sql .= "  `clock_out` = VALUES(`clock_out`),\n";
            $sql .= "  `work_description` = VALUES(`work_description`),\n";
            $sql .= "  `total_minutes` = VALUES(`total_minutes`),\n";
            $sql .= "  `updated_at` = VALUES(`updated_at`);\n\n";
        }

        // Summary
        $sql .= "-- ==================================================\n";
        $sql .= "-- Backup Summary\n";
        $sql .= "-- ==================================================\n";
        $sql .= "-- Project: {$project->name}\n";
        if ($project->client_name) {
            $sql .= "-- Client: {$project->client_name}\n";
        }
        $sql .= "-- Status: {$project->status}\n";
        $sql .= "-- Total Sessions: " . $timeLogs->count() . "\n";
        $sql .= "-- Total Hours: " . round($project->total_hours, 2) . "\n";
        if ($project->hourly_rate) {
            $sql .= "-- Hourly Rate: \${$project->hourly_rate}\n";
            $sql .= "-- Total Earnings: \$" . number_format($project->total_earnings, 2) . "\n";
        }
        $sql .= "-- ==================================================\n\n";

        $sql .= "-- To restore this backup:\n";
        $sql .= "-- 1. Create or select your database\n";
        $sql .= "-- 2. Run: mysql -u username -p database_name < this_file.sql\n";
        $sql .= "-- Or import via phpMyAdmin, MySQL Workbench, etc.\n";

        return $sql;
    }

    /**
     * Escape and quote value for SQL
     */
    private function sqlEscape($value)
    {
        if (is_null($value)) {
            return 'NULL';
        }

        // Escape single quotes and wrap in quotes
        $escaped = str_replace("'", "''", $value);
        return "'{$escaped}'";
    }

    /**
     * Cleanup old backup files (keep last 30 days)
     */
    private function cleanupOldBackups()
    {
        $this->line('Cleaning up old backups (keeping last 30 days)...');

        $files = Storage::files('backups');
        $deletedCount = 0;
        $cutoffDate = now()->subDays(30);

        foreach ($files as $file) {
            $lastModified = Storage::lastModified($file);
            if ($lastModified < $cutoffDate->timestamp) {
                Storage::delete($file);
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            $this->info("✓ Deleted {$deletedCount} old backup file(s).");
        } else {
            $this->line('No old backups to delete.');
        }
    }
}

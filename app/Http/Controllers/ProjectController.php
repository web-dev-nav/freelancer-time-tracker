<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Get all projects with optional filtering by status
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'active');

        $query = Project::query();

        if ($status === 'active') {
            $query->active();
        } elseif ($status === 'archived') {
            $query->archived();
        }

        $projects = $query->orderBy('name')->get();

        // Load stats for each project
        $projects->each(function ($project) {
            $project->loadCount('timeLogs');
            $project->setAttribute('total_hours', $project->total_hours);
            $project->setAttribute('total_sessions', $project->total_sessions);
            $project->setAttribute('total_earnings', $project->total_earnings);
            $project->setAttribute('has_time_logs', $project->timeLogs()->count() > 0);
        });

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * Get active projects (for dropdowns)
     */
    public function active()
    {
        $projects = Project::active()
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'client_name',
                'client_email',
                'client_address',
                'color',
                'hourly_rate',
                'has_tax',
            ]);

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * Get a single project with stats
     */
    public function show($id)
    {
        $project = Project::findOrFail($id);

        $project->loadCount('timeLogs');
        $project->setAttribute('total_hours', $project->total_hours);
        $project->setAttribute('total_sessions', $project->total_sessions);
        $project->setAttribute('total_earnings', $project->total_earnings);

        return response()->json([
            'success' => true,
            'data' => $project
        ]);
    }

    /**
     * Create a new project
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'nullable|string|max:2000',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'hourly_rate' => 'nullable|numeric|min:0',
            'has_tax' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000'
        ]);

        $project = Project::create([
            'name' => $request->name,
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'client_address' => $request->client_address,
            'color' => $request->color ?? '#8b5cf6',
            'hourly_rate' => $request->hourly_rate,
            'has_tax' => $request->has_tax ?? false,
            'description' => $request->description,
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'data' => $project
        ], 201);
    }

    /**
     * Update a project
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'nullable|string|max:2000',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'hourly_rate' => 'nullable|numeric|min:0',
            'has_tax' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|in:active,archived'
        ]);

        $project = Project::findOrFail($id);

        $project->update([
            'name' => $request->name,
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'client_address' => $request->client_address,
            'color' => $request->color ?? $project->color,
            'hourly_rate' => $request->hourly_rate,
            'has_tax' => $request->has_tax ?? $project->has_tax,
            'description' => $request->description,
            'status' => $request->status ?? $project->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'data' => $project->fresh()
        ]);
    }

    /**
     * Archive a project
     */
    public function archive($id)
    {
        $project = Project::findOrFail($id);
        $project->archive();

        return response()->json([
            'success' => true,
            'message' => 'Project archived successfully',
            'data' => $project
        ]);
    }

    /**
     * Activate a project
     */
    public function activate($id)
    {
        $project = Project::findOrFail($id);
        $project->activate();

        return response()->json([
            'success' => true,
            'message' => 'Project activated successfully',
            'data' => $project
        ]);
    }

    /**
     * Delete a project (only if no time logs)
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        // Check if project has time logs
        if ($project->timeLogs()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete project with existing time logs. Archive it instead.'
            ], 422);
        }

        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully'
        ]);
    }

    /**
     * Get project statistics
     */
    public function stats($id)
    {
        $project = Project::findOrFail($id);

        $timeLogs = $project->timeLogs()->completed()->get();

        $stats = [
            'total_hours' => $project->total_hours,
            'total_minutes' => $project->total_minutes,
            'total_sessions' => $project->total_sessions,
            'total_earnings' => $project->total_earnings,
            'average_session_hours' => $project->total_sessions > 0
                ? round($project->total_hours / $project->total_sessions, 2)
                : 0,
            'recent_activity' => $timeLogs->take(5)->map(function ($log) {
                return [
                    'id' => $log->id,
                    'date' => $log->clock_in->format('Y-m-d'),
                    'hours' => $log->duration_hours,
                    'description' => $log->work_description
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Backup project data as SQL file
     */
    public function backup($id)
    {
        $project = Project::findOrFail($id);
        $timeLogs = $project->timeLogs()->orderBy('clock_in', 'asc')->get();

        // Generate SQL dump
        $sql = $this->generateSqlBackup($project, $timeLogs);

        // Generate filename with timestamp for uniqueness
        $filename = sprintf(
            'project-%s-%s-%s-backup.sql',
            $project->id,
            \Illuminate\Support\Str::slug($project->name),
            now()->format('Y-m-d_His')
        );

        // Save backup to storage
        \Illuminate\Support\Facades\Storage::disk('local')->put('backups/' . $filename, $sql);

        return response()->json([
            'success' => true,
            'message' => 'Project backup created successfully',
            'filename' => $filename
        ]);
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
}

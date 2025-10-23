<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class DatabaseBackupController extends Controller
{
    /**
     * List all backup files with pagination
     */
    public function index(Request $request)
    {
        $backups = [];
        $files = Storage::disk('local')->files('backups');

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $backups[] = [
                    'filename' => basename($file),
                    'path' => $file,
                    'size' => Storage::disk('local')->size($file),
                    'created_at' => Storage::disk('local')->lastModified($file),
                    'formatted_size' => $this->formatBytes(Storage::disk('local')->size($file)),
                    'formatted_date' => date('Y-m-d H:i:s', Storage::disk('local')->lastModified($file))
                ];
            }
        }

        // Sort by date descending (newest first)
        usort($backups, function($a, $b) {
            return $b['created_at'] - $a['created_at'];
        });

        // Pagination
        $perPage = $request->get('per_page', 10); // Default 10 items per page
        $page = $request->get('page', 1);
        $total = count($backups);
        $totalPages = ceil($total / $perPage);

        // Slice the array for pagination
        $offset = ($page - 1) * $perPage;
        $paginatedBackups = array_slice($backups, $offset, $perPage);

        return response()->json([
            'success' => true,
            'data' => $paginatedBackups,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'has_more' => $page < $totalPages
            ]
        ]);
    }

    /**
     * Download a specific backup file
     */
    public function downloadFile($filename)
    {
        $path = 'backups/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Backup file not found'
            ], 404);
        }

        return Storage::disk('local')->download($path);
    }

    /**
     * Delete a backup file
     */
    public function deleteFile($filename)
    {
        $path = 'backups/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Backup file not found'
            ], 404);
        }

        Storage::disk('local')->delete($path);

        return response()->json([
            'success' => true,
            'message' => 'Backup deleted successfully'
        ]);
    }

    /**
     * Download complete database backup
     */
    public function download()
    {
        try {
            $dbConnection = config('database.default');
            $dbConfig = config("database.connections.{$dbConnection}");

            if ($dbConnection === 'sqlite') {
                return $this->downloadSqliteBackup($dbConfig);
            } elseif (in_array($dbConnection, ['mysql', 'mariadb'])) {
                return $this->downloadMysqlBackup($dbConfig);
            } elseif ($dbConnection === 'pgsql') {
                return $this->downloadPostgresBackup($dbConfig);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Unsupported database type: {$dbConnection}"
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download SQLite database backup
     */
    private function downloadSqliteBackup($config)
    {
        $dbPath = $config['database'];

        if (!file_exists($dbPath)) {
            throw new \Exception("SQLite database file not found");
        }

        // Generate SQL dump
        $sql = $this->generateSqliteDump($dbPath);

        // Generate filename
        $filename = sprintf(
            'database-full-%s-backup.sql',
            now()->format('Y-m-d_His')
        );

        // Save backup to storage
        Storage::disk('local')->put('backups/' . $filename, $sql);

        return response($sql, 200)
            ->header('Content-Type', 'application/sql')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Generate SQLite SQL dump
     */
    private function generateSqliteDump($dbPath)
    {
        $sql = "-- ==================================================\n";
        $sql .= "-- Full Database Backup\n";
        $sql .= "-- Generated: " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database Type: SQLite\n";
        $sql .= "-- ==================================================\n\n";

        // Get all tables
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");

        foreach ($tables as $table) {
            $tableName = $table->name;

            // Get table schema
            $createTable = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$tableName]);
            if (!empty($createTable)) {
                $sql .= "-- ==================================================\n";
                $sql .= "-- Table: {$tableName}\n";
                $sql .= "-- ==================================================\n\n";
                $sql .= $createTable[0]->sql . ";\n\n";

                // Get table data
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sql .= "-- Data for table: {$tableName}\n";

                    foreach ($rows as $row) {
                        $columns = array_keys((array)$row);
                        $values = array_map(function($value) {
                            return $this->sqlEscape($value);
                        }, array_values((array)$row));

                        $sql .= sprintf(
                            "INSERT INTO `%s` (`%s`) VALUES (%s);\n",
                            $tableName,
                            implode('`, `', $columns),
                            implode(', ', $values)
                        );
                    }
                    $sql .= "\n";
                }
            }
        }

        $sql .= "-- ==================================================\n";
        $sql .= "-- Backup completed successfully\n";
        $sql .= "-- ==================================================\n";

        return $sql;
    }

    /**
     * Download MySQL database backup
     */
    private function downloadMysqlBackup($config)
    {
        $sql = $this->generateMysqlDump($config);

        $filename = sprintf(
            'database-full-%s-backup.sql',
            now()->format('Y-m-d_His')
        );

        // Save backup to storage
        Storage::disk('local')->put('backups/' . $filename, $sql);

        return response($sql, 200)
            ->header('Content-Type', 'application/sql')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Generate MySQL SQL dump
     */
    private function generateMysqlDump($config)
    {
        $sql = "-- ==================================================\n";
        $sql .= "-- Full Database Backup\n";
        $sql .= "-- Generated: " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database Type: MySQL\n";
        $sql .= "-- Database: {$config['database']}\n";
        $sql .= "-- ==================================================\n\n";

        $tables = DB::select('SHOW TABLES');
        $dbName = $config['database'];

        foreach ($tables as $table) {
            $tableName = $table->{"Tables_in_{$dbName}"};

            // Get CREATE TABLE statement
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "-- ==================================================\n";
            $sql .= "-- Table: {$tableName}\n";
            $sql .= "-- ==================================================\n\n";
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

            // Get table data
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $sql .= "-- Data for table: {$tableName}\n";
                $sql .= "LOCK TABLES `{$tableName}` WRITE;\n";

                $columns = array_keys((array)$rows->first());
                $sql .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES\n";

                $values = [];
                foreach ($rows as $row) {
                    $rowValues = array_map(function($value) {
                        return $this->sqlEscape($value);
                    }, array_values((array)$row));
                    $values[] = '(' . implode(', ', $rowValues) . ')';
                }

                $sql .= implode(",\n", $values) . ";\n";
                $sql .= "UNLOCK TABLES;\n\n";
            }
        }

        $sql .= "-- ==================================================\n";
        $sql .= "-- Backup completed successfully\n";
        $sql .= "-- ==================================================\n";

        return $sql;
    }

    /**
     * Download PostgreSQL database backup
     */
    private function downloadPostgresBackup($config)
    {
        // For PostgreSQL, we'll use a simplified approach
        // In production, you might want to use pg_dump command
        $sql = "-- PostgreSQL backup\n";
        $sql .= "-- Generated: " . now()->format('Y-m-d H:i:s') . "\n\n";

        $tables = DB::select("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public'");

        foreach ($tables as $table) {
            $tableName = $table->tablename;

            // Get table data
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $sql .= "-- Table: {$tableName}\n";
                $columns = array_keys((array)$rows->first());

                foreach ($rows as $row) {
                    $values = array_map(function($value) {
                        return $this->sqlEscape($value);
                    }, array_values((array)$row));

                    $sql .= sprintf(
                        "INSERT INTO \"%s\" (\"%s\") VALUES (%s);\n",
                        $tableName,
                        implode('", "', $columns),
                        implode(', ', $values)
                    );
                }
                $sql .= "\n";
            }
        }

        $filename = sprintf(
            'database-full-%s-backup.sql',
            now()->format('Y-m-d_His')
        );

        // Save backup to storage
        Storage::disk('local')->put('backups/' . $filename, $sql);

        return response($sql, 200)
            ->header('Content-Type', 'application/sql')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Escape and quote value for SQL
     */
    private function sqlEscape($value)
    {
        if (is_null($value)) {
            return 'NULL';
        }

        if (is_numeric($value)) {
            return $value;
        }

        // Escape single quotes and wrap in quotes
        $escaped = str_replace("'", "''", $value);
        return "'{$escaped}'";
    }

    /**
     * Format bytes to human readable size
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

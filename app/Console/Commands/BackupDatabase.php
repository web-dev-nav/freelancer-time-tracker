<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--type=full : Backup type: full or schema-only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup entire database to SQL file (runs every 2 days via scheduler)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting full database backup...');

        $dbConnection = config('database.default');
        $dbConfig = config("database.connections.{$dbConnection}");

        try {
            if ($dbConnection === 'sqlite') {
                $this->backupSqlite($dbConfig);
            } elseif (in_array($dbConnection, ['mysql', 'mariadb'])) {
                $this->backupMysql($dbConfig);
            } elseif ($dbConnection === 'pgsql') {
                $this->backupPostgres($dbConfig);
            } else {
                $this->error("Unsupported database type: {$dbConnection}");
                return 1;
            }

            $this->cleanupOldBackups();
            $this->info('✓ Database backup completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error("Backup failed: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Backup SQLite database
     */
    private function backupSqlite($config)
    {
        $dbPath = $config['database'];

        if (!file_exists($dbPath)) {
            throw new \Exception("SQLite database file not found: {$dbPath}");
        }

        // Generate filename with timestamp
        $filename = sprintf(
            'database-full-%s-backup.sqlite',
            now()->format('Y-m-d_His')
        );

        // Copy SQLite file
        $backupPath = storage_path("app/backups/{$filename}");

        if (!copy($dbPath, $backupPath)) {
            throw new \Exception("Failed to copy SQLite database file");
        }

        $fileSize = $this->formatBytes(filesize($backupPath));
        $this->info("✓ SQLite database backed up: {$filename} ({$fileSize})");

        // Also create SQL dump for portability
        $this->createSqliteSqlDump($dbPath);
    }

    /**
     * Create SQL dump from SQLite database
     */
    private function createSqliteSqlDump($dbPath)
    {
        $filename = sprintf(
            'database-full-%s-backup.sql',
            now()->format('Y-m-d_His')
        );

        $backupPath = storage_path("app/backups/{$filename}");

        // Use sqlite3 command to generate SQL dump
        $command = sprintf(
            'sqlite3 %s .dump > %s 2>&1',
            escapeshellarg($dbPath),
            escapeshellarg($backupPath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($backupPath)) {
            $fileSize = $this->formatBytes(filesize($backupPath));
            $this->info("✓ SQL dump created: {$filename} ({$fileSize})");
        } else {
            // Fallback to manual SQL generation if sqlite3 command is not available
            $this->createManualSqlDump($backupPath);
        }
    }

    /**
     * Create manual SQL dump (fallback method)
     */
    private function createManualSqlDump($backupPath)
    {
        $sql = "-- ==================================================\n";
        $sql .= "-- Full Database Backup\n";
        $sql .= "-- Generated: " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: " . config('database.connections.' . config('database.default') . '.database') . "\n";
        $sql .= "-- ==================================================\n\n";

        // Get all tables
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");

        foreach ($tables as $table) {
            $tableName = $table->name;
            $this->line("Backing up table: {$tableName}");

            // Get table schema
            $createTable = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$tableName]);
            if (!empty($createTable)) {
                $sql .= "-- Table: {$tableName}\n";
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

        file_put_contents($backupPath, $sql);
        $fileSize = $this->formatBytes(filesize($backupPath));
        $this->info("✓ Manual SQL dump created: " . basename($backupPath) . " ({$fileSize})");
    }

    /**
     * Backup MySQL database
     */
    private function backupMysql($config)
    {
        $filename = sprintf(
            'database-full-%s-backup.sql',
            now()->format('Y-m-d_His')
        );

        $backupPath = storage_path("app/backups/{$filename}");

        $command = sprintf(
            'mysqldump -h %s -P %s -u %s %s %s > %s 2>&1',
            escapeshellarg($config['host']),
            escapeshellarg($config['port'] ?? 3306),
            escapeshellarg($config['username']),
            $config['password'] ? '-p' . escapeshellarg($config['password']) : '',
            escapeshellarg($config['database']),
            escapeshellarg($backupPath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($backupPath)) {
            // Fallback to manual method
            $this->info('mysqldump not available, using manual backup method...');
            $this->createManualMysqlDump($backupPath);
        } else {
            $fileSize = $this->formatBytes(filesize($backupPath));
            $this->info("✓ MySQL database backed up: {$filename} ({$fileSize})");
        }
    }

    /**
     * Create manual MySQL dump
     */
    private function createManualMysqlDump($backupPath)
    {
        $sql = "-- ==================================================\n";
        $sql .= "-- Full Database Backup\n";
        $sql .= "-- Generated: " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- ==================================================\n\n";

        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');

        foreach ($tables as $table) {
            $tableName = $table->{"Tables_in_{$dbName}"};
            $this->line("Backing up table: {$tableName}");

            // Get CREATE TABLE statement
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

            // Get table data
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $sql .= "-- Data for table: {$tableName}\n";

                $columns = array_keys((array)$rows->first());
                $sql .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES\n";

                $values = [];
                foreach ($rows as $row) {
                    $rowValues = array_map(function($value) {
                        return $this->sqlEscape($value);
                    }, array_values((array)$row));
                    $values[] = '(' . implode(', ', $rowValues) . ')';
                }

                $sql .= implode(",\n", $values) . ";\n\n";
            }
        }

        file_put_contents($backupPath, $sql);
        $fileSize = $this->formatBytes(filesize($backupPath));
        $this->info("✓ MySQL manual dump created: " . basename($backupPath) . " ({$fileSize})");
    }

    /**
     * Backup PostgreSQL database
     */
    private function backupPostgres($config)
    {
        $filename = sprintf(
            'database-full-%s-backup.sql',
            now()->format('Y-m-d_His')
        );

        $backupPath = storage_path("app/backups/{$filename}");

        $command = sprintf(
            'PGPASSWORD=%s pg_dump -h %s -p %s -U %s -F p -f %s %s 2>&1',
            escapeshellarg($config['password']),
            escapeshellarg($config['host']),
            escapeshellarg($config['port'] ?? 5432),
            escapeshellarg($config['username']),
            escapeshellarg($backupPath),
            escapeshellarg($config['database'])
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception("PostgreSQL backup failed: " . implode("\n", $output));
        }

        $fileSize = $this->formatBytes(filesize($backupPath));
        $this->info("✓ PostgreSQL database backed up: {$filename} ({$fileSize})");
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

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
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
            // Only delete database backup files, not project backups
            if (strpos($file, 'database-full-') !== false) {
                $lastModified = Storage::lastModified($file);
                if ($lastModified < $cutoffDate->timestamp) {
                    Storage::delete($file);
                    $deletedCount++;
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("✓ Deleted {$deletedCount} old database backup(s).");
        } else {
            $this->line('No old database backups to delete.');
        }
    }
}

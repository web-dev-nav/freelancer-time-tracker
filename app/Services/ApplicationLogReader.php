<?php

namespace App\Services;

class ApplicationLogReader
{
    public const MAX_BYTES = 262144;

    public function read(array $filters): array
    {
        $date = $filters['date'] ?? now()->toDateString();
        $legacy = ($filters['file'] ?? '') === 'laravel.log';
        $filename = $legacy ? 'laravel.log' : "laravel-{$date}.log";
        $directory = storage_path('logs');
        $path = $directory.'/'.$filename;
        $files = [];
        foreach (glob($directory.'/laravel-????-??-??.log') ?: [] as $file) {
            if (is_link($file) || ! is_file($file)) {
                continue;
            }
            $name = basename($file);
            $files[] = ['name' => $name, 'date' => substr($name, 8, 10), 'size' => filesize($file)];
        }
        usort($files, fn ($a, $b) => strcmp($b['date'], $a['date']));
        $days = array_column($files, 'date');
        for ($i = 0; $i < 7; $i++) {
            $days[] = now()->subDays($i)->toDateString();
        }
        $days = array_values(array_unique($days));
        rsort($days);
        $result = [
            'selected_file' => $filename, 'selected_date' => $date, 'available_files' => $files,
            'available_dates' => $days, 'legacy_available' => is_file($directory.'/laravel.log') && ! is_link($directory.'/laravel.log'),
            'content' => '', 'line_count' => 0, 'level' => $filters['level'] ?? 'all',
            'next_cursor' => null, 'has_more' => false, 'bytes_read' => 0,
            'timezone' => config('app.timezone'), 'retention_days' => config('logging.channels.daily.days'),
        ];
        if (! is_file($path) || is_link($path)) {
            return $result;
        }
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('Log file could not be opened.');
        }
        try {
            $size = fstat($handle)['size'];
            $end = min($size, (int) ($filters['before'] ?? $size));
            $start = max(0, $end - self::MAX_BYTES);
            fseek($handle, $start);
            $text = $end > $start ? fread($handle, $end - $start) : '';
            $result['bytes_read'] = strlen($text);
            // Start at a whole line, without scanning from the beginning of a large file.
            if ($start > 0 && ($newline = strpos($text, "\n")) !== false) {
                $boundary = $start + $newline + 1;
                $text = substr($text, $newline + 1);
                if ($text !== '') {
                    $start = $boundary;
                }
            }
            $lines = explode("\n", rtrim($text, "\r\n"));
            $limit = (int) ($filters['lines'] ?? 200);
            if (count($lines) > $limit) {
                $removed = array_splice($lines, 0, count($lines) - $limit);
                $start += strlen(implode("\n", $removed)) + 1;
            }
            $result['next_cursor'] = $start > 0 ? $start : null;
            $result['has_more'] = $start > 0;
            $matching = [];
            $include = false;
            foreach ($lines as $line) {
                if (preg_match('/^\[(\d{4}-\d{2}-\d{2}) (\d{2}:\d{2}):\d{2}\].*?\.([A-Z]+):/', $line, $match)) {
                    $include = (! $legacy || $match[1] === $date)
                        && (($filters['level'] ?? 'all') === 'all' || strtolower($match[3]) === $filters['level'])
                        && (empty($filters['start_time']) || $match[2] >= $filters['start_time'])
                        && (empty($filters['end_time']) || $match[2] <= $filters['end_time']);
                }
                // Keep stack traces with the matching entry rather than discarding context.
                if ($include) {
                    $matching[] = rtrim($line, "\r");
                }
            }
            $result['content'] = implode("\n", $matching);
            $result['line_count'] = count($matching);

            return $result;
        } finally {
            fclose($handle);
        }
    }
}

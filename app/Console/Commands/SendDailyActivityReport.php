<?php

namespace App\Console\Commands;

use App\Models\DailyActivitySchedule;
use App\Models\Setting;
use App\Models\TimeLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class SendDailyActivityReport extends Command
{
    protected $signature = 'activity:send-daily-summary {--force : Ignore time and last-sent checks}';

    protected $description = 'Send daily activity summary email for today\'s completed work logs';

    public function handle(): int
    {
        if (Schema::hasTable('daily_activity_schedules') && DailyActivitySchedule::query()->exists()) {
            return $this->handlePerClientSchedules();
        }

        return $this->handleLegacyGlobalSchedule();
    }

    private function handlePerClientSchedules(): int
    {
        $timezone = config('app.timezone', 'UTC');
        $nowLocal = Carbon::now($timezone);
        $today = $nowLocal->toDateString();
        $forceSend = (bool) $this->option('force');

        [$startUtc, $endUtc] = $this->resolveDayUtcRange($today, $timezone);
        $mailerConfig = $this->prepareMailerConfiguration($this->getEmailSettings());
        $schedules = DailyActivitySchedule::query()
            ->where('enabled', true)
            ->orderBy('client_email')
            ->get();

        if ($schedules->isEmpty()) {
            $this->line('No enabled per-client daily activity schedules.');
            return self::SUCCESS;
        }

        $sentCount = 0;

        foreach ($schedules as $schedule) {
            $sendTime = (string) $schedule->send_time;
            if (!preg_match('/^([01]\\d|2[0-3]):[0-5]\\d$/', $sendTime)) {
                Log::warning('Daily activity schedule skipped: invalid send time', [
                    'client_email' => $schedule->client_email,
                    'send_time' => $sendTime,
                ]);
                continue;
            }

            $scheduleType = strtolower(trim((string) ($schedule->schedule_type ?? 'daily')));
            if (!in_array($scheduleType, ['daily', 'date'], true)) {
                $scheduleType = 'daily';
            }

            if (!$forceSend) {
                if ($scheduleType === 'date') {
                    $sendDate = $schedule->send_date?->toDateString();
                    if (!$sendDate || $sendDate !== $today) {
                        continue;
                    }
                } else {
                    $workingDays = $this->parseWorkingDays((string) ($schedule->working_days ?? ''));
                    $todayKey = strtolower($nowLocal->format('D')); // mon,tue,wed...
                    if (!in_array($todayKey, $workingDays, true)) {
                        continue;
                    }
                }
            }

            $scheduledLocal = Carbon::createFromFormat('Y-m-d H:i', "{$today} {$sendTime}", $timezone);
            if (!$forceSend && $nowLocal->lt($scheduledLocal)) {
                continue;
            }

            if (!$forceSend && $schedule->last_sent_date?->toDateString() === $today) {
                continue;
            }

            $clientEmail = strtolower(trim((string) $schedule->client_email));
            if ($clientEmail === '' || !filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
                Log::warning('Daily activity schedule skipped: invalid client email', [
                    'client_email' => $schedule->client_email,
                ]);
                continue;
            }

            $logs = TimeLog::with('project')
                ->completed()
                ->whereBetween('clock_in', [$startUtc, $endUtc])
                ->whereHas('project', function ($query) use ($clientEmail) {
                    $query->whereRaw('LOWER(client_email) = ?', [$clientEmail]);
                })
                ->orderBy('clock_in', 'asc')
                ->get();

            $summary = $this->buildSummary($logs);
            $reportDate = Carbon::parse($today, $timezone)->format('M d, Y');
            $clientName = trim((string) ($schedule->client_name ?? ''));

            $subject = $this->resolvePerClientSubject(
                $schedule->subject,
                $reportDate,
                $clientName,
                $clientEmail
            );
            $activityColumns = $this->parseActivityColumns((string) ($schedule->activity_columns ?? ''));

            try {
                Mail::mailer($mailerConfig['mailer'])->send('emails.daily-activity-report', [
                    'reportDate' => $reportDate,
                    'timezone' => $timezone,
                    'summary' => $summary,
                    'logs' => $this->formatLogsForEmail($logs, $timezone),
                    'activityColumns' => $activityColumns,
                    'clientName' => $clientName !== '' ? $clientName : null,
                    'clientEmail' => $clientEmail,
                ], function ($message) use ($clientEmail, $subject, $mailerConfig): void {
                    $message->to([$clientEmail])
                        ->subject($subject);

                    if ($mailerConfig['from_address']) {
                        $message->from(
                            $mailerConfig['from_address'],
                            $mailerConfig['from_name'] ?: $mailerConfig['from_address']
                        );
                    }
                });

                $schedule->last_sent_date = $today;
                $schedule->save();
                $sentCount++;

                Log::info('Daily activity report sent (per-client)', [
                    'date' => $today,
                    'timezone' => $timezone,
                    'client_email' => $clientEmail,
                    'schedule_type' => $scheduleType,
                    'total_sessions' => $summary['total_sessions'],
                    'total_minutes' => $summary['total_minutes'],
                    'forced' => $forceSend,
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to send daily activity report (per-client)', [
                    'date' => $today,
                    'timezone' => $timezone,
                    'client_email' => $clientEmail,
                    'error' => $e->getMessage(),
                    'forced' => $forceSend,
                ]);
            }
        }

        $this->info("Daily activity per-client scheduler processed. Sent: {$sentCount}.");

        return self::SUCCESS;
    }

    private function handleLegacyGlobalSchedule(): int
    {
        $timezone = config('app.timezone', 'UTC');
        $nowLocal = Carbon::now($timezone);
        $today = $nowLocal->toDateString();
        $forceSend = (bool) $this->option('force');

        if (!$this->isReportEnabled()) {
            $this->line('Daily activity report is disabled.');
            return self::SUCCESS;
        }

        $recipients = $this->parseRecipients((string) Setting::getValue('daily_activity_email_recipients', ''));
        if (empty($recipients)) {
            $this->warn('No daily activity report recipients configured.');
            return self::SUCCESS;
        }

        $sendTime = (string) Setting::getValue('daily_activity_email_send_time', '18:00');
        if (!preg_match('/^([01]\\d|2[0-3]):[0-5]\\d$/', $sendTime)) {
            $this->warn('Invalid daily activity send time. Expected HH:MM.');
            return self::SUCCESS;
        }

        $scheduledLocal = Carbon::createFromFormat('Y-m-d H:i', "{$today} {$sendTime}", $timezone);
        if (!$forceSend && $nowLocal->lt($scheduledLocal)) {
            $this->line('Scheduled send time has not been reached yet.');
            return self::SUCCESS;
        }

        $lastSentDate = (string) Setting::getValue('daily_activity_email_last_sent_date', '');
        if (!$forceSend && $lastSentDate === $today) {
            $this->line('Daily activity report already sent for today.');
            return self::SUCCESS;
        }

        [$startUtc, $endUtc] = $this->resolveDayUtcRange($today, $timezone);

        $logs = TimeLog::with('project')
            ->completed()
            ->whereBetween('clock_in', [$startUtc, $endUtc])
            ->orderBy('clock_in', 'asc')
            ->get();

        $summary = $this->buildSummary($logs);

        $subject = sprintf(
            'Daily Activity Report - %s',
            Carbon::parse($today, $timezone)->format('M d, Y')
        );

        try {
            $mailerConfig = $this->prepareMailerConfiguration($this->getEmailSettings());

            Mail::mailer($mailerConfig['mailer'])->send('emails.daily-activity-report', [
                'reportDate' => Carbon::parse($today, $timezone)->format('M d, Y'),
                'timezone' => $timezone,
                'summary' => $summary,
                'logs' => $this->formatLogsForEmail($logs, $timezone),
                'activityColumns' => ['project', 'clock_in', 'clock_out', 'duration', 'description'],
            ], function ($message) use ($recipients, $subject, $mailerConfig): void {
                $message->to($recipients)
                    ->subject($subject);

                if ($mailerConfig['from_address']) {
                    $message->from(
                        $mailerConfig['from_address'],
                        $mailerConfig['from_name'] ?: $mailerConfig['from_address']
                    );
                }
            });

            Setting::setValue('daily_activity_email_last_sent_date', $today);

            Log::info('Daily activity report sent', [
                'date' => $today,
                'timezone' => $timezone,
                'recipients' => $recipients,
                'total_sessions' => $summary['total_sessions'],
                'total_minutes' => $summary['total_minutes'],
                'forced' => $forceSend,
            ]);

            $this->info('Daily activity report sent successfully.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('Failed to send daily activity report', [
                'date' => $today,
                'timezone' => $timezone,
                'error' => $e->getMessage(),
            ]);

            $this->error('Failed to send daily activity report: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function isReportEnabled(): bool
    {
        $value = Setting::getValue('daily_activity_email_enabled', '0');

        return $value === true || $value === 1 || $value === '1' || $value === 'true';
    }

    private function resolveDayUtcRange(string $day, string $timezone): array
    {
        $startUtc = Carbon::parse($day, $timezone)->startOfDay()->setTimezone('UTC');
        $endUtc = Carbon::parse($day, $timezone)->endOfDay()->setTimezone('UTC');

        return [$startUtc, $endUtc];
    }

    private function parseRecipients(string $raw): array
    {
        if ($raw === '') {
            return [];
        }

        $items = preg_split('/[;,]+/', $raw) ?: [];
        $valid = [];

        foreach ($items as $item) {
            $email = trim($item);
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $valid[] = $email;
            }
        }

        return array_values(array_unique($valid));
    }

    private function buildSummary(Collection $logs): array
    {
        $totalMinutes = (int) $logs->sum('total_minutes');
        $projects = $logs->groupBy(function ($log) {
            return $log->project?->name ?? 'No Project';
        })->map(function ($projectLogs, $projectName) {
            return [
                'project_name' => $projectName,
                'sessions' => $projectLogs->count(),
                'minutes' => (int) $projectLogs->sum('total_minutes'),
            ];
        })->values()->all();

        return [
            'total_sessions' => $logs->count(),
            'total_minutes' => $totalMinutes,
            'total_hours_decimal' => round($totalMinutes / 60, 2),
            'projects' => $projects,
        ];
    }

    private function formatLogsForEmail(Collection $logs, string $timezone): array
    {
        return $logs->map(function ($log) use ($timezone) {
            $clockInRaw = $log->getRawOriginal('clock_in');
            $clockOutRaw = $log->getRawOriginal('clock_out');

            $clockInLocal = $clockInRaw
                ? Carbon::parse($clockInRaw, 'UTC')->setTimezone($timezone)
                : null;
            $clockOutLocal = $clockOutRaw
                ? Carbon::parse($clockOutRaw, 'UTC')->setTimezone($timezone)
                : null;

            return [
                'date' => $clockInLocal?->format('D, M j, Y') ?? '-',
                'project' => $log->project?->name ?? 'No Project',
                'clock_in' => $clockInLocal?->format('h:i A') ?? '-',
                'clock_out' => $clockOutLocal?->format('h:i A') ?? '-',
                'duration' => $this->formatMinutes((int) ($log->total_minutes ?? 0)),
                'description_text' => $this->normalizeDescriptionForEmail((string) ($log->work_description ?: '')),
            ];
        })->all();
    }

    private function normalizeDescriptionForEmail(string $value): string
    {
        $raw = trim($value);
        if ($raw === '') {
            return '-';
        }

        $withLineBreaks = preg_replace('/<br\s*\/?>/i', "\n", $raw) ?? $raw;
        $plain = strip_tags($withLineBreaks);
        $decoded = html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $normalized = preg_replace("/\r\n|\r/", "\n", $decoded) ?? $decoded;

        return trim($normalized) !== '' ? trim($normalized) : '-';
    }

    private function formatMinutes(int $minutes): string
    {
        $hours = intdiv(max($minutes, 0), 60);
        $mins = max($minutes, 0) % 60;

        return sprintf('%d:%02d', $hours, $mins);
    }

    private function getEmailSettings(): array
    {
        return Setting::getValues([
            'email_mailer',
            'email_smtp_host',
            'email_smtp_port',
            'email_smtp_username',
            'email_smtp_password',
            'email_smtp_encryption',
            'email_from_address',
            'email_from_name',
        ], [
            'email_mailer' => 'default',
            'email_smtp_host' => null,
            'email_smtp_port' => null,
            'email_smtp_username' => null,
            'email_smtp_password' => null,
            'email_smtp_encryption' => null,
            'email_from_address' => config('mail.from.address'),
            'email_from_name' => config('mail.from.name'),
        ]);
    }

    private function prepareMailerConfiguration(array $emailSettings): array
    {
        $mailer = config('mail.default');
        $selectedMailer = $emailSettings['email_mailer'] ?? 'default';

        if ($selectedMailer === 'smtp' && !empty($emailSettings['email_smtp_host'])) {
            $dynamicMailer = 'settings_smtp';
            Config::set("mail.mailers.{$dynamicMailer}", [
                'transport' => 'smtp',
                'host' => $emailSettings['email_smtp_host'],
                'port' => (int) ($emailSettings['email_smtp_port'] ?? 587),
                'encryption' => $emailSettings['email_smtp_encryption'] ?: null,
                'username' => $emailSettings['email_smtp_username'],
                'password' => $emailSettings['email_smtp_password'],
                'timeout' => null,
                'auth_mode' => null,
            ]);

            $mailer = $dynamicMailer;
        } elseif ($selectedMailer === 'mail') {
            $mailer = 'sendmail';
        }

        return [
            'mailer' => $mailer,
            'from_address' => $emailSettings['email_from_address'] ?? config('mail.from.address'),
            'from_name' => $emailSettings['email_from_name'] ?? config('mail.from.name'),
        ];
    }

    private function resolvePerClientSubject(?string $template, string $reportDate, string $clientName, string $clientEmail): string
    {
        $rawTemplate = trim((string) $template);
        if ($rawTemplate === '') {
            return sprintf(
                'Daily Activity Report - %s%s',
                $reportDate,
                $clientName !== '' ? " ({$clientName})" : ''
            );
        }

        return strtr($rawTemplate, [
            '{date}' => $reportDate,
            '{client_name}' => $clientName !== '' ? $clientName : 'Client',
            '{client_email}' => $clientEmail,
        ]);
    }

    private function parseActivityColumns(string $raw): array
    {
        $allowed = ['summary_sessions', 'summary_hours', 'date', 'project', 'clock_in', 'clock_out', 'duration', 'description'];
        $items = preg_split('/[,\s;]+/', strtolower($raw)) ?: [];
        $result = [];

        foreach ($items as $item) {
            $key = trim($item);
            if ($key !== '' && in_array($key, $allowed, true)) {
                $result[] = $key;
            }
        }

        $result = array_values(array_unique($result));

        return !empty($result) ? $result : $allowed;
    }

    private function parseWorkingDays(string $raw): array
    {
        $allowed = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        $items = preg_split('/[,\s;]+/', strtolower($raw)) ?: [];
        $result = [];

        foreach ($items as $item) {
            $key = trim($item);
            if ($key !== '' && in_array($key, $allowed, true)) {
                $result[] = $key;
            }
        }

        $result = array_values(array_unique($result));

        return !empty($result) ? $result : ['mon', 'tue', 'wed', 'thu', 'fri'];
    }
}

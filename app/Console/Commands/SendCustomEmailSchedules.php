<?php

namespace App\Console\Commands;

use App\Models\CustomEmailSchedule;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Services\SchedulerLogService;

class SendCustomEmailSchedules extends Command
{
    /**
     * Minutes to wait before each retry after a failed send. The command runs every
     * minute, so without this spacing a failure retries ~1,440 times a day. The count
     * of entries also caps attempts: 3 delays = 4 attempts max per day.
     */
    private const RETRY_BACKOFF_MINUTES = [5, 15, 30];

    protected $signature = 'emails:send-custom-scheduled {--force : Ignore time and last-sent checks}';

    protected $description = 'Send scheduled custom emails';

    public function handle(): int
    {
        if (!Schema::hasTable('custom_email_schedules')) {
            $this->line('Custom email schedules table not found.');
            return self::SUCCESS;
        }

        $timezone = config('app.timezone', 'UTC');
        $nowLocal = Carbon::now($timezone);
        $today = $nowLocal->toDateString();
        $forceSend = (bool) $this->option('force');

        $schedules = CustomEmailSchedule::query()
            ->where('enabled', true)
            ->where('status', 'scheduled')
            ->orderBy('created_at')
            ->get();

        if ($schedules->isEmpty()) {
            $this->line('No scheduled custom emails.');
            return self::SUCCESS;
        }

        $mailerConfig = $this->prepareMailerConfiguration($this->getEmailSettings());
        $sentCount = 0;

        foreach ($schedules as $schedule) {
            $recipients = array_values(array_filter($schedule->recipients ?? []));
            if (empty($recipients)) {
                Log::warning('Custom email schedule skipped: no recipients', [
                    'schedule_id' => $schedule->id,
                ]);
                SchedulerLogService::record([
                    'source' => 'custom_email',
                    'type' => 'Custom Email',
                    'name' => $schedule->name ?: 'Unnamed',
                    'status' => 'skipped',
                    'detail' => 'No recipients configured',
                    'payload' => ['schedule_id' => $schedule->id],
                ]);
                continue;
            }

            $sendTime = (string) ($schedule->send_time ?? '09:00');
            if (!preg_match('/^([01]\\d|2[0-3]):[0-5]\\d$/', $sendTime)) {
                Log::warning('Custom email schedule skipped: invalid send time', [
                    'schedule_id' => $schedule->id,
                    'send_time' => $sendTime,
                ]);
                SchedulerLogService::record([
                    'source' => 'custom_email',
                    'type' => 'Custom Email',
                    'name' => $schedule->name ?: 'Unnamed',
                    'status' => 'skipped',
                    'detail' => 'Invalid send time',
                    'payload' => ['schedule_id' => $schedule->id, 'send_time' => $sendTime],
                ]);
                continue;
            }

            $scheduleType = strtolower(trim((string) ($schedule->schedule_type ?? 'date')));
            if (!in_array($scheduleType, ['daily', 'date'], true)) {
                $scheduleType = 'date';
            }

            if (!$forceSend) {
                if ($scheduleType === 'date') {
                    $sendDate = $schedule->send_date?->toDateString();
                    if (!$sendDate) {
                        continue;
                    }

                    $scheduledLocal = Carbon::createFromFormat('Y-m-d H:i', "{$sendDate} {$sendTime}", $timezone);
                    if ($nowLocal->lt($scheduledLocal)) {
                        continue;
                    }
                } else {
                    $workingDays = $this->parseWorkingDays((string) ($schedule->working_days ?? ''));
                    $todayKey = strtolower($nowLocal->format('D'));
                    if (!in_array($todayKey, $workingDays, true)) {
                        continue;
                    }

                    $scheduledLocal = Carbon::createFromFormat('Y-m-d H:i', "{$today} {$sendTime}", $timezone);
                    if ($nowLocal->lt($scheduledLocal)) {
                        continue;
                    }

                    if ($schedule->last_sent_date?->toDateString() === $today) {
                        continue;
                    }
                }
            }

            $lastAttempt = $schedule->last_attempt_at
                ? Carbon::parse($schedule->last_attempt_at)->setTimezone($timezone)
                : null;
            $attemptsToday = $lastAttempt?->toDateString() === $today
                ? (int) $schedule->failed_attempts
                : 0;

            if (!$forceSend && !$this->mayAttemptNow($attemptsToday, $lastAttempt, $nowLocal)) {
                continue;
            }

            try {
                Mail::mailer($mailerConfig['mailer'])->send([
                    'html' => 'emails.custom-scheduled',
                    'text' => 'emails.custom-scheduled-text',
                ], [
                    'name' => $schedule->name,
                    'subject' => $schedule->subject,
                    'body' => $schedule->body,
                    'body_html' => $this->sanitizeEmailBodyHtml((string) ($schedule->body ?? '')),
                ], function ($message) use ($schedule, $recipients, $mailerConfig): void {
                    $message->to($recipients)->subject($schedule->subject);

                    if ($mailerConfig['from_address']) {
                        $message->from(
                            $mailerConfig['from_address'],
                            $mailerConfig['from_name'] ?: $mailerConfig['from_address']
                        );
                    }
                });

                if ($scheduleType === 'date') {
                    $schedule->status = 'sent';
                    $schedule->enabled = false;
                    $schedule->sent_at = Carbon::now();
                } else {
                    $schedule->last_sent_date = $today;
                }

                $schedule->last_attempt_at = Carbon::now();
                $schedule->failed_attempts = 0;
                $schedule->save();
                $sentCount++;

                Log::info('Custom scheduled email sent', [
                    'schedule_id' => $schedule->id,
                    'recipients' => $recipients,
                    'schedule_type' => $scheduleType,
                    'forced' => $forceSend,
                ]);
                $scheduledDate = $scheduleType === 'daily'
                    ? $today
                    : ($schedule->send_date?->toDateString() ?? $today);
                SchedulerLogService::record([
                    'source' => 'custom_email',
                    'type' => 'Custom Email',
                    'name' => $schedule->name ?: 'Unnamed',
                    'status' => 'sent',
                    'detail' => 'Email dispatched',
                    'scheduled_at' => Carbon::createFromFormat('Y-m-d H:i', "{$scheduledDate} {$sendTime}", $timezone),
                    'executed_at' => Carbon::now(),
                    'payload' => ['schedule_id' => $schedule->id, 'recipients' => $recipients],
                ]);
            } catch (\Throwable $e) {
                $attemptsToday++;
                $schedule->last_attempt_at = Carbon::now();
                $schedule->failed_attempts = $attemptsToday;
                $schedule->save();

                $nextRetry = $this->nextRetryDelayMinutes($attemptsToday);
                $givingUp = $nextRetry === null;

                Log::error('Failed to send custom scheduled email', [
                    'schedule_id' => $schedule->id,
                    'error' => $e->getMessage(),
                    'attempt' => $attemptsToday,
                    'max_attempts' => $this->maxAttempts(),
                    'retry_in_minutes' => $nextRetry,
                    'giving_up_for_today' => $givingUp,
                ]);
                SchedulerLogService::record([
                    'source' => 'custom_email',
                    'type' => 'Custom Email',
                    'name' => $schedule->name ?: 'Unnamed',
                    'status' => 'error',
                    'detail' => $givingUp
                        ? sprintf('%s (attempt %d/%d - no further attempts today)', $e->getMessage(), $attemptsToday, $this->maxAttempts())
                        : sprintf('%s (attempt %d/%d - retrying in %d min)', $e->getMessage(), $attemptsToday, $this->maxAttempts(), $nextRetry),
                    'executed_at' => Carbon::now(),
                    'payload' => [
                        'schedule_id' => $schedule->id,
                        'attempt' => $attemptsToday,
                        'max_attempts' => $this->maxAttempts(),
                        'retry_in_minutes' => $nextRetry,
                    ],
                ]);
            }
        }

        $this->info("Custom email scheduler processed. Sent: {$sentCount}.");

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function maxAttempts(): int
    {
        return count(self::RETRY_BACKOFF_MINUTES) + 1;
    }

    /**
     * Minutes until the next retry, or null when the attempt budget is spent.
     */
    private function nextRetryDelayMinutes(int $attemptsMade): ?int
    {
        return self::RETRY_BACKOFF_MINUTES[$attemptsMade - 1] ?? null;
    }

    private function mayAttemptNow(int $attemptsToday, ?Carbon $lastAttempt, Carbon $now): bool
    {
        if ($attemptsToday === 0) {
            return true;
        }

        $delay = $this->nextRetryDelayMinutes($attemptsToday);
        if ($delay === null) {
            return false;
        }

        return $lastAttempt === null || $now->gte($lastAttempt->copy()->addMinutes($delay));
    }

    private function parseWorkingDays(string $raw): array
    {
        $allowed = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

        $values = collect(preg_split('/[,\s;]+/', $raw))
            ->map(fn ($item) => strtolower(trim((string) $item)))
            ->filter(fn ($item) => $item !== '' && in_array($item, $allowed, true))
            ->unique()
            ->values()
            ->all();

        return !empty($values) ? $values : ['mon', 'tue', 'wed', 'thu', 'fri'];
    }

    private function getEmailSettings(): array
    {
        return Setting::whereIn('key', [
            'email_mailer',
            'email_from_address',
            'email_from_name',
            'email_smtp_host',
            'email_smtp_port',
            'email_smtp_username',
            'email_smtp_password',
            'email_smtp_encryption',
        ])->pluck('value', 'key')->toArray();
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

    private function sanitizeEmailBodyHtml(string $value): string
    {
        $raw = trim($value);
        if ($raw === '') {
            return '';
        }

        $withoutScripts = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', '', $raw) ?? $raw;
        $withoutComments = preg_replace('/<!--.*?-->/s', '', $withoutScripts) ?? $withoutScripts;
        $allowed = '<p><br><strong><b><em><i><u><ul><ol><li><blockquote>';
        $stripped = strip_tags($withoutComments, $allowed);
        $clean = preg_replace('/<([a-z0-9]+)(?:\s[^>]*)?>/i', '<$1>', $stripped) ?? $stripped;
        $clean = preg_replace("/\r\n|\r/", "\n", $clean) ?? $clean;

        return trim($clean);
    }
}

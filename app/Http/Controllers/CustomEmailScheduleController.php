<?php

namespace App\Http\Controllers;

use App\Models\CustomEmailSchedule;
use App\Models\DailyActivitySchedule;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class CustomEmailScheduleController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('custom_email_schedules')) {
            $invoiceSchedules = $this->getInvoiceSchedules();
            return response()->json([
                'success' => true,
                'data' => [
                    'schedules' => [],
                    'invoice_schedules' => $invoiceSchedules,
                    'upcoming_schedules' => $this->getUpcomingSchedules(),
                    'suggested_recipients' => $this->getSuggestedRecipients(),
                ],
            ]);
        }

        $schedules = CustomEmailSchedule::query()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (CustomEmailSchedule $schedule) => $this->formatSchedule($schedule))
            ->values();

        $invoiceSchedules = $this->getInvoiceSchedules();

        return response()->json([
            'success' => true,
            'data' => [
                'schedules' => $schedules,
                'invoice_schedules' => $invoiceSchedules,
                'upcoming_schedules' => $this->getUpcomingSchedules(),
                'suggested_recipients' => $this->getSuggestedRecipients(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('custom_email_schedules')) {
            return response()->json([
                'success' => false,
                'message' => 'Custom email schedules table is missing. Run migrations first.',
            ], 422);
        }

        $validated = $this->validateSchedulePayload($request);
        $recipients = $this->normalizeRecipients($validated['recipients'] ?? '');

        if (empty($recipients)) {
            throw ValidationException::withMessages([
                'recipients' => 'Provide at least one valid email address.',
            ]);
        }

        $scheduleType = $validated['schedule_type'] ?? 'date';
        $sendDate = $scheduleType === 'date' ? ($validated['send_date'] ?? null) : null;
        if ($scheduleType === 'date' && empty($sendDate)) {
            throw ValidationException::withMessages([
                'send_date' => 'Date is required for one-time schedules.',
            ]);
        }

        $schedule = CustomEmailSchedule::create([
            'name' => $validated['name'] ?? null,
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'recipients' => $recipients,
            'schedule_type' => $scheduleType,
            'send_time' => $validated['send_time'] ?? '09:00',
            'send_date' => $sendDate,
            'working_days' => $scheduleType === 'daily'
                ? ($validated['working_days'] ?? 'mon,tue,wed,thu,fri')
                : null,
            'enabled' => array_key_exists('enabled', $validated) ? (bool) $validated['enabled'] : true,
            'status' => 'scheduled',
            'last_sent_date' => null,
            'sent_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Custom email scheduled.',
            'data' => $this->formatSchedule($schedule),
        ]);
    }

    public function update(Request $request, CustomEmailSchedule $schedule)
    {
        if ($schedule->status !== 'scheduled') {
            return response()->json([
                'success' => false,
                'message' => 'Only scheduled emails can be edited.',
            ], 422);
        }

        $validated = $this->validateSchedulePayload($request, true);
        $recipients = array_key_exists('recipients', $validated)
            ? $this->normalizeRecipients($validated['recipients'])
            : $schedule->recipients;

        if (empty($recipients)) {
            throw ValidationException::withMessages([
                'recipients' => 'Provide at least one valid email address.',
            ]);
        }

        $scheduleType = $validated['schedule_type'] ?? $schedule->schedule_type ?? 'date';
        $sendDate = $scheduleType === 'date' ? ($validated['send_date'] ?? $schedule->send_date?->toDateString()) : null;
        if ($scheduleType === 'date' && empty($sendDate)) {
            throw ValidationException::withMessages([
                'send_date' => 'Date is required for one-time schedules.',
            ]);
        }

        $sendTime = $validated['send_time'] ?? $schedule->send_time ?? '09:00';
        $workingDays = $scheduleType === 'daily'
            ? ($validated['working_days'] ?? $schedule->working_days ?? 'mon,tue,wed,thu,fri')
            : null;

        $schedule->fill([
            'name' => $validated['name'] ?? $schedule->name,
            'subject' => $validated['subject'] ?? $schedule->subject,
            'body' => $validated['body'] ?? $schedule->body,
            'recipients' => $recipients,
            'schedule_type' => $scheduleType,
            'send_time' => $sendTime,
            'send_date' => $sendDate,
            'working_days' => $workingDays,
            'enabled' => array_key_exists('enabled', $validated) ? (bool) $validated['enabled'] : $schedule->enabled,
            'status' => 'scheduled',
        ]);

        $schedule->last_sent_date = null;
        $schedule->sent_at = null;
        $schedule->save();

        return response()->json([
            'success' => true,
            'message' => 'Custom email updated.',
            'data' => $this->formatSchedule($schedule),
        ]);
    }

    public function cancel(CustomEmailSchedule $schedule)
    {
        if ($schedule->status === 'cancelled') {
            return response()->json([
                'success' => true,
                'message' => 'Email schedule already cancelled.',
                'data' => $this->formatSchedule($schedule),
            ]);
        }

        $schedule->status = 'cancelled';
        $schedule->enabled = false;
        $schedule->save();

        return response()->json([
            'success' => true,
            'message' => 'Email schedule cancelled.',
            'data' => $this->formatSchedule($schedule),
        ]);
    }

    public function destroy(CustomEmailSchedule $schedule)
    {
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Email schedule deleted.',
        ]);
    }

    private function validateSchedulePayload(Request $request, bool $partial = false): array
    {
        $rules = [
            'name' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
            'recipients' => 'required',
            'schedule_type' => 'nullable|in:daily,date',
            'send_time' => ['nullable', 'string', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'send_date' => 'nullable|date',
            'working_days' => 'nullable|string|max:255',
            'enabled' => 'nullable|boolean',
        ];

        if ($partial) {
            foreach ($rules as $key => $rule) {
                if (is_array($rule)) {
                    $rules[$key] = array_map(function ($item) {
                        return $item === 'required' ? 'nullable' : $item;
                    }, $rule);
                } else {
                    $rules[$key] = str_replace('required', 'nullable', $rule);
                }
            }
        }

        return $request->validate($rules);
    }

    /**
     * @param mixed $input
     * @return array<int, string>
     */
    private function normalizeRecipients($input): array
    {
        $raw = is_array($input) ? $input : preg_split('/[,\s;]+/', (string) $input);
        $emails = collect($raw)
            ->map(fn ($item) => strtolower(trim((string) $item)))
            ->filter(fn ($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();

        return $emails;
    }

    private function formatSchedule(CustomEmailSchedule $schedule): array
    {
        return [
            'id' => $schedule->id,
            'name' => $schedule->name,
            'subject' => $schedule->subject,
            'body' => $schedule->body,
            'recipients' => $schedule->recipients ?? [],
            'schedule_type' => $schedule->schedule_type,
            'send_time' => $schedule->send_time,
            'send_date' => $schedule->send_date?->toDateString(),
            'working_days' => $schedule->working_days,
            'enabled' => (bool) $schedule->enabled,
            'status' => $schedule->status,
            'last_sent_date' => $schedule->last_sent_date?->toDateString(),
            'sent_at' => $schedule->sent_at?->toDateTimeString(),
            'created_at' => $schedule->created_at?->toDateTimeString(),
            'updated_at' => $schedule->updated_at?->toDateTimeString(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getInvoiceSchedules(): array
    {
        if (!Schema::hasTable('invoices')) {
            return [];
        }

        $invoices = Invoice::query()
            ->where(function ($query) {
                $query->whereNotNull('scheduled_send_at')
                    ->orWhereNotNull('reminder_send_at');
            })
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->orderByDesc('updated_at')
            ->get();

        $schedules = [];

        foreach ($invoices as $invoice) {
            if ($invoice->scheduled_send_at) {
                $schedules[] = $this->formatInvoiceSchedule($invoice, 'send', $invoice->scheduled_send_at);
            }
            if ($invoice->reminder_send_at) {
                $schedules[] = $this->formatInvoiceSchedule($invoice, 'reminder', $invoice->reminder_send_at);
            }
        }

        return $schedules;
    }

    private function formatInvoiceSchedule(Invoice $invoice, string $kind, Carbon $sendAt): array
    {
        $label = $kind === 'reminder' ? 'Reminder' : 'Send';
        $subject = $kind === 'reminder'
            ? "Payment Reminder: Invoice {$invoice->invoice_number}"
            : "Invoice {$invoice->invoice_number}";

        return [
            'id' => "invoice-{$kind}-{$invoice->id}",
            'source' => 'invoice',
            'invoice_id' => $invoice->id,
            'schedule_kind' => $kind,
            'name' => "Invoice {$invoice->invoice_number} • {$label}",
            'subject' => $subject,
            'body' => $kind === 'reminder'
                ? 'Scheduled payment reminder email with invoice PDF attached.'
                : 'Scheduled invoice email with PDF attached.',
            'recipients' => array_values(array_filter([$invoice->client_email])),
            'schedule_type' => 'date',
            'send_time' => $sendAt->format('H:i'),
            'send_date' => $sendAt->toDateString(),
            'working_days' => null,
            'enabled' => true,
            'status' => 'scheduled',
            'last_sent_date' => null,
            'sent_at' => null,
            'created_at' => $invoice->updated_at?->toDateTimeString(),
            'updated_at' => $invoice->updated_at?->toDateTimeString(),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function getSuggestedRecipients(): array
    {
        if (!Schema::hasTable('projects')) {
            return [];
        }

        return Project::query()
            ->whereNotNull('client_email')
            ->where('client_email', '!=', '')
            ->selectRaw('LOWER(client_email) as email')
            ->distinct()
            ->orderBy('email')
            ->limit(50)
            ->pluck('email')
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getUpcomingSchedules(): array
    {
        $timezone = config('app.timezone', 'UTC');
        $now = Carbon::now($timezone);

        $items = array_merge(
            $this->collectUpcomingInvoiceSchedules($now, $timezone),
            $this->collectUpcomingCustomEmailSchedules($now, $timezone),
            $this->collectUpcomingDailyActivitySchedules($now, $timezone),
        );

        usort($items, static function (array $a, array $b): int {
            return strcmp((string) ($a['run_at'] ?? ''), (string) ($b['run_at'] ?? ''));
        });

        return array_slice($items, 0, 100);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function collectUpcomingInvoiceSchedules(Carbon $now, string $timezone): array
    {
        if (!Schema::hasTable('invoices')) {
            return [];
        }

        $invoices = Invoice::query()
            ->where(function ($query) {
                $query->whereNotNull('scheduled_send_at')
                    ->orWhereNotNull('reminder_send_at');
            })
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->orderBy('id')
            ->get();

        $items = [];

        foreach ($invoices as $invoice) {
            if ($invoice->scheduled_send_at) {
                $runAt = $invoice->scheduled_send_at->copy()->setTimezone($timezone);
                if ($runAt->gte($now)) {
                    $items[] = $this->buildUpcomingItem(
                        id: "invoice-send-{$invoice->id}",
                        source: 'invoice',
                        type: 'Invoice Send',
                        name: "Invoice {$invoice->invoice_number}",
                        recipients: array_values(array_filter([$invoice->client_email])),
                        runAtLocal: $runAt,
                        detail: 'Scheduled invoice email send'
                    );
                }
            }

            if ($invoice->reminder_send_at) {
                $runAt = $invoice->reminder_send_at->copy()->setTimezone($timezone);
                if ($runAt->gte($now)) {
                    $items[] = $this->buildUpcomingItem(
                        id: "invoice-reminder-{$invoice->id}",
                        source: 'invoice',
                        type: 'Invoice Reminder',
                        name: "Invoice {$invoice->invoice_number}",
                        recipients: array_values(array_filter([$invoice->client_email])),
                        runAtLocal: $runAt,
                        detail: 'Scheduled payment reminder'
                    );
                }
            }
        }

        return $items;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function collectUpcomingCustomEmailSchedules(Carbon $now, string $timezone): array
    {
        if (!Schema::hasTable('custom_email_schedules')) {
            return [];
        }

        $schedules = CustomEmailSchedule::query()
            ->where('status', 'scheduled')
            ->where('enabled', true)
            ->orderBy('id')
            ->get();

        $items = [];

        foreach ($schedules as $schedule) {
            $scheduleType = strtolower(trim((string) ($schedule->schedule_type ?? 'date')));
            $sendTime = (string) ($schedule->send_time ?? '09:00');
            $runAt = null;
            $detail = '';

            if ($scheduleType === 'daily') {
                $workingDays = $this->parseWorkingDays((string) ($schedule->working_days ?? ''));
                $runAt = $this->nextRecurringRun(
                    now: $now,
                    sendTime: $sendTime,
                    workingDays: $workingDays,
                    timezone: $timezone,
                    lastSentDate: $schedule->last_sent_date
                );
                $detail = 'Daily • ' . strtoupper(implode(',', $workingDays));
            } else {
                $sendDate = $schedule->send_date?->toDateString();
                if ($sendDate) {
                    $runAt = Carbon::createFromFormat('Y-m-d H:i', "{$sendDate} {$sendTime}", $timezone);
                    if ($schedule->last_sent_date?->toDateString() === $sendDate) {
                        $runAt = null;
                    } elseif ($runAt->lt($now)) {
                        $runAt = null;
                    }
                }
                $detail = 'One-time date schedule';
            }

            if (!$runAt) {
                continue;
            }

            $items[] = $this->buildUpcomingItem(
                id: "custom-email-{$schedule->id}",
                source: 'custom_email',
                type: 'Custom Email',
                name: $schedule->name ?: 'Custom Scheduled Email',
                recipients: is_array($schedule->recipients) ? $schedule->recipients : [],
                runAtLocal: $runAt,
                detail: $detail
            );
        }

        return $items;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function collectUpcomingDailyActivitySchedules(Carbon $now, string $timezone): array
    {
        $items = [];

        if (Schema::hasTable('daily_activity_schedules') && DailyActivitySchedule::query()->exists()) {
            $schedules = DailyActivitySchedule::query()
                ->where('enabled', true)
                ->orderBy('client_email')
                ->get();

            foreach ($schedules as $schedule) {
                $scheduleType = strtolower(trim((string) ($schedule->schedule_type ?? 'daily')));
                $sendTime = (string) ($schedule->send_time ?? '18:00');
                $runAt = null;
                $detail = '';

                if ($scheduleType === 'date') {
                    $sendDate = $schedule->send_date?->toDateString();
                    if ($sendDate) {
                        $runAt = Carbon::createFromFormat('Y-m-d H:i', "{$sendDate} {$sendTime}", $timezone);
                        if ($schedule->last_sent_date?->toDateString() === $sendDate) {
                            $runAt = null;
                        } elseif ($runAt->lt($now)) {
                            $runAt = null;
                        }
                    }
                    $detail = 'Client daily activity • one-time';
                } else {
                    $workingDays = $this->parseWorkingDays((string) ($schedule->working_days ?? ''));
                    $runAt = $this->nextRecurringRun(
                        now: $now,
                        sendTime: $sendTime,
                        workingDays: $workingDays,
                        timezone: $timezone,
                        lastSentDate: $schedule->last_sent_date
                    );
                    $detail = 'Client daily activity • recurring';
                }

                if (!$runAt) {
                    continue;
                }

                $items[] = $this->buildUpcomingItem(
                    id: "daily-activity-{$schedule->id}",
                    source: 'daily_activity',
                    type: 'Daily Activity',
                    name: $schedule->client_name ?: ($schedule->client_email ?: 'Daily Activity Client'),
                    recipients: array_values(array_filter([$schedule->client_email])),
                    runAtLocal: $runAt,
                    detail: $detail
                );
            }

            return $items;
        }

        $enabled = Setting::getValue('daily_activity_email_enabled', '0');
        $isEnabled = $enabled === true || $enabled === 1 || $enabled === '1' || $enabled === 'true';
        if (!$isEnabled) {
            return $items;
        }

        $sendTime = (string) Setting::getValue('daily_activity_email_send_time', '18:00');
        $recipientsRaw = (string) Setting::getValue('daily_activity_email_recipients', '');
        $lastSentDate = (string) Setting::getValue('daily_activity_email_last_sent_date', '');
        $recipients = $this->normalizeRecipients($recipientsRaw);

        $runAt = $this->nextRecurringRun(
            now: $now,
            sendTime: $sendTime,
            workingDays: ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'],
            timezone: $timezone,
            lastSentDate: $lastSentDate !== '' ? Carbon::parse($lastSentDate, $timezone) : null
        );

        if ($runAt) {
            $items[] = $this->buildUpcomingItem(
                id: 'daily-activity-global',
                source: 'daily_activity',
                type: 'Daily Activity',
                name: 'Global Daily Activity Report',
                recipients: $recipients,
                runAtLocal: $runAt,
                detail: 'Global fallback daily activity schedule'
            );
        }

        return $items;
    }

    /**
     * @param array<int, string> $workingDays
     */
    private function nextRecurringRun(
        Carbon $now,
        string $sendTime,
        array $workingDays,
        string $timezone,
        ?Carbon $lastSentDate = null
    ): ?Carbon {
        if (!preg_match('/^([01]\\d|2[0-3]):[0-5]\\d$/', $sendTime)) {
            return null;
        }

        $normalizedDays = array_values(array_unique(array_map('strtolower', $workingDays)));
        if (empty($normalizedDays)) {
            $normalizedDays = ['mon', 'tue', 'wed', 'thu', 'fri'];
        }

        for ($i = 0; $i <= 30; $i++) {
            $date = $now->copy()->addDays($i);
            $dayKey = strtolower($date->format('D'));
            if (!in_array($dayKey, $normalizedDays, true)) {
                continue;
            }

            $candidate = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . $sendTime, $timezone);
            if ($candidate->lt($now)) {
                continue;
            }

            if ($lastSentDate && $lastSentDate->toDateString() === $candidate->toDateString()) {
                continue;
            }

            return $candidate;
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function parseWorkingDays(string $raw): array
    {
        $allowed = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        $parts = preg_split('/[,\s;]+/', strtolower(trim($raw))) ?: [];
        $days = [];

        foreach ($parts as $part) {
            if ($part !== '' && in_array($part, $allowed, true)) {
                $days[] = $part;
            }
        }

        $days = array_values(array_unique($days));

        return !empty($days) ? $days : ['mon', 'tue', 'wed', 'thu', 'fri'];
    }

    /**
     * @param array<int, string> $recipients
     * @return array<string, mixed>
     */
    private function buildUpcomingItem(
        string $id,
        string $source,
        string $type,
        string $name,
        array $recipients,
        Carbon $runAtLocal,
        string $detail
    ): array {
        return [
            'id' => $id,
            'source' => $source,
            'type' => $type,
            'name' => $name,
            'recipients' => array_values(array_unique(array_filter($recipients))),
            'run_at' => $runAtLocal->copy()->setTimezone('UTC')->toIso8601String(),
            'run_at_local' => $runAtLocal->format('Y-m-d H:i'),
            'detail' => $detail,
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\CustomEmailSchedule;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class CustomEmailScheduleController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('custom_email_schedules')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'schedules' => [],
                    'suggested_recipients' => $this->getSuggestedRecipients(),
                ],
            ]);
        }

        $schedules = CustomEmailSchedule::query()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (CustomEmailSchedule $schedule) => $this->formatSchedule($schedule))
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'schedules' => $schedules,
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
}

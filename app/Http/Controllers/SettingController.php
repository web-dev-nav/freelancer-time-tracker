<?php

namespace App\Http\Controllers;

use App\Models\DailyActivitySchedule;
use App\Models\Project;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SettingController extends Controller
{
    /**
     * Return application settings.
     */
    public function index()
    {
        $defaultActivityRecipients = $this->defaultActivityRecipients();

        $defaults = [
            'invoice_company_name'   => config('app.name'),
            'invoice_company_address'=> null,
            'invoice_tax_number'     => null,
            'payment_etransfer_email'=> null,
            'payment_bank_info'      => null,
            'payment_instructions'   => null,
            'email_mailer'           => 'default',
            'email_smtp_host'        => null,
            'email_smtp_port'        => null,
            'email_smtp_username'    => null,
            'email_smtp_password'    => null,
            'email_smtp_encryption'  => null,
            'email_from_address'     => config('mail.from.address'),
            'email_from_name'        => config('mail.from.name'),
            'stripe_enabled'         => false,
            'stripe_publishable_key' => null,
            'stripe_secret_key'      => null,
            'daily_activity_email_enabled' => false,
            'daily_activity_email_recipients' => $defaultActivityRecipients,
            'daily_activity_email_send_time' => '18:00',
            'daily_activity_email_last_sent_date' => null,
        ];

        try {
            $settings = Setting::getValues(array_keys($defaults), $defaults);
            $this->ensureActivityRecipientsDefault($settings, $defaultActivityRecipients);

            // SECURITY: Decrypt Stripe secret key if it exists
            if (!empty($settings['stripe_secret_key'])) {
                try {
                    $settings['stripe_secret_key'] = decrypt($settings['stripe_secret_key']);
                } catch (\Exception $e) {
                    // If decryption fails, value might not be encrypted (legacy data)
                    Log::warning('Failed to decrypt Stripe secret key', ['error' => $e->getMessage()]);
                }
            }

            // SECURITY: Never send secret key to frontend - mask it
            if (!empty($settings['stripe_secret_key'])) {
                $settings['stripe_secret_key'] = '••••••••' . substr($settings['stripe_secret_key'], -4);
            }

            $settings['daily_activity_client_schedules'] = $this->getClientSchedulesForAutomation();
        } catch (\Throwable $e) {
            report($e);
            $settings = $defaults;
            $settings['daily_activity_client_schedules'] = [];
        }

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Update application settings.
     */
    public function update(Request $request)
    {
        $defaultActivityRecipients = $this->defaultActivityRecipients();

        $defaults = [
            'invoice_company_name'   => config('app.name'),
            'invoice_company_address'=> null,
            'invoice_tax_number'     => null,
            'payment_etransfer_email'=> null,
            'payment_bank_info'      => null,
            'payment_instructions'   => null,
            'email_mailer'           => 'default',
            'email_smtp_host'        => null,
            'email_smtp_port'        => null,
            'email_smtp_username'    => null,
            'email_smtp_password'    => null,
            'email_smtp_encryption'  => null,
            'email_from_address'     => config('mail.from.address'),
            'email_from_name'        => config('mail.from.name'),
            'stripe_enabled'         => false,
            'stripe_publishable_key' => null,
            'stripe_secret_key'      => null,
            'daily_activity_email_enabled' => false,
            'daily_activity_email_recipients' => $defaultActivityRecipients,
            'daily_activity_email_send_time' => '18:00',
            'daily_activity_email_last_sent_date' => null,
        ];

        $keys = array_keys($defaults);

        try {
            $validated = $request->validate([
                'invoice_company_name'    => 'nullable|string|max:255',
                'invoice_company_address' => 'nullable|string|max:2000',
                'invoice_tax_number'      => 'nullable|string|max:255',
                'payment_etransfer_email' => 'nullable|email|max:255',
                'payment_bank_info'       => 'nullable|string|max:2000',
                'payment_instructions'    => 'nullable|string|max:2000',
                'email_mailer'            => 'nullable|in:default,mail,smtp',
                'email_smtp_host'         => 'nullable|string|max:255',
                'email_smtp_port'         => 'nullable|integer|min:1|max:65535',
                'email_smtp_username'     => 'nullable|string|max:255',
                'email_smtp_password'     => 'nullable|string|max:255',
                'email_smtp_encryption'   => 'nullable|in:tls,ssl,starttls',
                'email_from_address'      => 'nullable|email|max:255',
                'email_from_name'         => 'nullable|string|max:255',
                'stripe_enabled'          => 'nullable|boolean',
                'daily_activity_email_enabled' => 'nullable|boolean',
                'daily_activity_email_recipients' => 'nullable|string|max:2000',
                'daily_activity_email_send_time' => ['nullable', 'string', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
                'daily_activity_email_last_sent_date' => 'nullable|date',
                'daily_activity_client_schedules' => 'nullable|array',
                'daily_activity_client_schedules.*.client_email' => 'required_with:daily_activity_client_schedules|email|max:255',
                'daily_activity_client_schedules.*.client_name' => 'nullable|string|max:255',
                'daily_activity_client_schedules.*.enabled' => 'nullable|boolean',
                'daily_activity_client_schedules.*.schedule_type' => 'nullable|in:daily,date',
                'daily_activity_client_schedules.*.send_time' => ['nullable', 'string', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
                'daily_activity_client_schedules.*.send_date' => 'nullable|date',
                'daily_activity_client_schedules.*.working_days' => 'nullable|string|max:255',
                'daily_activity_client_schedules.*.subject' => 'nullable|string|max:255',
                'daily_activity_client_schedules.*.activity_columns' => 'nullable|string|max:255',
                // SECURITY: Validate Stripe key formats
                'stripe_publishable_key'  => ['nullable', 'string', 'max:255', 'regex:/^(pk_(test|live)_[a-zA-Z0-9]+)?$/'],
                'stripe_secret_key'       => ['nullable', 'string', 'max:255', 'regex:/^(sk_(test|live)_[a-zA-Z0-9]+)?$/'],
            ]);

            foreach ($keys as $key) {
                if (!array_key_exists($key, $validated)) {
                    continue;
                }

                $value = $validated[$key];

                if ($value === null) {
                    Setting::setValue($key, null);
                    continue;
                }

                // Handle boolean values (e.g., stripe_enabled)
                if (is_bool($value)) {
                    Setting::setValue($key, $value ? '1' : '0');

                    // SECURITY: Audit log for Stripe settings changes
                    if ($key === 'stripe_enabled') {
                        Log::info('Stripe payment integration ' . ($value ? 'enabled' : 'disabled'), [
                            'ip' => $request->ip(),
                            'user_agent' => $request->userAgent()
                        ]);
                    }
                    continue;
                }

                $sanitized = trim($value);
                $finalValue = $sanitized !== '' ? $sanitized : null;

                // SECURITY: Encrypt Stripe secret key before storing
                if ($key === 'stripe_secret_key' && $finalValue !== null) {
                    $finalValue = encrypt($finalValue);

                    // SECURITY: Audit log for secret key changes
                    Log::warning('Stripe secret key updated', [
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'key_preview' => 'sk_****' . substr($sanitized, -4)
                    ]);
                }

                // SECURITY: Audit log for publishable key changes
                if ($key === 'stripe_publishable_key' && $finalValue !== null) {
                    Log::info('Stripe publishable key updated', [
                        'ip' => $request->ip(),
                        'key_preview' => 'pk_****' . substr($sanitized, -4)
                    ]);
                }

                Setting::setValue($key, $finalValue);

                // Log email mailer changes
                if ($key === 'email_mailer') {
                    Log::info('Email mailer setting updated', [
                        'key' => $key,
                        'value' => $finalValue
                    ]);
                }
            }

            $this->syncDailyActivityClientSchedules($validated['daily_activity_client_schedules'] ?? []);

            $data = Setting::getValues($keys, $defaults);
            $this->ensureActivityRecipientsDefault($data, $defaultActivityRecipients);
            $data['daily_activity_client_schedules'] = $this->getClientSchedulesForAutomation();

            // SECURITY: Decrypt and mask secret key before returning
            if (!empty($data['stripe_secret_key'])) {
                try {
                    $decrypted = decrypt($data['stripe_secret_key']);
                    $data['stripe_secret_key'] = '••••••••' . substr($decrypted, -4);
                } catch (\Exception $e) {
                    $data['stripe_secret_key'] = '••••••••';
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Unable to save settings at the moment.',
                'data' => $defaults,
            ]);
        }
    }

    /**
     * Send a test email to verify email configuration.
     */
    public function testEmail(Request $request)
    {
        $validated = $request->validate([
            'test_email' => 'required|email',
            'email_mailer' => 'nullable|in:default,mail,smtp',
            'email_smtp_host' => 'nullable|string|max:255',
            'email_smtp_port' => 'nullable|integer|min:1|max:65535',
            'email_smtp_username' => 'nullable|string|max:255',
            'email_smtp_password' => 'nullable|string|max:255',
            'email_smtp_encryption' => 'nullable|in:tls,ssl,starttls',
            'email_from_address' => 'nullable|email|max:255',
            'email_from_name' => 'nullable|string|max:255',
        ]);

        try {
            $emailSettings = [
                'email_mailer' => $validated['email_mailer'] ?? 'default',
                'email_smtp_host' => $validated['email_smtp_host'] ?? null,
                'email_smtp_port' => $validated['email_smtp_port'] ?? null,
                'email_smtp_username' => $validated['email_smtp_username'] ?? null,
                'email_smtp_password' => $validated['email_smtp_password'] ?? null,
                'email_smtp_encryption' => $validated['email_smtp_encryption'] ?? null,
                'email_from_address' => $validated['email_from_address'] ?? config('mail.from.address'),
                'email_from_name' => $validated['email_from_name'] ?? config('mail.from.name'),
            ];

            Log::info('Test email settings received', [
                'email_mailer' => $emailSettings['email_mailer'],
                'from_address' => $emailSettings['email_from_address']
            ]);

            $mailerConfig = $this->prepareMailerConfiguration($emailSettings);

            // Send test email
            Mail::mailer($mailerConfig['mailer'])->send([], [], function ($message) use ($validated, $mailerConfig) {
                $message->to($validated['test_email'])
                    ->subject('Test Email from ' . config('app.name'))
                    ->html($this->getTestEmailHtml());

                if ($mailerConfig['from_address']) {
                    $message->from(
                        $mailerConfig['from_address'],
                        $mailerConfig['from_name'] ?: $mailerConfig['from_address']
                    );
                }
            });

            Log::info('Test email sent successfully', [
                'to' => $validated['test_email'],
                'mailer' => $mailerConfig['mailer'],
                'from' => $mailerConfig['from_address'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully! Check your inbox (and spam folder).',
            ]);

        } catch (\Exception $e) {
            Log::error('Test email failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Prepare mailer configuration.
     */
    protected function prepareMailerConfiguration(array $emailSettings): array
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

    /**
     * Derive default daily activity recipients from known client emails.
     */
    protected function defaultActivityRecipients(): ?string
    {
        try {
            $emails = Project::query()
                ->whereNotNull('client_email')
                ->where('client_email', '!=', '')
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->pluck('client_email')
                ->map(fn ($email) => trim((string) $email))
                ->filter(fn ($email) => $email !== '')
                ->unique()
                ->values()
                ->all();

            if (empty($emails)) {
                return null;
            }

            // Pre-fill with the most recently used client email.
            return $emails[0];
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * Guarantee daily activity recipients are prefilled from DB when blank.
     *
     * @param array<string, mixed> $settings
     */
    protected function ensureActivityRecipientsDefault(array &$settings, ?string $defaultRecipients): void
    {
        $currentRecipients = trim((string) ($settings['daily_activity_email_recipients'] ?? ''));

        if ($currentRecipients === '' && $defaultRecipients !== null) {
            $settings['daily_activity_email_recipients'] = $defaultRecipients;
        }
    }

    /**
     * Return distinct clients from projects merged with configured schedules.
     *
     * @return array<int, array{client_email: string, client_name: string|null, enabled: bool, schedule_type: string, send_time: string, send_date: string|null, working_days: string, subject: string|null, activity_columns: string, last_sent_date: string|null}>
     */
    protected function getClientSchedulesForAutomation(): array
    {
        if (!Schema::hasTable('daily_activity_schedules')) {
            return [];
        }
        $hasScheduleTypeColumn = Schema::hasColumn('daily_activity_schedules', 'schedule_type');
        $hasSendDateColumn = Schema::hasColumn('daily_activity_schedules', 'send_date');
        $hasWorkingDaysColumn = Schema::hasColumn('daily_activity_schedules', 'working_days');
        $hasSubjectColumn = Schema::hasColumn('daily_activity_schedules', 'subject');
        $hasActivityColumnsColumn = Schema::hasColumn('daily_activity_schedules', 'activity_columns');

        $profiles = $this->knownClientProfiles();
        $schedules = DailyActivitySchedule::query()
            ->orderBy('client_email')
            ->get();

        $scheduleMap = [];
        foreach ($schedules as $schedule) {
            $email = strtolower(trim((string) $schedule->client_email));
            if ($email === '') {
                continue;
            }

            $scheduleMap[$email] = $schedule;
        }

        $result = [];

        foreach ($profiles as $profile) {
            $email = strtolower(trim((string) ($profile['client_email'] ?? '')));
            $schedule = $email !== '' ? ($scheduleMap[$email] ?? null) : null;

            $result[] = [
                'client_email' => $profile['client_email'] ?? '',
                'client_name' => $profile['client_name'] ?? null,
                'enabled' => (bool) ($schedule?->enabled ?? false),
                'schedule_type' => $hasScheduleTypeColumn ? ((string) ($schedule?->schedule_type ?? 'daily')) : 'daily',
                'send_time' => $schedule?->send_time ?: '18:00',
                'send_date' => $hasSendDateColumn ? ($schedule?->send_date?->toDateString()) : null,
                'working_days' => $hasWorkingDaysColumn ? ((string) ($schedule?->working_days ?? 'mon,tue,wed,thu,fri')) : 'mon,tue,wed,thu,fri',
                'subject' => $hasSubjectColumn ? ($schedule?->subject) : null,
                'activity_columns' => $hasActivityColumnsColumn ? ((string) ($schedule?->activity_columns ?? '')) : 'date,project,clock_in,clock_out,duration,description',
                'last_sent_date' => $schedule?->last_sent_date?->toDateString(),
            ];
        }

        usort($result, function (array $a, array $b): int {
            return strcasecmp($a['client_name'] ?: $a['client_email'], $b['client_name'] ?: $b['client_email']);
        });

        return $result;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    protected function syncDailyActivityClientSchedules(array $rows): void
    {
        if (!Schema::hasTable('daily_activity_schedules')) {
            return;
        }
        $hasScheduleTypeColumn = Schema::hasColumn('daily_activity_schedules', 'schedule_type');
        $hasSendDateColumn = Schema::hasColumn('daily_activity_schedules', 'send_date');
        $hasWorkingDaysColumn = Schema::hasColumn('daily_activity_schedules', 'working_days');
        $hasSubjectColumn = Schema::hasColumn('daily_activity_schedules', 'subject');
        $hasActivityColumnsColumn = Schema::hasColumn('daily_activity_schedules', 'activity_columns');

        foreach ($rows as $row) {
            $email = strtolower(trim((string) ($row['client_email'] ?? '')));
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $sendTime = trim((string) ($row['send_time'] ?? '18:00'));
            if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $sendTime)) {
                $sendTime = '18:00';
            }

            $scheduleType = $this->sanitizeScheduleType((string) ($row['schedule_type'] ?? 'daily'));
            $sendDate = trim((string) ($row['send_date'] ?? ''));
            $workingDays = $this->sanitizeWorkingDays((string) ($row['working_days'] ?? ''));
            $name = trim((string) ($row['client_name'] ?? ''));
            $subject = trim((string) ($row['subject'] ?? ''));
            $activityColumns = $this->sanitizeActivityColumns((string) ($row['activity_columns'] ?? ''));
            $payload = [
                'client_name' => $name !== '' ? $name : null,
                'enabled' => !empty($row['enabled']),
                'send_time' => $sendTime,
            ];
            if ($hasScheduleTypeColumn) {
                $payload['schedule_type'] = $scheduleType;
            }
            if ($hasSendDateColumn) {
                $payload['send_date'] = $scheduleType === 'date' && $sendDate !== '' ? $sendDate : null;
            }
            if ($hasWorkingDaysColumn) {
                $payload['working_days'] = $workingDays;
            }
            if ($hasSubjectColumn) {
                $payload['subject'] = $subject !== '' ? $subject : null;
            }
            if ($hasActivityColumnsColumn) {
                $payload['activity_columns'] = $activityColumns;
            }

            $existing = DailyActivitySchedule::query()
                ->where('client_email', $email)
                ->first();

            if ($existing) {
                $sendTimeChanged = (string) $existing->send_time !== $sendTime;
                $enabledBecameTrue = !$existing->enabled && !empty($row['enabled']);
                $scheduleTypeChanged = $hasScheduleTypeColumn && (string) ($existing->schedule_type ?? 'daily') !== $scheduleType;
                $sendDateChanged = $hasSendDateColumn && (string) ($existing->send_date?->toDateString() ?? '') !== ($scheduleType === 'date' ? $sendDate : '');
                $workingDaysChanged = $hasWorkingDaysColumn && (string) ($existing->working_days ?? 'mon,tue,wed,thu,fri') !== $workingDays;

                if ($sendTimeChanged || $enabledBecameTrue || $scheduleTypeChanged || $sendDateChanged || $workingDaysChanged) {
                    // Allow scheduler to send again today using the updated schedule.
                    $payload['last_sent_date'] = null;
                }
            }

            DailyActivitySchedule::query()->updateOrCreate(
                ['client_email' => $email],
                $payload
            );
        }
    }

    /**
     * @return array<int, array{client_email: string, client_name: string|null}>
     */
    protected function knownClientProfiles(): array
    {
        $projects = Project::query()
            ->with(['clientUser:id,name,email'])
            ->select(['client_name', 'client_email', 'client_user_id', 'updated_at'])
            ->where('status', 'active')
            ->orderBy('updated_at', 'desc')
            ->get();

        $profiles = [];
        $dedupe = [];

        foreach ($projects as $project) {
            $projectEmail = trim((string) $project->client_email);
            $linkedUserEmail = trim((string) ($project->clientUser?->email ?? ''));
            $email = strtolower($projectEmail !== '' ? $projectEmail : $linkedUserEmail);

            $projectName = trim((string) ($project->client_name ?? ''));
            $linkedUserName = trim((string) ($project->clientUser?->name ?? ''));
            $name = $projectName !== '' ? $projectName : $linkedUserName;
            $nameKey = mb_strtolower($name);
            $key = $nameKey . '|' . $email;

            if ($name === '' && $email === '') {
                continue;
            }

            if (isset($dedupe[$key])) {
                continue;
            }

            $dedupe[$key] = true;
            $profiles[] = [
                'client_email' => $email !== '' ? $email : '',
                'client_name' => $name !== '' ? $name : null,
            ];
        }

        $clientUsers = User::query()
            ->select(['name', 'email'])
            ->where('role', 'client')
            ->whereHas('clientProjects', function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('name')
            ->get();

        foreach ($clientUsers as $clientUser) {
            $email = strtolower(trim((string) $clientUser->email));
            if ($email === '') {
                continue;
            }

            $name = trim((string) ($clientUser->name ?? ''));
            $nameKey = mb_strtolower($name);
            $key = $nameKey . '|' . $email;
            if (isset($dedupe[$key])) {
                continue;
            }

            $dedupe[$key] = true;
            $profiles[] = [
                'client_email' => $email,
                'client_name' => $name !== '' ? $name : null,
            ];
        }

        return $profiles;
    }

    protected function sanitizeActivityColumns(string $raw): string
    {
        $allowed = ['date', 'project', 'clock_in', 'clock_out', 'duration', 'description'];
        $items = preg_split('/[,\s;]+/', strtolower($raw)) ?: [];
        $clean = [];

        foreach ($items as $item) {
            $key = trim($item);
            if ($key !== '' && in_array($key, $allowed, true)) {
                $clean[] = $key;
            }
        }

        $clean = array_values(array_unique($clean));
        if (empty($clean)) {
            $clean = $allowed;
        }

        return implode(',', $clean);
    }

    protected function sanitizeScheduleType(string $raw): string
    {
        $type = strtolower(trim($raw));
        return in_array($type, ['daily', 'date'], true) ? $type : 'daily';
    }

    protected function sanitizeWorkingDays(string $raw): string
    {
        $allowed = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        $items = preg_split('/[,\s;]+/', strtolower($raw)) ?: [];
        $clean = [];

        foreach ($items as $item) {
            $key = trim($item);
            if ($key !== '' && in_array($key, $allowed, true)) {
                $clean[] = $key;
            }
        }

        $clean = array_values(array_unique($clean));
        if (empty($clean)) {
            $clean = ['mon', 'tue', 'wed', 'thu', 'fri'];
        }

        return implode(',', $clean);
    }

    /**
     * Clear application caches (config, route, view) from the settings UI.
     */
    public function flushCache(Request $request)
    {
        try {
            $commands = [
                'config:clear',
                'cache:clear',
                'route:clear',
                'view:clear',
                'optimize:clear',
            ];

            $outputs = [];

            foreach ($commands as $command) {
                Artisan::call($command);
                $outputs[$command] = trim(Artisan::output());
            }

            Log::info('Application caches cleared from settings UI', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application caches cleared successfully.',
                'commands' => $commands,
                'output' => $outputs,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to clear caches: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Debug email configuration - shows what's in database vs what will be used.
     */
    public function debugEmail()
    {
        $defaults = [
            'email_mailer'           => 'default',
            'email_smtp_host'        => null,
            'email_smtp_port'        => null,
            'email_smtp_username'    => null,
            'email_smtp_password'    => null,
            'email_smtp_encryption'  => null,
            'email_from_address'     => config('mail.from.address'),
            'email_from_name'        => config('mail.from.name'),
        ];

        $keys = array_keys($defaults);
        $dbSettings = Setting::getValues($keys, $defaults);

        // Check raw database value
        $rawMailer = Setting::where('key', 'email_mailer')->first();

        return response()->json([
            'success' => true,
            'database_settings' => $dbSettings,
            'raw_email_mailer_record' => $rawMailer,
            'default_mailer_from_env' => config('mail.default'),
            'mail_from_env' => [
                'MAIL_MAILER' => env('MAIL_MAILER'),
                'MAIL_HOST' => env('MAIL_HOST'),
                'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
            ],
        ]);
    }

    /**
     * Return application logs for the settings debug tab.
     */
    public function logs(Request $request)
    {
        $validated = $request->validate([
            'lines' => 'nullable|integer|min:50|max:3000',
            'file' => 'nullable|string|max:255',
            'level' => 'nullable|in:all,debug,info,notice,warning,error,critical,alert,emergency',
        ]);

        $lineLimit = (int) ($validated['lines'] ?? 400);
        $level = (string) ($validated['level'] ?? 'all');
        $logsDir = storage_path('logs');

        if (!File::isDirectory($logsDir)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'selected_file' => null,
                    'available_files' => [],
                    'content' => '',
                    'line_count' => 0,
                    'level' => $level,
                ],
            ]);
        }

        $availableFiles = collect(File::files($logsDir))
            ->filter(fn ($file) => str_ends_with($file->getFilename(), '.log'))
            ->map(function ($file) {
                return [
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'modified_at' => date('Y-m-d H:i:s', $file->getMTime()),
                    'modified_ts' => $file->getMTime(),
                ];
            })
            ->sortByDesc('modified_ts')
            ->values()
            ->all();

        if (empty($availableFiles)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'selected_file' => null,
                    'available_files' => [],
                    'content' => '',
                    'line_count' => 0,
                    'level' => $level,
                ],
            ]);
        }

        $requestedFile = trim((string) ($validated['file'] ?? ''));
        $selectedFile = $availableFiles[0]['name'];

        foreach ($availableFiles as $fileMeta) {
            if ($requestedFile !== '' && $fileMeta['name'] === $requestedFile) {
                $selectedFile = $fileMeta['name'];
                break;
            }
            if ($requestedFile === '' && $fileMeta['name'] === 'laravel.log') {
                $selectedFile = 'laravel.log';
                break;
            }
        }

        $selectedPath = $logsDir . DIRECTORY_SEPARATOR . $selectedFile;
        $lines = $this->tailFileLines($selectedPath, $lineLimit);

        if ($level !== 'all') {
            $needle = ".{$level}:";
            $lines = array_values(array_filter($lines, function (string $line) use ($needle): bool {
                return stripos($line, $needle) !== false;
            }));
        }

        return response()->json([
            'success' => true,
            'data' => [
                'selected_file' => $selectedFile,
                'available_files' => $availableFiles,
                'content' => implode("\n", $lines),
                'line_count' => count($lines),
                'level' => $level,
            ],
        ]);
    }

    /**
     * Delete a log file from storage/logs.
     */
    public function deleteLogFile(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|string|max:255',
        ]);

        $fileName = basename((string) $validated['file']);
        $logsDir = storage_path('logs');
        $targetPath = $logsDir . DIRECTORY_SEPARATOR . $fileName;

        if (!str_ends_with(strtolower($fileName), '.log')) {
            return response()->json([
                'success' => false,
                'message' => 'Only .log files can be deleted.',
            ], 422);
        }

        if (!File::isDirectory($logsDir) || !File::exists($targetPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Log file not found.',
            ], 404);
        }

        try {
            File::delete($targetPath);

            Log::warning('Log file deleted from settings UI', [
                'file' => $fileName,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Deleted {$fileName}",
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete log file.',
            ], 500);
        }
    }

    /**
     * Read the last N lines of a text file efficiently.
     *
     * @return array<int, string>
     */
    protected function tailFileLines(string $path, int $lineLimit): array
    {
        if (!File::exists($path) || $lineLimit <= 0) {
            return [];
        }

        try {
            $file = new \SplFileObject($path, 'r');
            $file->seek(PHP_INT_MAX);
            $lastLine = $file->key();
            $startLine = max(0, $lastLine - $lineLimit + 1);

            $lines = [];
            for ($lineNo = $startLine; $lineNo <= $lastLine; $lineNo++) {
                $file->seek($lineNo);
                if (!$file->eof()) {
                    $line = rtrim((string) $file->current(), "\r\n");
                    if ($line !== '') {
                        $lines[] = $line;
                    }
                }
            }

            return $lines;
        } catch (\Throwable $e) {
            report($e);
            return [];
        }
    }

    /**
     * Generate HTML for test email.
     */
    protected function getTestEmailHtml(): string
    {
        $appName = config('app.name');
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #f9f9f9;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        h1 {
            color: #8b5cf6;
            margin-top: 0;
        }
        .success {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info {
            background: #e0e7ff;
            border-left: 4px solid #6366f1;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        ul {
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Email Configuration Test</h1>

        <div class="success">
            <strong>Success!</strong> Your email configuration is working correctly.
        </div>

        <p>This is a test email from <strong>' . htmlspecialchars($appName) . '</strong>.</p>

        <div class="info">
            <strong>What this means:</strong>
            <ul>
                <li>Your email server is properly configured</li>
                <li>Emails are being sent successfully</li>
                <li>Invoice emails should work correctly</li>
            </ul>
        </div>

        <p>If you received this email in your spam folder, please mark it as "Not Spam" to ensure future invoices arrive in your inbox.</p>

        <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 13px;">
            Sent from ' . htmlspecialchars($appName) . ' at ' . date('Y-m-d H:i:s') . '
        </p>
    </div>
</body>
</html>';
    }
}

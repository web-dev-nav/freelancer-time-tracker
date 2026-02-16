<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
        } catch (\Throwable $e) {
            report($e);
            $settings = $defaults;
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

            $data = Setting::getValues($keys, $defaults);
            $this->ensureActivityRecipientsDefault($data, $defaultActivityRecipients);

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

<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class SendScheduledInvoiceEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled invoice emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for scheduled invoices to send...');

        // Find invoices scheduled to be sent now or in the past
        $invoices = Invoice::with(['project', 'items'])
            ->whereNotNull('scheduled_send_at')
            ->where('scheduled_send_at', '<=', Carbon::now())
            ->whereNull('sent_at')
            ->get();

        if ($invoices->isEmpty()) {
            $this->info('No scheduled invoices to send at this time.');
            return 0;
        }

        $this->info("Found {$invoices->count()} scheduled invoice(s) to send.");

        $sent = 0;
        $failed = 0;

        foreach ($invoices as $invoice) {
            try {
                $this->info("Processing invoice {$invoice->invoice_number}...");

                if (!$invoice->client_email) {
                    $this->warn("Skipping invoice {$invoice->invoice_number}: No client email");
                    $invoice->scheduled_send_at = null;
                    $invoice->save();
                    continue;
                }

                // Get settings
                $companySettings = $this->getInvoiceSettings();
                $emailSettings = $this->getEmailSettings();
                $mailerConfig = $this->prepareMailerConfiguration($emailSettings);

                // Mark invoice as sent BEFORE generating PDF
                if ($invoice->status === 'draft') {
                    $invoice->markAsSent();
                    $invoice->refresh();
                }

                $invoice->ensureViewToken();
                if ($invoice->isDirty('view_token')) {
                    $invoice->save();
                    $invoice->refresh();
                }

                // Generate PDF
                $pdf = PDF::loadView('invoices.pdf', [
                    'invoice' => $invoice,
                    'companySettings' => $companySettings,
                ]);
                $pdfContent = $pdf->output();

                // Email subject and body
                $defaultCompanyName = $companySettings['invoice_company_name'] ?? config('app.name');
                $subject = "Invoice {$invoice->invoice_number} from " . $defaultCompanyName;

                // Generate detailed message with payment instructions (same as regular send)
                $message = $this->generateInvoiceEmailMessage($invoice, $companySettings);
                $htmlMessage = $this->convertToHtmlEmail($message);
                $htmlMessage = $this->injectTrackingPixel($htmlMessage, $invoice);

                // Send email
                Mail::mailer($mailerConfig['mailer'])
                    ->send([], [], function ($mail) use ($invoice, $subject, $htmlMessage, $pdfContent, $mailerConfig) {
                        $mail->to($invoice->client_email)
                            ->subject($subject)
                            ->html($htmlMessage)
                            ->attachData($pdfContent, "invoice-{$invoice->invoice_number}.pdf", [
                                'mime' => 'application/pdf',
                            ]);

                        if ($mailerConfig['from_address']) {
                            $mail->from(
                                $mailerConfig['from_address'],
                                $mailerConfig['from_name'] ?: $mailerConfig['from_address']
                            );
                        }
                    });

                // Clear scheduled send time
                $invoice->scheduled_send_at = null;
                $invoice->save();

                // Log history
                $invoice->logHistory('sent', 'Invoice email sent to ' . $invoice->client_email . ' (scheduled)', [
                    'email' => $invoice->client_email,
                    'subject' => $subject,
                    'sent_via' => 'scheduled_cron',
                ]);

                $this->info("✓ Sent invoice {$invoice->invoice_number} to {$invoice->client_email}");
                $sent++;

            } catch (\Exception $e) {
                $this->error("Failed to send invoice {$invoice->invoice_number}: {$e->getMessage()}");
                Log::error("Failed to send scheduled invoice {$invoice->invoice_number}", [
                    'error' => $e->getMessage(),
                    'invoice_id' => $invoice->id,
                ]);
                $failed++;
            }
        }

        $this->info("\n✓ Successfully sent {$sent} invoice(s)");
        if ($failed > 0) {
            $this->warn("✗ Failed to send {$failed} invoice(s)");
        }

        return 0;
    }

    /**
     * Get invoice settings
     */
    private function getInvoiceSettings()
    {
        $settings = Setting::whereIn('key', [
            'invoice_company_name',
            'invoice_company_email',
            'invoice_company_address',
        ])->pluck('value', 'key')->toArray();

        return $settings;
    }

    /**
     * Get email settings
     */
    private function getEmailSettings()
    {
        $settings = Setting::whereIn('key', [
            'email_mailer',
            'email_from_address',
            'email_from_name',
            'email_smtp_host',
            'email_smtp_port',
            'email_smtp_username',
            'email_smtp_password',
            'email_smtp_encryption',
        ])->pluck('value', 'key')->toArray();

        return $settings;
    }

    /**
     * Prepare mailer configuration
     */
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

    private function injectTrackingPixel(string $html, Invoice $invoice): string
    {
        $invoice->ensureViewToken();
        if ($invoice->isDirty('view_token')) {
            $invoice->save();
        }

        $trackingUrl = route('invoices.track-open', [
            'invoice' => $invoice->id,
            'token' => $invoice->view_token,
        ]);

        $pixel = '<img src="' . e($trackingUrl) . '" width="1" height="1" style="display:none;" alt="" />';

        if (str_contains($html, '</body>')) {
            return str_replace('</body>', $pixel . '</body>', $html);
        }

        return $html . $pixel;
    }

    /**
     * Generate invoice email message with payment instructions
     */
    private function generateInvoiceEmailMessage(Invoice $invoice, array $companySettings): string
    {
        $paymentSettings = Setting::whereIn('key', [
            'payment_etransfer_email',
            'payment_bank_info',
            'payment_instructions',
        ])->pluck('value', 'key')->toArray();

        $companyName = $companySettings['invoice_company_name'] ?? config('app.name');

        // Calculate current month bill period
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startDate = $startOfMonth->format('M d, Y');
        $endDate = $endOfMonth->format('M d, Y');
        $dueDate = $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A';

        // Build email body
        $body = "Thank you for choosing {$companyName}. The invoice for bill period ({$startDate} - {$endDate}) is attached.\n\n";
        $body .= "The total amount $" . number_format($invoice->total, 2) . " will be due on {$dueDate}.\n\n";

        // Add payment instructions
        $hasPaymentInfo = false;
        $body .= "Payment Instructions:\n";
        $instructionNumber = 1;

        // E-Transfer
        if (!empty($paymentSettings['payment_etransfer_email'])) {
            $body .= "{$instructionNumber}. By Interac e-Transfer\n";
            $body .= "   Send to: {$paymentSettings['payment_etransfer_email']}\n";
            $body .= "   Reference: Invoice {$invoice->invoice_number}\n\n";
            $instructionNumber++;
            $hasPaymentInfo = true;
        }

        // Direct Deposit
        if (!empty($paymentSettings['payment_bank_info'])) {
            $body .= "{$instructionNumber}. By Direct Deposit\n";
            $bankLines = explode("\n", $paymentSettings['payment_bank_info']);
            foreach ($bankLines as $line) {
                if (trim($line)) {
                    $body .= "   " . trim($line) . "\n";
                }
            }
            $body .= "\n";
            $instructionNumber++;
            $hasPaymentInfo = true;
        }

        // Additional instructions
        if (!empty($paymentSettings['payment_instructions'])) {
            $body .= "{$instructionNumber}. {$paymentSettings['payment_instructions']}\n\n";
            $instructionNumber++;
            $hasPaymentInfo = true;
        }

        // If no payment info is configured in settings, show a reminder
        if (!$hasPaymentInfo) {
            $body .= "Please configure payment instructions in Settings.\n\n";
        }

        $body .= "If you have any questions, please don't hesitate to contact us.\n\n";
        $body .= "Best regards,\n{$companyName}";

        return $body;
    }

    /**
     * Convert plain text message to HTML
     */
    private function convertToHtmlEmail($text)
    {
        $html = nl2br(htmlspecialchars($text));

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        {$html}
    </div>
</body>
</html>
HTML;
    }
}

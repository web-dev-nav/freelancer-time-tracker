<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

class InvoiceMailer
{
    /**
     * Generate a PDF for the provided invoice.
     */
    public function generatePdf(Invoice $invoice, ?array $companySettings = null)
    {
        $companySettings = $companySettings ?? $this->getInvoiceSettings();

        return Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'companySettings' => $companySettings,
        ]);
    }

    /**
     * Send an invoice email to the provided recipient.
     *
     * @param  Invoice  $invoice
     * @param  string|null  $recipientEmail
     * @param  array<string, mixed>  $options
     */
    public function sendInvoiceEmail(Invoice $invoice, ?string $recipientEmail = null, array $options = []): void
    {
        $recipientEmail = $recipientEmail ?? $invoice->client_email;

        if (!$recipientEmail) {
            throw new InvalidArgumentException('Recipient email address is required to send an invoice.');
        }

        $companySettings = $this->getInvoiceSettings();
        $emailSettings = $this->getEmailSettings();
        $mailerConfig = $this->prepareMailerConfiguration($emailSettings);

        $pdf = $this->generatePdf($invoice, $companySettings);
        $pdfContent = $pdf->output();

        $defaultCompanyName = $companySettings['invoice_company_name'] ?? config('app.name');
        $subject = $options['subject'] ?? "Invoice {$invoice->invoice_number} from {$defaultCompanyName}";
        $message = $options['message'] ?? "Please find attached invoice {$invoice->invoice_number}.";

        Mail::mailer($mailerConfig['mailer'])->send([], [], function ($mail) use (
            $recipientEmail,
            $subject,
            $message,
            $pdfContent,
            $invoice,
            $mailerConfig
        ) {
            $mail->to($recipientEmail)
                ->subject($subject)
                ->html($message)
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
    }

    /**
     * Retrieve invoice/company settings shared across PDFs & emails.
     *
     * @return array<string, mixed>
     */
    public function getInvoiceSettings(): array
    {
        return Setting::getValues([
            'invoice_company_name',
            'invoice_company_address',
            'invoice_tax_number',
        ], [
            'invoice_company_name' => config('app.name'),
            'invoice_company_address' => null,
            'invoice_tax_number' => null,
        ]);
    }

    /**
     * Retrieve email delivery settings.
     *
     * @return array<string, mixed>
     */
    public function getEmailSettings(): array
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

    /**
     * Configure mailer characteristics for the current request.
     *
     * @param  array<string, mixed>  $emailSettings
     * @return array{mailer:string,from_address:?string,from_name:?string}
     */
    protected function prepareMailerConfiguration(array $emailSettings): array
    {
        $mailer = config('mail.default');

        if (($emailSettings['email_mailer'] ?? 'default') === 'smtp' && !empty($emailSettings['email_smtp_host'])) {
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
        }

        return [
            'mailer' => $mailer,
            'from_address' => $emailSettings['email_from_address'] ?? config('mail.from.address'),
            'from_name' => $emailSettings['email_from_name'] ?? config('mail.from.name'),
        ];
    }
}

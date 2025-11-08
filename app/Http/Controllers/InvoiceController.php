<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Project;
use App\Models\Setting;
use App\Models\TimeLog;
use App\Services\InvoiceMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function __construct(protected InvoiceMailer $invoiceMailer)
    {
    }

    /**
     * Get all invoices with pagination and filtering
     */
    public function index(Request $request)
    {
        $query = Invoice::with('project');

        // Hide cancelled invoices by default (unless specifically requested)
        if (!$request->has('show_cancelled') || $request->show_cancelled != 'true') {
            $query->where('status', '!=', 'cancelled');
        }

        // Filter by project
        if ($request->has('project_id') && $request->project_id != 'all') {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Search by invoice number or client name
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_number', 'like', '%' . $request->search . '%')
                  ->orWhere('client_name', 'like', '%' . $request->search . '%');
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($invoices);
    }

    /**
     * Get a single invoice with items
     */
    public function show($id)
    {
        $invoice = Invoice::with(['project', 'items.timeLog'])->findOrFail($id);
        return response()->json($invoice);
    }

    /**
     * Create a new invoice
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'time_log_ids' => 'array',
            'time_log_ids.*' => 'exists:time_logs,id',
            'items' => 'array',
            'items.*.description' => 'required|string',
            'items.*.work_date' => 'required|date',
            'items.*.hours' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
            'client_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $project = Project::findOrFail($request->project_id);

            // Create the invoice
            $invoice = new Invoice();
            $clientName = $request->get('client_name');
            if (is_string($clientName)) {
                $clientName = trim($clientName);
            }

            $invoice->invoice_number = $invoice->generateInvoiceNumber();
            $invoice->project_id = $project->id;
            $invoice->client_name = $clientName ?: ($project->client_name ?? $project->name);
            $invoice->client_email = $request->exists('client_email')
                ? $request->input('client_email')
                : $project->client_email;
            $invoice->client_address = $request->exists('client_address')
                ? $request->input('client_address')
                : $project->client_address;

            $companySettings = $this->getInvoiceSettings();
            $invoice->company_name = $companySettings['invoice_company_name'] ?? config('app.name');
            $invoice->company_address = $companySettings['invoice_company_address'];

            $invoice->invoice_date = $request->invoice_date;
            $invoice->due_date = $request->due_date;
            $invoice->tax_rate = $project->has_tax ? 13.00 : 0.00;
            $invoice->notes = $request->notes;
            $invoice->description = $request->description;
            $invoice->status = 'draft';
            $invoice->save();

            // Add time logs as invoice items
            if ($request->has('time_log_ids') && is_array($request->time_log_ids)) {
                foreach ($request->time_log_ids as $timeLogId) {
                    $timeLog = TimeLog::findOrFail($timeLogId);

                    $item = new InvoiceItem();
                    $item->invoice_id = $invoice->id;
                    $item->time_log_id = $timeLog->id;
                    $item->description = $timeLog->work_description ?? 'Work performed';
                    $item->work_date = Carbon::parse($timeLog->clock_in)->toDateString();
                    $item->hours = $timeLog->total_minutes / 60;
                    $item->rate = $project->hourly_rate ?? 0;
                    $item->calculateAmount();
                }
            }

            // Add custom items if provided
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $itemData) {
                    $item = new InvoiceItem();
                    $item->invoice_id = $invoice->id;
                    $item->time_log_id = null;
                    $item->description = $itemData['description'];
                    $item->work_date = $itemData['work_date'];
                    $item->hours = $itemData['hours'];
                    $item->rate = $itemData['rate'];
                    $item->calculateAmount();
                }
            }

            // Calculate totals
            $invoice->calculateTotals();

            DB::commit();

            return response()->json([
                'message' => 'Invoice created successfully',
                'invoice' => $invoice->load(['project', 'items'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing invoice
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Only allow updates to draft invoices
        if ($invoice->status !== 'draft') {
            return response()->json([
                'message' => 'Only draft invoices can be edited'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'invoice_date' => 'sometimes|date',
            'due_date' => 'sometimes|date',
            'notes' => 'nullable|string',
            'description' => 'nullable|string',
            'client_name' => 'sometimes|string',
            'client_email' => 'sometimes|email|nullable',
            'client_address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $invoice->update($request->only([
                'invoice_date',
                'due_date',
                'notes',
                'description',
                'client_name',
                'client_email',
                'client_address'
            ]));

            return response()->json([
                'message' => 'Invoice updated successfully',
                'invoice' => $invoice->load(['project', 'items'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an invoice
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Only allow deletion of draft invoices
        if ($invoice->status !== 'draft') {
            return response()->json([
                'message' => 'Only draft invoices can be deleted'
            ], 403);
        }

        try {
            $invoice->delete();

            return response()->json([
                'message' => 'Invoice deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add item to invoice
     */
    public function addItem(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status !== 'draft') {
            return response()->json([
                'message' => 'Cannot add items to non-draft invoices'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'time_log_id' => 'nullable|exists:time_logs,id',
            'description' => 'required|string',
            'work_date' => 'required|date',
            'hours' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $item = new InvoiceItem();
            $item->invoice_id = $invoice->id;
            $item->time_log_id = $request->time_log_id;
            $item->description = $request->description;
            $item->work_date = $request->work_date;
            $item->hours = $request->hours;
            $item->rate = $request->rate;
            $item->calculateAmount();

            $invoice->calculateTotals();

            DB::commit();

            return response()->json([
                'message' => 'Item added successfully',
                'item' => $item,
                'invoice' => $invoice->load(['project', 'items'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to add item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from invoice
     */
    public function removeItem($id, $itemId)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status !== 'draft') {
            return response()->json([
                'message' => 'Cannot remove items from non-draft invoices'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $item = InvoiceItem::where('invoice_id', $invoice->id)
                ->where('id', $itemId)
                ->firstOrFail();

            $item->delete();

            $invoice->calculateTotals();

            DB::commit();

            return response()->json([
                'message' => 'Item removed successfully',
                'invoice' => $invoice->load(['project', 'items'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to remove item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate and download PDF
     */
    public function downloadPdf($id)
    {
        $invoice = Invoice::with(['project', 'items'])->findOrFail($id);

        $companySettings = $this->getInvoiceSettings();

        try {
            $pdf = PDF::loadView('invoices.pdf', [
                'invoice' => $invoice,
                'companySettings' => $companySettings,
            ]);

            return $pdf->download("invoice-{$invoice->invoice_number}.pdf");

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview PDF in browser
     */
    public function previewPdf($id)
    {
        $invoice = Invoice::with(['project', 'items'])->findOrFail($id);

        $companySettings = $this->getInvoiceSettings();

        try {
            $pdf = PDF::loadView('invoices.pdf', [
                'invoice' => $invoice,
                'companySettings' => $companySettings,
            ]);

            return $pdf->stream("invoice-{$invoice->invoice_number}.pdf");

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send invoice via email with PDF attachment
     */
    public function sendEmail(Request $request, $id)
    {
        $invoice = Invoice::with(['project', 'items'])->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'subject' => 'nullable|string',
            'message' => 'nullable|string',
            'scheduled_send_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Use client email from invoice or provided email
        $recipientEmail = $request->email ?? $invoice->client_email;

        if (!$recipientEmail) {
            return response()->json([
                'message' => 'No email address provided'
            ], 422);
        }

        // Handle scheduled send
        if ($request->has('scheduled_send_at') && $request->scheduled_send_at) {
            $scheduledTime = Carbon::parse($request->scheduled_send_at);

            // Check if scheduled time is in the past (with 1 minute buffer for processing time)
            if ($scheduledTime->lt(Carbon::now()->subMinute())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Scheduled time cannot be in the past. Please select a future date/time.'
                ], 422);
            }

            $invoice->scheduled_send_at = $scheduledTime;
            $invoice->client_email = $recipientEmail; // Save email for later sending
            $invoice->save();

            return response()->json([
                'message' => 'Invoice scheduled to send on ' . $scheduledTime->format('M d, Y h:i A'),
                'invoice' => $invoice->fresh(),
                'success' => true
            ]);
        }

        $companySettings = $this->getInvoiceSettings();
        $emailSettings = $this->getEmailSettings();
        $mailerConfig = $this->prepareMailerConfiguration($emailSettings);

        try {
            // Mark invoice as sent BEFORE generating PDF
            if ($invoice->status === 'draft') {
                $invoice->markAsSent();
                // Refresh the invoice to get updated status for PDF
                $invoice->refresh();
            }

            // Generate PDF with updated status
            $pdf = PDF::loadView('invoices.pdf', [
                'invoice' => $invoice,
                'companySettings' => $companySettings,
            ]);
            $pdfContent = $pdf->output();

            // Email subject and body
            $defaultCompanyName = $companySettings['invoice_company_name'] ?? config('app.name');
            $subject = $request->subject ?? "Invoice {$invoice->invoice_number} from " . $defaultCompanyName;
            $message = $request->message ?? "Please find attached invoice {$invoice->invoice_number}.";

            // Convert plain text message to HTML for better formatting
            $htmlMessage = $this->convertToHtmlEmail($message);

            // Send email
            Mail::mailer($mailerConfig['mailer'])
                ->send([], [], function ($mail) use ($recipientEmail, $subject, $htmlMessage, $pdfContent, $invoice, $mailerConfig) {
                    $mail->to($recipientEmail)
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

            return response()->json([
                'message' => 'Invoice sent successfully',
                'invoice' => $invoice->fresh(),
                'success' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status === 'paid') {
            return response()->json([
                'message' => 'Invoice is already marked as paid'
            ], 400);
        }

        try {
            $invoice->markAsPaid();

            // Clear scheduled send if it was scheduled
            if ($invoice->scheduled_send_at) {
                $invoice->scheduled_send_at = null;
                $invoice->save();
            }

            return response()->json([
                'message' => 'Invoice marked as paid',
                'invoice' => $invoice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel/Archive invoice
     */
    public function cancel($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Invoice is already cancelled'
            ], 400);
        }

        if ($invoice->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel a paid invoice'
            ], 400);
        }

        try {
            $invoice->markAsCancelled();

            // Clear scheduled send if it was scheduled
            if ($invoice->scheduled_send_at) {
                $invoice->scheduled_send_at = null;
                $invoice->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice cancelled successfully',
                'invoice' => $invoice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice statistics
     */
    public function stats(Request $request)
    {
        $query = Invoice::query();

        // Filter by project if provided
        if ($request->has('project_id') && $request->project_id != 'all') {
            $query->where('project_id', $request->project_id);
        }

        $stats = [
            'total_invoices' => $query->count(),
            'draft_count' => (clone $query)->draft()->count(),
            'sent_count' => (clone $query)->sent()->count(),
            'paid_count' => (clone $query)->paid()->count(),
            'overdue_count' => (clone $query)->overdue()->count(),
            'total_revenue' => (clone $query)->paid()->sum('total'),
            'pending_revenue' => (clone $query)->unpaid()->sum('total'),
        ];

        return response()->json($stats);
    }

    /**
     * Get unbilled time logs for a project
     */
    public function getUnbilledLogs(Request $request)
    {
        $query = TimeLog::completed()
            ->whereDoesntHave('invoiceItem')
            ->with('project');

        // Filter by project
        if ($request->has('project_id') && $request->project_id != 'all') {
            $query->where('project_id', $request->project_id);
        }

        $logs = $query->orderBy('clock_in', 'desc')->get();

        return response()->json($logs);
    }

    /**
     * Convert plain text email message to HTML format.
     *
     * @param string $message
     * @return string
     */
    protected function convertToHtmlEmail(string $message): string
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: left;
        }
        .email-content {
            color: #333333;
            font-size: 14px;
            text-align: left;
        }
        .email-content p {
            margin: 0 0 12px 0;
            text-align: left;
        }
        .payment-instructions {
            background-color: #f9f9f9;
            border-left: 4px solid #8b5cf6;
            padding: 15px 20px;
            margin: 20px 0;
            text-align: left;
        }
        .payment-instructions h3 {
            margin: 0 0 12px 0;
            color: #8b5cf6;
            font-size: 16px;
            text-align: left;
        }
        .payment-method {
            margin: 12px 0 8px 0;
            padding-left: 0;
            text-align: left;
        }
        .payment-method strong {
            color: #1f2937;
            display: block;
            margin-bottom: 3px;
            text-align: left;
        }
        .payment-detail {
            margin: 2px 0 2px 20px;
            color: #6b7280;
            line-height: 1.5;
            text-align: left;
        }
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 13px;
            text-align: left;
        }
        .highlight {
            color: #8b5cf6;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-content">';

        // Parse the message and format it
        $lines = explode("\n", $message);
        $inPaymentInstructions = false;
        $inPaymentMethod = false;

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            // Skip completely empty lines
            if (empty($trimmedLine)) {
                continue;
            }

            // Check if this is the payment instructions header
            if (stripos($trimmedLine, 'Payment Instructions:') !== false) {
                $html .= '</div><div class="payment-instructions"><h3>Payment Instructions</h3>';
                $inPaymentInstructions = true;
                continue;
            }

            // Check if this is a numbered payment method
            if (preg_match('/^(\d+)\.\s+(.+)$/', $trimmedLine, $matches)) {
                if ($inPaymentMethod) {
                    $html .= '</div>';
                }
                $html .= '<div class="payment-method"><strong>' . $matches[1] . '. ' . htmlspecialchars($matches[2]) . '</strong>';
                $inPaymentMethod = true;
                continue;
            }

            // Check for amount highlight
            if (preg_match('/total amount \$[\d,]+\.?\d*/i', $trimmedLine)) {
                $html .= '<p>' . preg_replace('/(\$[\d,]+\.?\d*)/', '<span class="highlight">$1</span>', htmlspecialchars($trimmedLine)) . '</p>';
                continue;
            }

            // Check if we're closing payment instructions (closing message detected)
            if ($inPaymentInstructions && (stripos($trimmedLine, 'If you have any questions') !== false || stripos($trimmedLine, 'Best regards') !== false)) {
                if ($inPaymentMethod) {
                    $html .= '</div>';
                    $inPaymentMethod = false;
                }
                $html .= '</div><div class="email-content">';
                $inPaymentInstructions = false;
                // Now add the closing line outside the payment block
                $html .= '<p>' . htmlspecialchars($trimmedLine) . '</p>';
                continue;
            }

            // Regular paragraph (outside payment instructions)
            if (!$inPaymentInstructions) {
                $html .= '<p>' . htmlspecialchars($trimmedLine) . '</p>';
                continue;
            }

            // Payment detail line (inside payment instructions, after a payment method)
            if ($inPaymentInstructions && $inPaymentMethod) {
                $html .= '<div class="payment-detail">' . htmlspecialchars($trimmedLine) . '</div>';
            }
        }

        if ($inPaymentMethod) {
            $html .= '</div>';
        }

        if ($inPaymentInstructions) {
            $html .= '</div><div class="email-content">';
        }

        $html .= '
        </div>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Retrieve shared invoice settings.
     *
     * @return array<string, mixed>
     */
    protected function getInvoiceSettings(): array
    {
        return Setting::getValues([
            'invoice_company_name',
            'invoice_company_address',
            'invoice_tax_number',
            'payment_etransfer_email',
            'payment_bank_info',
            'payment_instructions',
        ], [
            'invoice_company_name' => config('app.name'),
            'invoice_company_address' => null,
            'invoice_tax_number' => null,
            'payment_etransfer_email' => null,
            'payment_bank_info' => null,
            'payment_instructions' => null,
        ]);
    }

    /**
     * Retrieve email settings for invoice delivery.
     *
     * @return array<string, mixed>
     */
    protected function getEmailSettings(): array
    {
        $settings = Setting::getValues([
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

        \Log::info('Retrieved email settings from database (InvoiceController)', [
            'email_mailer' => $settings['email_mailer']
        ]);

        return $settings;
    }

    /**
     * Configure mailer based on settings.
     *
     * @param array<string, mixed> $emailSettings
     * @return array{mailer:string,from_address:?string,from_name:?string}
     */
    protected function prepareMailerConfiguration(array $emailSettings): array
    {
        $mailer = config('mail.default');
        $selectedMailer = $emailSettings['email_mailer'] ?? 'default';

        \Log::info('Preparing mailer configuration (InvoiceController)', [
            'selected_mailer' => $selectedMailer,
            'default_mailer' => $mailer,
            'email_settings' => $emailSettings
        ]);

        if ($selectedMailer === 'smtp' && !empty($emailSettings['email_smtp_host'])) {
            $dynamicMailer = 'settings_smtp';
            Config::set("mail.mailers.{$dynamicMailer}", [
                'transport' => 'smtp',
                'host' => $emailSettings['email_smtp_host'],
                'port' => (int)($emailSettings['email_smtp_port'] ?? 587),
                'encryption' => $emailSettings['email_smtp_encryption'] ?: null,
                'username' => $emailSettings['email_smtp_username'],
                'password' => $emailSettings['email_smtp_password'],
                'timeout' => null,
                'auth_mode' => null,
            ]);

            $mailer = $dynamicMailer;
            \Log::info('Using custom SMTP mailer (InvoiceController)');
        } elseif ($selectedMailer === 'mail') {
            $mailer = 'sendmail';
            \Log::info('Using PHP mail() function via sendmail transport (InvoiceController)');
        } else {
            \Log::info('Using default mailer from .env (InvoiceController)', ['mailer' => $mailer]);
        }

        \Log::info('Final mailer configuration (InvoiceController)', [
            'mailer' => $mailer,
            'from_address' => $emailSettings['email_from_address'] ?? config('mail.from.address'),
            'from_name' => $emailSettings['email_from_name'] ?? config('mail.from.name')
        ]);

        return [
            'mailer' => $mailer,
            'from_address' => $emailSettings['email_from_address'] ?? config('mail.from.address'),
            'from_name' => $emailSettings['email_from_name'] ?? config('mail.from.name'),
        ];
    }

}

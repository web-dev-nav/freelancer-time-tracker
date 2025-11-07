<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendScheduledInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminder emails for unpaid invoices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for invoices that need payment reminders...');

        // Find invoices that are:
        // 1. Status = 'sent' (not draft, not paid)
        // 2. Due date is within the next 3 days OR already overdue
        $today = Carbon::today();
        $reminderThreshold = $today->copy()->addDays(3);

        $invoices = Invoice::with(['project'])
            ->where('status', 'sent')
            ->where('due_date', '<=', $reminderThreshold)
            ->get();

        if ($invoices->isEmpty()) {
            $this->info('No invoices need reminders at this time.');
            return 0;
        }

        $this->info("Found {$invoices->count()} invoice(s) that need reminders.");

        $sent = 0;
        $failed = 0;

        foreach ($invoices as $invoice) {
            try {
                $dueDate = Carbon::parse($invoice->due_date);
                $isOverdue = $dueDate->isPast();
                $daysUntilDue = $today->diffInDays($dueDate, false);

                if ($isOverdue) {
                    $this->warn("Invoice {$invoice->invoice_number} is overdue (due {$dueDate->format('Y-m-d')})");
                } else {
                    $this->info("Invoice {$invoice->invoice_number} is due in {$daysUntilDue} day(s)");
                }

                // You can implement email sending logic here
                // For now, we'll just log it
                Log::info("Payment reminder needed for invoice {$invoice->invoice_number}", [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'client_name' => $invoice->client_name,
                    'client_email' => $invoice->client_email,
                    'due_date' => $invoice->due_date,
                    'total' => $invoice->total,
                    'is_overdue' => $isOverdue,
                ]);

                $sent++;
            } catch (\Exception $e) {
                $this->error("Failed to process invoice {$invoice->invoice_number}: {$e->getMessage()}");
                Log::error("Failed to send reminder for invoice {$invoice->invoice_number}", [
                    'error' => $e->getMessage(),
                    'invoice_id' => $invoice->id,
                ]);
                $failed++;
            }
        }

        $this->info("\n✓ Processed {$sent} invoice(s)");
        if ($failed > 0) {
            $this->warn("✗ Failed to process {$failed} invoice(s)");
        }

        return 0;
    }
}

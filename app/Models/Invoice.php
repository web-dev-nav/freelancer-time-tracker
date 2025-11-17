<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    public const STRIPE_FEE_NOTE = 'A Stripe processing fee (2.9% + $0.30 CAD) applies only when you choose to pay via Stripe.';

    protected $fillable = [
        'invoice_number',
        'project_id',
        'client_name',
        'client_email',
        'client_address',
        'company_name',
        'company_email',
        'company_address',
        'invoice_date',
        'due_date',
        'status',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'stripe_fees_included',
        'stripe_fee_amount',
        'stripe_fee_percentage',
        'stripe_fee_fixed',
        'total',
        'notes',
        'description',
        'sent_at',
        'scheduled_send_at',
        'paid_at',
        'cancelled_at',
        'view_token',
        'opened_at',
        'opened_count',
        'opened_ip',
        'opened_user_agent',
    ];

    // SECURITY: Protect Stripe fields from mass assignment
    // These should only be set programmatically by the StripePaymentService
    protected $guarded = [
        'stripe_payment_link',
        'stripe_payment_intent_id',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'sent_at' => 'datetime',
        'scheduled_send_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'stripe_fees_included' => 'boolean',
        'stripe_fee_amount' => 'decimal:2',
        'stripe_fee_percentage' => 'decimal:2',
        'stripe_fee_fixed' => 'decimal:2',
        'total' => 'decimal:2',
        'opened_at' => 'datetime',
        'opened_count' => 'integer',
    ];

    protected $appends = [
        'formatted_invoice_date',
        'formatted_due_date',
        'is_overdue',
    ];

    protected static function booted()
    {
        static::creating(function (Invoice $invoice) {
            $invoice->ensureViewToken();
        });
    }

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function history()
    {
        return $this->hasMany(InvoiceHistory::class)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['draft', 'sent']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'sent')
            ->where('due_date', '<', Carbon::now()->toDateString());
    }

    // Methods
    public function calculateTotals()
    {
        $this->subtotal = $this->items()->sum('amount');
        $this->tax_amount = $this->subtotal * ($this->tax_rate / 100);

        // Calculate Stripe fees if included
        if ($this->stripe_fees_included) {
            $this->calculateStripeFees();
        } else {
            $this->stripe_fee_amount = 0;
        }

        // Invoice total should reflect the amount owing before any optional Stripe fees
        $this->total = $this->subtotal + $this->tax_amount;
        $this->save();
    }

    /**
     * Calculate Stripe transaction fees
     * Based on Stripe Canada pricing: 2.9% + $0.30 CAD per transaction
     */
    public function calculateStripeFees()
    {
        $baseAmount = $this->subtotal + $this->tax_amount;

        if ($baseAmount <= 0) {
            $this->stripe_fee_amount = 0;
            return;
        }

        // Stripe fee percentage (default 2.9% for Canadian card payments)
        $percentage = ($this->stripe_fee_percentage ?? 2.9) / 100;

        // Stripe fixed fee (default $0.30 CAD)
        $fixedFee = $this->stripe_fee_fixed ?? 0.30;

        // Guard against invalid configuration to avoid division by zero
        if ($percentage >= 1) {
            $this->stripe_fee_amount = 0;
            return;
        }

        // Solve for the gross amount so the merchant receives the base amount after Stripe fees:
        // gross - (gross * percentage + fixedFee) = baseAmount  =>  gross = (baseAmount + fixedFee) / (1 - percentage)
        $grossCharge = ($baseAmount + $fixedFee) / (1 - $percentage);
        $fee = max(0, $grossCharge - $baseAmount);

        $this->stripe_fee_amount = round($fee, 2);
    }

    public function syncStripeFeeNote(): void
    {
        if ($this->stripe_fees_included) {
            $this->ensureStripeFeeNote();
        } else {
            $this->removeStripeFeeNote();
        }
    }

    public function ensureStripeFeeNote(): void
    {
        if (!$this->stripe_fees_included) {
            return;
        }

        $note = self::STRIPE_FEE_NOTE;
        $existing = (string) ($this->notes ?? '');

        if ($existing !== '' && str_contains($existing, $note)) {
            return;
        }

        $trimmed = trim($existing);
        $this->notes = $trimmed !== '' ? $trimmed . PHP_EOL . PHP_EOL . $note : $note;
    }

    public function removeStripeFeeNote(): void
    {
        if (!$this->notes) {
            return;
        }

        $note = self::STRIPE_FEE_NOTE;

        if (!str_contains($this->notes, $note)) {
            return;
        }

        $parts = array_map(static fn ($part) => trim($part), explode($note, $this->notes));
        $parts = array_filter($parts, static fn ($part) => $part !== '');
        $this->notes = $parts ? implode(PHP_EOL . PHP_EOL, $parts) : null;
    }

    public function markAsSent()
    {
        $this->status = 'sent';
        $this->sent_at = Carbon::now();
        $this->save();
    }

    public function markAsPaid()
    {
        $this->status = 'paid';
        $this->paid_at = Carbon::now();
        $this->save();
    }

    public function markAsCancelled()
    {
        $this->status = 'cancelled';
        $this->cancelled_at = Carbon::now();
        $this->save();
    }

    public function ensureViewToken(): void
    {
        if (empty($this->view_token)) {
            $this->view_token = $this->generateViewToken();
        }
    }

    public function generateViewToken(): string
    {
        $token = Str::random(40);
        $this->view_token = $token;
        return $token;
    }

    public function generateInvoiceNumber()
    {
        $date = Carbon::now();
        $year = $date->format('Y');
        $month = $date->format('m');

        // Find the last invoice number for this month
        $lastInvoice = static::where('invoice_number', 'like', "INV-{$year}-{$month}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('INV-%s-%s-%04d', $year, $month, $newNumber);
    }

    /**
     * Log a history event for this invoice
     */
    public function logHistory($action, $description = null, $metadata = [])
    {
        return $this->history()->create([
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    // Accessors
    public function getIsOverdueAttribute()
    {
        return $this->status === 'sent' &&
               $this->due_date < Carbon::now()->toDateString();
    }

    public function getFormattedInvoiceDateAttribute()
    {
        return $this->invoice_date->format('M d, Y');
    }

    public function getFormattedDueDateAttribute()
    {
        return $this->due_date->format('M d, Y');
    }
}

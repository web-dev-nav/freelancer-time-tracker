<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory;

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
        'total',
        'notes',
        'description',
        'sent_at',
        'paid_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected $appends = [
        'formatted_invoice_date',
        'formatted_due_date',
        'is_overdue',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
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
        $this->total = $this->subtotal + $this->tax_amount;
        $this->save();
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'time_log_id',
        'description',
        'work_date',
        'hours',
        'rate',
        'amount',
    ];

    protected $casts = [
        'work_date' => 'date',
        'hours' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function timeLog()
    {
        return $this->belongsTo(TimeLog::class);
    }

    // Accessors
    public function getFormattedWorkDateAttribute()
    {
        return $this->work_date->format('M d, Y');
    }

    // Methods
    public function calculateAmount()
    {
        $this->amount = $this->hours * $this->rate;
        $this->save();
    }
}

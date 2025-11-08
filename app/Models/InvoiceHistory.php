<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceHistory extends Model
{
    const UPDATED_AT = null; // We only track creation time, not updates

    protected $table = 'invoice_history';

    protected $fillable = [
        'invoice_id',
        'action',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the invoice that owns this history entry
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

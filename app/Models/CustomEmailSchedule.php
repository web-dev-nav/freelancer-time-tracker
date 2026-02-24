<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomEmailSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'body',
        'recipients',
        'schedule_type',
        'send_time',
        'send_date',
        'working_days',
        'enabled',
        'status',
        'last_sent_date',
        'sent_at',
    ];

    protected $casts = [
        'recipients' => 'array',
        'enabled' => 'boolean',
        'send_date' => 'date',
        'last_sent_date' => 'date',
        'sent_at' => 'datetime',
    ];
}

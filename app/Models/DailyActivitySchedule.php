<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyActivitySchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_email',
        'client_name',
        'enabled',
        'schedule_type',
        'send_time',
        'send_date',
        'working_days',
        'subject',
        'activity_columns',
        'last_sent_date',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'send_date' => 'date',
        'last_sent_date' => 'date',
    ];
}

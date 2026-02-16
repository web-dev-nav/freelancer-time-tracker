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
        'send_time',
        'last_sent_date',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'last_sent_date' => 'date',
    ];
}

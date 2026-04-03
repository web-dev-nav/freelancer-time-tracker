<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedulerLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'source',
        'type',
        'name',
        'status',
        'detail',
        'scheduled_at',
        'executed_at',
        'message',
        'payload',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'executed_at' => 'datetime',
        'payload' => 'array',
    ];
}

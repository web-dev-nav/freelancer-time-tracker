<?php

// Create model: php artisan make:model WorkEntry

// app/Models/WorkEntry.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class WorkEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_date',
        'start_time',
        'end_time',
        'task_description',
        'duration'
    ];

    protected $casts = [
        'work_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'duration' => 'decimal:2'
    ];

    // Scope to get entries for a specific date range
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('work_date', [$startDate, $endDate]);
    }

    // Scope to get entries for a 2-week period ending on a specific date
    public function scopeTwoWeekPeriod($query, $endDate)
    {
        $startDate = Carbon::parse($endDate)->subDays(13);
        return $query->betweenDates($startDate, $endDate);
    }

    // Calculate total hours for a collection
    public static function calculateTotalHours($entries)
    {
        return $entries->sum('duration');
    }
}
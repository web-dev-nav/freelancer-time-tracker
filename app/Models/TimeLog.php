<?php

// app/Models/TimeLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'project_id',
        'status',
        'clock_in',
        'clock_out',
        'total_minutes',
        'work_description',
        'project_name',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'total_minutes' => 'integer'
    ];

    // Boot method to auto-generate session_id
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($timeLog) {
            if (empty($timeLog->session_id)) {
                $timeLog->session_id = Str::random(32);
            }
        });
    }

    // Accessors for formatted display
    public function getClockInDateAttribute()
    {
        $raw = $this->getRawOriginal('clock_in');
        if (!$raw) {
            return null;
        }

        return Carbon::parse($raw, 'UTC')
            ->setTimezone('America/Toronto')
            ->format('Y-m-d');
    }

    public function getClockInTimeAttribute()
    {
        $raw = $this->getRawOriginal('clock_in');
        if (!$raw) {
            return null;
        }

        return Carbon::parse($raw, 'UTC')
            ->setTimezone('America/Toronto')
            ->format('H:i');
    }

    public function getClockOutTimeAttribute()
    {
        $raw = $this->getRawOriginal('clock_out');
        if (!$raw) {
            return null;
        }

        return Carbon::parse($raw, 'UTC')
            ->setTimezone('America/Toronto')
            ->format('H:i');
    }

    public function getDurationHoursAttribute()
    {
        return $this->total_minutes ? round($this->total_minutes / 60, 2) : 0;
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->total_minutes) return '0:00';
        
        $hours = floor($this->total_minutes / 60);
        $minutes = $this->total_minutes % 60;
        return sprintf('%d:%02d', $hours, $minutes);
    }

    // Add these accessors to the appends array so they're included in JSON
    protected $appends = [
        'clock_in_date',
        'clock_in_time',
        'clock_out_time',
        'duration_hours',
        'formatted_duration'
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function invoiceItem()
    {
        return $this->hasOne(InvoiceItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('clock_in', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);
    }

    public function scopeTwoWeekPeriod($query, $endDate)
    {
        $end = Carbon::parse($endDate)->endOfDay();
        $start = $end->copy()->subDays(13)->startOfDay();

        return $query->byDateRange($start, $end);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeActiveProjects($query)
    {
        return $query->where(function($q) {
            $q->whereHas('project', function($subQ) {
                $subQ->where('status', 'active');
            })->orWhereNull('project_id');
        });
    }

    // Static methods
    public static function getActiveSession()
    {
        return static::active()->first();
    }

    public static function createSession($clockIn, $projectId = null, $ipAddress = null, $userAgent = null)
    {
        return static::create([
            'clock_in' => $clockIn,
            'project_id' => $projectId,
            'status' => 'active',
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);
    }

    public function completeSession($clockOut, $workDescription, $projectName = null)
    {
        $this->clock_out = $clockOut;
        $this->work_description = $workDescription;
        $this->project_name = $projectName;
        $this->total_minutes = $this->clock_in->diffInMinutes($clockOut);
        $this->status = 'completed';
        $this->save();

        return $this;
    }

    public function cancelSession()
    {
        $this->status = 'cancelled';
        $this->save();
        
        return $this;
    }

    // Calculate totals for collections
    public static function calculateTotalHours($logs)
    {
        return $logs->sum('total_minutes') / 60;
    }

    public static function calculateTotalMinutes($logs)
    {
        return $logs->sum('total_minutes');
    }
}

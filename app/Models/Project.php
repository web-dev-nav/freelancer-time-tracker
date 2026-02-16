<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'client_name',
        'client_email',
        'client_user_id',
        'client_address',
        'client_can_access_dashboard',
        'client_can_access_history',
        'client_can_access_reports',
        'client_can_access_invoices',
        'color',
        'hourly_rate',
        'has_tax',
        'status',
        'description'
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'has_tax' => 'boolean',
        'client_can_access_dashboard' => 'boolean',
        'client_can_access_history' => 'boolean',
        'client_can_access_reports' => 'boolean',
        'client_can_access_invoices' => 'boolean',
    ];

    // Relationships
    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function clientUser()
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    // Accessors
    public function getTotalHoursAttribute()
    {
        return $this->timeLogs()->completed()->sum('total_minutes') / 60;
    }

    public function getTotalMinutesAttribute()
    {
        return $this->timeLogs()->completed()->sum('total_minutes');
    }

    public function getTotalSessionsAttribute()
    {
        return $this->timeLogs()->completed()->count();
    }

    public function getTotalEarningsAttribute()
    {
        if (!$this->hourly_rate) {
            return null;
        }
        $earnings = $this->total_hours * $this->hourly_rate;

        // Apply 13% tax if enabled
        if ($this->has_tax) {
            $earnings = $earnings * 1.13;
        }

        return $earnings;
    }

    // Methods
    public function archive()
    {
        $this->status = 'archived';
        $this->save();
        return $this;
    }

    public function activate()
    {
        $this->status = 'active';
        $this->save();
        return $this;
    }

    // Static helpers
    public static function getActiveProjects()
    {
        return static::active()->orderBy('name')->get();
    }
}

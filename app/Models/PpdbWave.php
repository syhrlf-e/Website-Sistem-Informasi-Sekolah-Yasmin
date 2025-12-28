<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * PpdbWave Model
 * 
 * Represents a registration wave/period for PPDB
 * Auto-activates/deactivates based on start_date and end_date
 */
class PpdbWave extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'academic_year',
        'start_date',
        'end_date',
        'quota',
        'fee',
        'is_active',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: wave has many registrations
     */
    public function registrations()
    {
        return $this->hasMany(PpdbRegistration::class, 'wave_id');
    }

    /**
     * Scope: Get currently active waves
     * Wave is considered open if:
     * - is_active = true
     * - today is between start_date and end_date
     */
    public function scopeOpen($query)
    {
        $today = Carbon::today();
        return $query->where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today);
    }

    /**
     * Scope: Get waves for specific academic year
     */
    public function scopeForAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Check if wave is currently open for registration
     */
    public function isOpen(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = Carbon::today();
        return $today->between($this->start_date, $this->end_date);
    }

    /**
     * Get registration count for this wave
     */
    public function getRegistrationCountAttribute(): int
    {
        return $this->registrations()->count();
    }

    /**
     * Check if quota is reached
     */
    public function isQuotaFull(): bool
    {
        if ($this->quota === null) {
            return false; // No quota limit
        }
        return $this->registration_count >= $this->quota;
    }

    /**
     * Get available slots
     */
    public function getAvailableSlotsAttribute(): ?int
    {
        if ($this->quota === null) {
            return null; // Unlimited
        }
        return max(0, $this->quota - $this->registration_count);
    }

    /**
     * Get status counts for this wave
     */
    public function getStatusCountsAttribute(): array
    {
        return [
            'pending' => $this->registrations()->where('status', 'pending')->count(),
            'verified' => $this->registrations()->where('status', 'verified')->count(),
            'selection' => $this->registrations()->where('status', 'selection')->count(),
            'accepted' => $this->registrations()->where('status', 'accepted')->count(),
            'rejected' => $this->registrations()->where('status', 'rejected')->count(),
        ];
    }
}

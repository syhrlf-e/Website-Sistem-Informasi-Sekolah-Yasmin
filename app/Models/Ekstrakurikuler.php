<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ekstrakurikuler extends Model
{
    use HasFactory;

    protected $table = 'ekstrakurikuler';

    protected $fillable = [
        'nama',
        'tagline',
        'badge',
        'deskripsi',
        'benefits',
        'jadwal',
        'pembina',
        'whatsapp_admin',
        'lokasi',
        'gambar',
        'is_active',
        'enable_registration',
        'registration_token',
        'token_expires_at',
        'token_generated_at',
        'max_participants',
        'registration_deadline'
    ];

    protected $casts = [
        'benefits' => 'array',
        'is_active' => 'boolean',
        'enable_registration' => 'boolean',
        'token_expires_at' => 'datetime',
        'token_generated_at' => 'datetime',
        'registration_deadline' => 'datetime'
    ];

    /**
     * Append computed attributes to JSON
     */
    protected $appends = [
        'is_registration_open',
        'registration_closure_reason',
        'approved_registrations_count',
        'available_slots',
        'is_slot_full'
    ];

    /**
     * Get all registrations for this ekstrakurikuler
     */
    public function registrations()
    {
        return $this->hasMany(EkstrakurikulerRegistration::class);
    }

    /**
     * Accessor: Get count of approved registrations
     * Uses eager-loaded count if available (via withCount), otherwise queries
     */
    public function getApprovedRegistrationsCountAttribute(): int
    {
        // Check if count was eager-loaded via withCount()
        // This prevents N+1 queries when loading multiple ekstrakurikuler
        if (isset($this->attributes['approved_count'])) {
            return (int) $this->attributes['approved_count'];
        }

        // Fallback to query (for single model access)
        return $this->registrations()->where('status', 'approved')->count();
    }

    /**
     * Accessor: Get available slots remaining
     * Returns -1 if no limit set (unlimited)
     */
    public function getAvailableSlotsAttribute(): int
    {
        if (!$this->max_participants) {
            return -1; // unlimited
        }
        return max(0, $this->max_participants - $this->approved_registrations_count);
    }

    /**
     * Accessor: Check if slots are full
     */
    public function getIsSlotFullAttribute(): bool
    {
        if (!$this->max_participants) {
            return false; // no limit = never full
        }
        return $this->available_slots <= 0;
    }

    /**
     * Check if registration token is valid
     */
    public function hasValidToken()
    {
        return $this->registration_token &&
            $this->token_expires_at &&
            $this->token_expires_at->isFuture();
    }

    /**
     * Check if can generate new token (24-hour cooldown)
     */
    public function canGenerateToken()
    {
        // If never generated, can generate
        if (!$this->token_generated_at) {
            return true;
        }

        // Check if 24 hours have passed since last generation
        // Use copy() to avoid modifying original Carbon instance
        return $this->token_generated_at->copy()->addHours(24)->isPast();
    }

    /**
     * Check if registration deadline has passed
     */
    public function isDeadlinePassed()
    {
        // If no deadline set, never passed
        if (!$this->registration_deadline) {
            return false;
        }

        // Check if current time is past the deadline
        return $this->registration_deadline->isPast();
    }

    /**
     * Check if registration is currently allowed
     * Takes into account: manual toggle, deadline, and slot availability
     */
    public function canRegister()
    {
        // If registration is manually disabled, cannot register
        if (!$this->enable_registration) {
            return false;
        }

        // If deadline has passed, cannot register
        if ($this->isDeadlinePassed()) {
            return false;
        }

        // If slots are full, cannot register
        if ($this->is_slot_full) {
            return false;
        }

        // All conditions met
        return true;
    }

    /**
     * Accessor: Get computed registration status
     * This combines enable_registration, deadline check, and slot availability
     */
    public function getIsRegistrationOpenAttribute()
    {
        return $this->canRegister();
    }

    /**
     * Accessor: Get the reason why registration is closed
     * Returns: 'open', 'manual_close', 'deadline_passed', or 'slot_full'
     */
    public function getRegistrationClosureReasonAttribute()
    {
        // If can register, it's open
        if ($this->canRegister()) {
            return 'open';
        }

        // Check specific reasons in priority order
        if (!$this->enable_registration) {
            return 'manual_close';
        }

        if ($this->isDeadlinePassed()) {
            return 'deadline_passed';
        }

        if ($this->is_slot_full) {
            return 'slot_full';
        }

        return 'manual_close';
    }
}
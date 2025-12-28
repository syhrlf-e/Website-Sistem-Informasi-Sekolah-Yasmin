<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class EkstrakurikulerRegistration extends Model
{
    use HasFactory;
    protected $fillable = [
        'ekstrakurikuler_id',
        'nama_lengkap',
        'kelas',
        'email',
        'no_whatsapp',
        'alasan_bergabung',
        'registration_token',
        'status',
        'admin_notes',
        'approved_by',
        'approved_at',
    ];
    protected $casts = [
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    /**
     * Get the ekstrakurikuler that this registration belongs to
     */
    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class);
    }
    /**
     * Get the admin user who approved/rejected this registration
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    /**
     * Scope to get pending registrations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    /**
     * Scope to get approved registrations
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    /**
     * Scope to get rejected registrations
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
    /**
     * Scope to search by name, email, or class
     * Uses FULLTEXT index for nama_lengkap and email (faster)
     * Falls back to LIKE for short queries or kelas field
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            if (strlen($search) < 3) {
                // LIKE fallback for very short queries
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('kelas', 'like', "%{$search}%");
            } else {
                // FULLTEXT search for nama_lengkap and email
                $q->whereFullText(['nama_lengkap', 'email'], $search)
                    ->orWhere('kelas', 'like', "%{$search}%");
            }
        });
    }
    /**
     * Check if registration is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
    /**
     * Check if registration is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }
    /**
     * Check if registration is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}

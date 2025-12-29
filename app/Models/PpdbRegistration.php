<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * PpdbRegistration Model
 * 
 * Represents a PPDB applicant with all their data.
 * Auto-generates registration_number and token on creation.
 */
class PpdbRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        // System
        'wave_id',
        'status',
        'catatan_admin',
        'foto_path',
        'dokumen_path',
        'verified_at',
        'accepted_at',
        'verified_by',

        // Identitas Diri
        'nama_lengkap',
        'nik',
        'nisn',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'kewarganegaraan',
        'anak_ke',
        'jumlah_saudara',
        'no_hp',
        'email',
        'hobi',
        'cita_cita',
        'prestasi_akademik',
        'prestasi_non_akademik',

        // Tempat Tinggal
        'alamat_lengkap',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'kota_kabupaten',
        'provinsi',
        'kode_pos',

        // Jurusan
        'jurusan_pilihan',

        // Kesehatan
        'golongan_darah',
        'tinggi_badan',
        'berat_badan',
        'riwayat_penyakit',
        'alergi',
        'keterangan_alergi',

        // Pendidikan
        'asal_sekolah',
        'npsn_sekolah',
        'alamat_sekolah',
        'tahun_lulus',

        // Ayah
        'nama_ayah',
        'nik_ayah',
        'pekerjaan_ayah',
        'pendidikan_ayah',
        'penghasilan_ayah',
        'no_hp_ayah',
        'ayah_masih_hidup',

        // Ibu
        'nama_ibu',
        'nik_ibu',
        'pekerjaan_ibu',
        'pendidikan_ibu',
        'penghasilan_ibu',
        'no_hp_ibu',
        'ibu_masih_hidup',

        // Wali
        'nama_wali',
        'nik_wali',
        'hubungan_wali',
        'pekerjaan_wali',
        'pendidikan_wali',
        'penghasilan_wali',
        'no_hp_wali',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'alergi' => 'boolean',
        'ayah_masih_hidup' => 'boolean',
        'ibu_masih_hidup' => 'boolean',
        'tinggi_badan' => 'decimal:2',
        'berat_badan' => 'decimal:2',
        'verified_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($registration) {
            // Auto-generate registration number: PPDB-2025-0001
            $year = date('Y');
            $lastReg = self::whereYear('created_at', $year)
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = $lastReg
                ? (int) substr($lastReg->registration_number, -4) + 1
                : 1;

            $registration->registration_number = sprintf('PPDB-%s-%04d', $year, $nextNumber);

            // Auto-generate 16-character alphanumeric token (mixed case)
            $registration->token = Str::random(16);
        });
    }

    /**
     * Relationship: registration belongs to a wave
     */
    public function wave()
    {
        return $this->belongsTo(PpdbWave::class, 'wave_id');
    }

    /**
     * Relationship: verified by user
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope: filter by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: filter by wave
     */
    public function scopeForWave($query, $waveId)
    {
        return $query->where('wave_id', $waveId);
    }

    /**
     * Scope: search by name or registration number
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%")
                ->orWhere('registration_number', 'like', "%{$search}%")
                ->orWhere('nik', 'like', "%{$search}%")
                ->orWhere('no_hp', 'like', "%{$search}%");
        });
    }

    /**
     * Get status label in Indonesian
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'selection' => 'Tahap Seleksi',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
            default => 'Unknown',
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'verified' => 'blue',
            'selection' => 'purple',
            'accepted' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    /**
     * Verify registration by checking registration_number and token
     */
    public static function verifyCredentials(string $registrationNumber, string $token): ?self
    {
        return self::where('registration_number', $registrationNumber)
            ->where('token', strtoupper($token))
            ->first();
    }

    /**
     * Update status with timestamp
     */
    public function updateStatus(string $newStatus, ?int $verifiedBy = null): bool
    {
        $this->status = $newStatus;

        if ($newStatus === 'verified' && !$this->verified_at) {
            $this->verified_at = now();
            $this->verified_by = $verifiedBy;
        }

        if ($newStatus === 'accepted' && !$this->accepted_at) {
            $this->accepted_at = now();
        }

        return $this->save();
    }

    /**
     * Get full address formatted
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [
            $this->alamat_lengkap,
            "RT {$this->rt}/RW {$this->rw}",
            "Kel. {$this->kelurahan}",
            "Kec. {$this->kecamatan}",
            $this->kota_kabupaten,
            $this->provinsi,
            $this->kode_pos,
        ];

        return implode(', ', array_filter($parts));
    }
}

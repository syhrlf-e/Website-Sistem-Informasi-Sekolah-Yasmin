<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    protected $table = 'sekolahs';

    protected $fillable = [
        'npsn',
        'nama',
        'nama_normalized',
        'bentuk',
        'status',
        'akreditasi',
        'alamat_jalan',
        'nama_dusun',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        'lintang',
        'bujur',
    ];

    protected $casts = [
        'lintang' => 'decimal:7',
        'bujur' => 'decimal:7',
    ];

    /**
     * Normalize name for search (lowercase, remove special chars)
     */
    public static function normalizeName(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/[^a-z0-9\s]/', '', $name);
        // Expand abbreviations
        $name = str_replace(['smpn', 'smp n'], 'smp negeri', $name);
        $name = preg_replace('/\s+/', ' ', trim($name));
        return $name;
    }

    /**
     * Search schools by keyword
     */
    public static function search(string $keyword, int $limit = 20)
    {
        $keyword = trim($keyword);

        if (strlen($keyword) < 3) {
            return collect();
        }

        $normalized = self::normalizeName($keyword);
        $words = explode(' ', $normalized);

        return self::query()
            ->where(function ($query) use ($words, $normalized) {
                // Match normalized name
                $query->where('nama_normalized', 'LIKE', "%{$normalized}%");

                // Or match each word
                foreach ($words as $word) {
                    if (strlen($word) >= 2) {
                        $query->orWhere('nama_normalized', 'LIKE', "%{$word}%")
                            ->orWhere('kecamatan', 'LIKE', "%{$word}%")
                            ->orWhere('kabupaten', 'LIKE', "%{$word}%");
                    }
                }
            })
            ->orderByRaw("CASE 
                WHEN nama_normalized LIKE ? THEN 1
                WHEN nama_normalized LIKE ? THEN 2
                ELSE 3
            END", ["{$normalized}%", "%{$normalized}%"])
            ->limit($limit)
            ->get();
    }
}

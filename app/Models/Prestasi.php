<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    use HasFactory;

    protected $table = 'prestasi';

    protected $fillable = [
        'nama_prestasi',
        'slug',
        'kategori',
        'tingkat',
        'tahun',
        'peserta',
        'image',
        'image_crop', // Crop coordinates: {x, y, width, height, scale}
        'deskripsi'
    ];

    protected $casts = [
        'peserta' => 'array',
        'image_crop' => 'array', // JSON to array
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['kategori'] ?? false, function ($query, $kategori) {
            return $query->where('kategori', $kategori);
        });

        $query->when($filters['tingkat'] ?? false, function ($query, $tingkat) {
            return $query->where('tingkat', $tingkat);
        });

        $query->when($filters['tahun'] ?? false, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        });
    }
}

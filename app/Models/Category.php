<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke Berita (ganti dari posts)
    public function berita()
    {
        return $this->hasMany(Berita::class, 'category_id');
    }
    
    // Backward compatibility (kalau masih ada yang pakai posts)
    // Bisa dihapus kalau udah yakin gak dipake lagi
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
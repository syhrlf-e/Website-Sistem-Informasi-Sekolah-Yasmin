<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'author',
        'role',
        'text',
        'photo',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    // Scope untuk filter testimoni aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk sorting berdasarkan order
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class Announcement extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'image',
        'link',
        'start_date',
        'end_date',
        'is_active',
        'priority'
    ];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];
    // Accessor untuk full image URL
    public function getImageAttribute($value)
    {
        if (!$value) return null;
        
        // Kalau sudah full URL, return as is
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        
        // Kalau path lokal, return full URL
        return asset('storage/' . $value);
    }
    // Scope untuk announcement yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now())
                    ->orderBy('priority', 'desc')
                    ->orderBy('created_at', 'desc');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_id',
        'image_path',
        'title',
        'description',
        'order'
    ];

    protected $casts = [
        'order' => 'integer'
    ];

    protected $appends = ['image_url'];

    /**
     * Relationship: Belongs to Gallery
     */
    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }

    /**
     * Accessor for full image URL
     */
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'orientation',
        'width',
        'height',
        'order',
        'grid_position',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'width' => 'integer',
        'height' => 'integer',
        'order' => 'integer'
    ];

    protected $appends = ['image_url'];

    /**
     * Relationship: Has many additional images
     */
    public function images()
    {
        return $this->hasMany(GalleryImage::class)->orderBy('order');
    }

    /**
     * Accessor untuk full URL gambar utama
     */
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }

    /**
     * Get all images for slideshow (main + additional)
     */
    public function getAllImagesAttribute()
    {
        $images = [];

        // Add main image first (uses slot's title/description)
        if ($this->image_path) {
            $images[] = [
                'id' => 'main',
                'image_path' => $this->image_path,
                'image_url' => $this->image_url,
                'title' => $this->title,
                'description' => $this->description,
                'order' => 0
            ];
        }

        // Add additional images (each has own title/description)
        foreach ($this->images as $img) {
            $images[] = [
                'id' => $img->id,
                'image_path' => $img->image_path,
                'image_url' => $img->image_url,
                'title' => $img->title,
                'description' => $img->description,
                'order' => $img->order
            ];
        }

        return $images;
    }
}
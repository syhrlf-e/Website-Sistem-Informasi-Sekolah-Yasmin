<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

/**
 * Trait ProcessesImages
 * 
 * Provides helper methods for image upload, conversion to WebP,
 * and automatic compression/resizing.
 * 
 * @example
 * use App\Traits\ProcessesImages;
 * 
 * class MyController {
 *     use ProcessesImages;
 *     
 *     public function store(Request $request) {
 *         $path = $this->uploadImage($request->file('image'), 'news');
 *     }
 * }
 */
trait ProcessesImages
{
    /**
     * Upload image, convert to WebP, resize, and compress
     * 
     * @param UploadedFile $file The uploaded image file
     * @param string $folder Storage folder (e.g., 'news', 'gallery', 'prestasi') - without 'public/' prefix
     * @param int $maxWidth Maximum width (default 1200)
     * @param int $maxHeight Maximum height (default 1200)
     * @param int $quality WebP quality 1-100 (default 85)
     * @return string The storage path to the saved image (relative to public disk)
     */
    protected function uploadImage(
        UploadedFile $file,
        string $folder,
        int $maxWidth = 1200,
        int $maxHeight = 1200,
        int $quality = 85
    ): string {
        // Remove 'public/' prefix if accidentally included
        $folder = preg_replace('/^public\//', '', $folder);

        // Generate unique filename with WebP extension
        $filename = time() . '_' . Str::random(10) . '.webp';
        $path = $folder . '/' . $filename;

        // Create image manager with GD driver
        $manager = new ImageManager(new Driver());

        // Read and process image
        $image = $manager->read($file);

        // Resize if larger than max dimensions (maintain aspect ratio)
        $image->scale(width: $maxWidth, height: $maxHeight);

        // Encode to WebP with specified quality
        $encoded = $image->toWebp($quality);

        // Save to PUBLIC storage disk
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    /**
     * Upload thumbnail image with smaller dimensions
     * 
     * @param UploadedFile $file The uploaded image file
     * @param string $folder Storage folder
     * @return string The storage path
     */
    protected function uploadThumbnail(UploadedFile $file, string $folder): string
    {
        return $this->uploadImage($file, $folder, 400, 400, 80);
    }

    /**
     * Delete image from storage
     * 
     * @param string|null $path The storage path to delete
     * @return bool
     */
    protected function deleteImage(?string $path): bool
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    /**
     * Get full URL for image path
     * 
     * @param string|null $path Storage path
     * @return string|null
     */
    protected function getImageUrl(?string $path): ?string
    {
        if (!$path)
            return null;
        return Storage::url($path);
    }
}

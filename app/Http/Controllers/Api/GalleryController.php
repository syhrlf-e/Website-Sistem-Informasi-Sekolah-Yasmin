<?php
namespace App\Http\Controllers\Api;

use App\Models\Gallery;
use App\Traits\ProcessesImages;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    use ProcessesImages;
    use LogsActivity;

    /**
     * Get galleries for frontend (cached)
     */
    public function index()
    {
        $galleries = Cache::remember('galleries_public', 3600, function () {
            return Gallery::with('images')
                ->where('is_active', true)
                ->orderBy('order')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
        });
        return response()->json($galleries);
    }

    /**
     * Get all galleries for admin panel
     */
    public function adminIndex()
    {
        $galleries = Gallery::orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($galleries);
    }

    /**
     * Upload single atau multiple with WebP conversion
     */
    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'titles' => 'array',
            'titles.*' => 'string|max:255',
            'descriptions' => 'array',
            'descriptions.*' => 'string|nullable',
        ]);

        $uploadedGalleries = [];

        foreach ($request->file('images') as $index => $image) {
            // Upload with WebP conversion
            $result = $this->uploadGalleryImage($image);

            // Create gallery record
            $gallery = Gallery::create([
                'title' => $request->titles[$index] ?? 'Gallery Image',
                'description' => $request->descriptions[$index] ?? null,
                'image_path' => $result['path'],
                'orientation' => $result['orientation'],
                'width' => $result['width'],
                'height' => $result['height'],
                'order' => Gallery::max('order') + 1,
            ]);

            $uploadedGalleries[] = $gallery;
        }

        // ✅ Log activity
        $this->logActivity('create', 'Mengupload ' . count($uploadedGalleries) . ' gambar galeri baru');

        // Clear cache
        $this->clearGalleryCaches();

        return response()->json([
            'message' => 'Images uploaded successfully',
            'data' => $uploadedGalleries
        ], 201);
    }

    /**
     * Update gallery
     */
    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title' => 'string|max:255',
            'description' => 'string|nullable',
            'is_active' => 'boolean',
            'order' => 'integer',
        ]);

        $gallery->update($request->only(['title', 'description', 'is_active', 'order']));

        // ✅ Log activity
        $this->logUpdate('Galeri', $gallery, []);

        // Clear cache
        $this->clearGalleryCaches();

        return response()->json([
            'message' => 'Gallery updated successfully',
            'data' => $gallery
        ]);
    }

    /**
     * Delete gallery
     */
    public function destroy(Gallery $gallery)
    {
        // ✅ Log activity before delete
        $this->logDelete('Galeri', $gallery);

        // Delete file from storage
        $this->deleteImage($gallery->image_path);

        $gallery->delete();

        // Clear cache
        $this->clearGalleryCaches();

        return response()->json([
            'message' => 'Gallery deleted successfully'
        ]);
    }

    // ==================== GRID SYSTEM METHODS ====================

    /**
     * Get galleries by grid position (untuk frontend)
     */
    public function getGridGalleries()
    {
        $galleries = Gallery::with('images')
            ->whereNotNull('grid_position')
            ->where('is_active', true)
            ->whereBetween('grid_position', [1, 9])
            ->orderBy('grid_position')
            ->get();

        $gridGalleries = [];
        foreach ($galleries as $gallery) {
            $gridGalleries[$gallery->grid_position - 1] = [
                'id' => $gallery->id,
                'title' => $gallery->title,
                'description' => $gallery->description,
                'image_path' => $gallery->image_path,
                'orientation' => $gallery->orientation,
                'grid_position' => $gallery->grid_position,
                'image_url' => asset('storage/' . $gallery->image_path)
            ];
        }

        return response()->json($gridGalleries);
    }

    /**
     * Get grid galleries for admin (include inactive and all images)
     */
    public function adminGridIndex()
    {
        $galleries = Gallery::with('images')
            ->whereNotNull('grid_position')
            ->whereBetween('grid_position', [1, 9])
            ->orderBy('grid_position')
            ->get();

        $gridGalleries = array_fill(0, 9, null);

        foreach ($galleries as $gallery) {
            $gridGalleries[$gallery->grid_position - 1] = [
                'id' => $gallery->id,
                'title' => $gallery->title,
                'description' => $gallery->description,
                'image_path' => $gallery->image_path,
                'orientation' => $gallery->orientation,
                'width' => $gallery->width,
                'height' => $gallery->height,
                'grid_position' => $gallery->grid_position,
                'is_active' => $gallery->is_active,
                'image_url' => asset('storage/' . $gallery->image_path),
                'all_images' => $gallery->all_images, // Include all images for slideshow
                'image_count' => count($gallery->all_images)
            ];
        }

        return response()->json($gridGalleries);
    }

    /**
     * Get available slots with validation
     */
    public function getAvailableSlots()
    {
        $filledPositions = Gallery::whereNotNull('grid_position')
            ->whereBetween('grid_position', [1, 9])
            ->pluck('grid_position')
            ->toArray();

        $row1Complete = in_array(1, $filledPositions) && in_array(2, $filledPositions);
        $row2Complete = in_array(3, $filledPositions) && in_array(4, $filledPositions) &&
            in_array(5, $filledPositions) && in_array(6, $filledPositions);

        $availableSlots = [];

        for ($i = 1; $i <= 9; $i++) {
            $isAvailable = !in_array($i, $filledPositions);

            if ($i <= 2) {
                $availableSlots[$i] = $isAvailable;
            } elseif ($i <= 6) {
                $availableSlots[$i] = $isAvailable && $row1Complete;
            } else {
                $availableSlots[$i] = $isAvailable && $row1Complete && $row2Complete;
            }
        }

        return response()->json([
            'available_slots' => $availableSlots,
            'filled_positions' => $filledPositions,
            'row1_complete' => $row1Complete,
            'row2_complete' => $row2Complete
        ]);
    }

    /**
     * Store gallery to specific grid position with WebP conversion
     */
    public function storeGridGallery(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'grid_position' => 'required|integer|min:1|max:9',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Check if position is already filled
        $existingGallery = Gallery::where('grid_position', $request->grid_position)->first();
        if ($existingGallery) {
            return response()->json([
                'message' => 'Posisi grid sudah terisi'
            ], 422);
        }

        // Validate sequential rule
        $filledPositions = Gallery::whereNotNull('grid_position')
            ->whereBetween('grid_position', [1, 9])
            ->pluck('grid_position')
            ->toArray();

        $row1Complete = in_array(1, $filledPositions) && in_array(2, $filledPositions);
        $row2Complete = in_array(3, $filledPositions) && in_array(4, $filledPositions) &&
            in_array(5, $filledPositions) && in_array(6, $filledPositions);

        $position = $request->grid_position;

        if ($position > 2 && !$row1Complete) {
            return response()->json([
                'message' => 'Lengkapi baris 1 terlebih dahulu (slot 1 & 2)'
            ], 422);
        }

        if ($position > 6 && !$row2Complete) {
            return response()->json([
                'message' => 'Lengkapi baris 2 terlebih dahulu (slot 3, 4, 5 & 6)'
            ], 422);
        }

        // Upload image with WebP conversion
        $result = $this->uploadGalleryImage($request->file('image'));

        // Create gallery
        $gallery = Gallery::create([
            'title' => $request->title,
            'description' => $request->description,
            'image_path' => $result['path'],
            'orientation' => $result['orientation'],
            'width' => $result['width'],
            'height' => $result['height'],
            'grid_position' => $request->grid_position,
            'order' => $request->grid_position,
            'is_active' => true,
        ]);

        // Clear cache
        $this->clearGalleryCaches();

        return response()->json([
            'message' => 'Gallery berhasil ditambahkan',
            'data' => [
                'id' => $gallery->id,
                'title' => $gallery->title,
                'description' => $gallery->description,
                'image_path' => $gallery->image_path,
                'grid_position' => $gallery->grid_position,
                'image_url' => asset('storage/' . $gallery->image_path)
            ]
        ], 201);
    }

    /**
     * Update grid gallery with WebP conversion
     */
    public function updateGridGallery(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Update text fields
        if ($request->has('title')) {
            $gallery->title = $request->title;
        }
        if ($request->has('description')) {
            $gallery->description = $request->description;
        }
        if ($request->has('is_active')) {
            $gallery->is_active = $request->is_active;
        }

        // Update image if provided with WebP conversion
        if ($request->hasFile('image')) {
            // Delete old image
            $this->deleteImage($gallery->image_path);

            // Upload new image with WebP conversion
            $result = $this->uploadGalleryImage($request->file('image'));

            $gallery->image_path = $result['path'];
            $gallery->width = $result['width'];
            $gallery->height = $result['height'];
            $gallery->orientation = $result['orientation'];
        }

        $gallery->save();

        // Clear cache
        $this->clearGalleryCaches();

        return response()->json([
            'message' => 'Gallery berhasil diupdate',
            'data' => [
                'id' => $gallery->id,
                'title' => $gallery->title,
                'description' => $gallery->description,
                'image_path' => $gallery->image_path,
                'grid_position' => $gallery->grid_position,
                'is_active' => $gallery->is_active,
                'image_url' => asset('storage/' . $gallery->image_path)
            ]
        ]);
    }

    /**
     * Delete grid gallery
     */
    public function destroyGridGallery(Gallery $gallery)
    {
        // Delete main image file
        $this->deleteImage($gallery->image_path);

        // Delete all additional images
        foreach ($gallery->images as $image) {
            $this->deleteImage($image->image_path);
        }

        $gallery->delete();

        // Clear cache
        $this->clearGalleryCaches();

        return response()->json([
            'message' => 'Gallery berhasil dihapus'
        ]);
    }

    /**
     * Add additional image to existing gallery slot (max 3 total including main)
     */
    public function addImageToSlot(Request $request, Gallery $gallery)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Check max images (main + 2 additional = 3 total)
        $currentCount = $gallery->images()->count() + 1; // +1 for main image
        if ($currentCount >= 3) {
            return response()->json([
                'message' => 'Maksimal 3 foto per slot'
            ], 422);
        }

        // Upload image with WebP conversion
        $result = $this->uploadGalleryImage($request->file('image'));

        // Create gallery image record with title and description
        $galleryImage = $gallery->images()->create([
            'image_path' => $result['path'],
            'title' => $request->title,
            'description' => $request->description,
            'order' => $currentCount + 1
        ]);

        // Clear cache
        $this->clearGalleryCaches();

        return response()->json([
            'message' => 'Foto berhasil ditambahkan',
            'data' => [
                'id' => $galleryImage->id,
                'image_path' => $galleryImage->image_path,
                'image_url' => $galleryImage->image_url,
                'title' => $galleryImage->title,
                'description' => $galleryImage->description,
                'order' => $galleryImage->order
            ],
            'all_images' => $gallery->fresh()->all_images,
            'image_count' => count($gallery->fresh()->all_images)
        ], 201);
    }

    /**
     * Delete specific image from gallery slot
     */
    public function deleteSlotImage(Request $request, Gallery $gallery)
    {
        $imageId = $request->input('image_id');

        // If deleting main image
        if ($imageId === 'main') {
            // Check if there are additional images
            $firstAdditional = $gallery->images()->orderBy('order')->first();

            if ($firstAdditional) {
                // Promote first additional to main
                $this->deleteImage($gallery->image_path);
                $gallery->image_path = $firstAdditional->image_path;
                $gallery->save();
                $firstAdditional->delete();

                // Reorder remaining images
                $gallery->images()->get()->each(function ($img, $index) {
                    $img->update(['order' => $index + 1]);
                });
            } else {
                return response()->json([
                    'message' => 'Tidak bisa menghapus foto utama. Hapus slot untuk menghapus semua.'
                ], 422);
            }
        } else {
            // Delete additional image
            $image = $gallery->images()->find($imageId);
            if (!$image) {
                return response()->json([
                    'message' => 'Gambar tidak ditemukan'
                ], 404);
            }

            $this->deleteImage($image->image_path);
            $image->delete();

            // Reorder remaining images
            $gallery->images()->orderBy('order')->get()->each(function ($img, $index) {
                $img->update(['order' => $index + 1]);
            });
        }

        // Clear cache
        $this->clearGalleryCaches();

        return response()->json([
            'message' => 'Foto berhasil dihapus',
            'all_images' => $gallery->fresh()->all_images,
            'image_count' => count($gallery->fresh()->all_images)
        ]);
    }

    /**
     * Helper: Upload gallery image with WebP conversion and dimension tracking
     */
    private function uploadGalleryImage($file): array
    {
        $filename = time() . '_' . Str::random(10) . '.webp';
        $path = 'galleries/' . $filename;

        // Create image manager with GD driver
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);

        // Get original dimensions before resize
        $width = $image->width();
        $height = $image->height();

        // Determine orientation
        $orientation = $width > $height ? 'landscape' : ($width == $height ? 'square' : 'portrait');

        // Resize if larger than 1600px (gallery needs higher res)
        $image->scale(width: 1600, height: 1600);

        // Encode to WebP with 85% quality
        $encoded = $image->toWebp(85);

        // Save to public storage
        Storage::disk('public')->put($path, (string) $encoded);

        return [
            'path' => $path,
            'width' => $width,
            'height' => $height,
            'orientation' => $orientation
        ];
    }

    /**
     * Helper: Clear all gallery caches
     */
    private function clearGalleryCaches(): void
    {
        Cache::forget('galleries_public');
        Cache::forget('galleries_home'); // Also clear homepage cache
    }
}

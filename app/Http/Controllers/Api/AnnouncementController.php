<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Traits\ProcessesImages;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AnnouncementController extends Controller
{
    use ProcessesImages;
    use LogsActivity;

    /**
     * Get list of announcements (cached)
     */
    public function index()
    {
        $announcements = Cache::remember('announcements_all', 1800, function () {
            return Announcement::orderBy('priority', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
        });

        return response()->json([
            'success' => true,
            'data' => $announcements
        ]);
    }

    /**
     * Get single announcement
     */
    public function show($id)
    {
        $announcement = Announcement::find($id);
        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $announcement
        ]);
    }

    /**
     * Create announcement with WebP conversion
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'link' => 'nullable|url',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Upload image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadAnnouncementImage($request->file('image'));
        }

        $announcement = Announcement::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'link' => $request->link,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->is_active ?? true,
            'priority' => $request->priority ?? 0
        ]);

        // ✅ Log activity
        $this->logCreate('Pengumuman', $announcement);

        // Clear cache
        Cache::forget('announcements_all');

        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully',
            'data' => $announcement
        ], 201);
    }

    /**
     * Update announcement with WebP conversion
     */
    public function update(Request $request, $id)
    {
        $announcement = Announcement::find($id);
        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'link' => 'nullable|url',
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle image upload with WebP conversion
        if ($request->hasFile('image')) {
            // Delete old image
            $this->deleteImage($announcement->image);

            // Upload new image with WebP conversion
            $announcement->image = $this->uploadAnnouncementImage($request->file('image'));
        }

        // Store old values for logging
        $oldValues = $announcement->toArray();

        // Update fields
        $announcement->fill($request->except('image'));
        $announcement->save();

        // ✅ Log activity
        $this->logUpdate('Pengumuman', $announcement, $oldValues);

        // Clear cache
        Cache::forget('announcements_all');

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully',
            'data' => $announcement
        ]);
    }

    /**
     * Delete announcement
     */
    public function destroy($id)
    {
        $announcement = Announcement::find($id);
        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ], 404);
        }

        // ✅ Log activity before delete
        $this->logDelete('Pengumuman', $announcement);

        // Delete image
        $this->deleteImage($announcement->image);

        $announcement->delete();

        // Clear cache
        Cache::forget('announcements_all');

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully'
        ]);
    }

    /**
     * Get active announcement (for frontend)
     */
    public function getActive()
    {
        $announcement = Announcement::active()->first();

        return response()->json([
            'success' => true,
            'data' => $announcement
        ]);
    }

    /**
     * Helper: Upload announcement image with WebP conversion
     */
    private function uploadAnnouncementImage($file): string
    {
        $filename = time() . '_' . Str::random(10) . '.webp';
        $path = 'announcements/' . $filename;

        // Create image manager with GD driver
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);

        // Keep original aspect ratio, resize to max 1200px
        $image->scale(width: 1200, height: 1200);

        // Encode to WebP with 85% quality
        $encoded = $image->toWebp(85);

        // Save to public storage
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }
}
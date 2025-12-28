<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Traits\ProcessesImages;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AdminNewsController extends Controller
{
    use ProcessesImages;
    use LogsActivity;

    /**
     * Mengambil daftar berita dengan pagination dan filter
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $category = $request->get('category');
        $status = $request->get('status');

        $query = News::query()->latest('created_at');

        if ($search) {
            $query->search($search);
        }

        if ($category && $category !== 'all') {
            $query->byCategory($category);
        }

        if ($status === 'published') {
            $query->where('published', true);
        } elseif ($status === 'draft') {
            $query->where('published', false);
        }

        $news = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $news->map(function ($item) {
                return $this->formatNewsItem($item);
            }),
            'meta' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
                'from' => $news->firstItem(),
                'to' => $news->lastItem(),
            ]
        ]);
    }

    /**
     * Mengambil detail berita untuk edit
     */
    public function show($id)
    {
        $news = News::withTrashed()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->formatNewsItem($news, true)
        ]);
    }

    /**
     * Membuat berita baru dengan WebP conversion
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'category' => 'required|in:event,achievement,announcement,other',
            'author' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
            'gallery.*' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
            'is_featured' => 'boolean',
            'published' => 'boolean',
            'published_at' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->except(['image', 'gallery']);

            // Upload Gambar Utama dengan WebP conversion
            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadNewsImage($request->file('image'), 'news');
            }

            // Upload Galeri dengan WebP conversion
            if ($request->hasFile('gallery')) {
                $galleryPaths = [];
                foreach ($request->file('gallery') as $image) {
                    $galleryPaths[] = $this->uploadNewsImage($image, 'news/gallery');
                }
                $data['gallery'] = $galleryPaths;
            }

            // Generate Slug Otomatis
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            // Pastikan Slug Unik
            $data['slug'] = $this->generateUniqueSlug($data['slug']);

            $news = News::create($data);

            // ✅ Log activity
            $this->logCreate('Berita', $news);

            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil dibuat',
                'data' => $this->formatNewsItem($news, true)
            ], 201);

        } catch (\Exception $e) {
            // Rollback jika gagal
            if (isset($data['image'])) {
                Storage::delete($data['image']);
            }
            if (isset($data['gallery'])) {
                foreach ($data['gallery'] as $image) {
                    Storage::delete($image);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat berita: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update berita yang ada dengan WebP conversion
     */
    public function update(Request $request, $id)
    {
        $news = News::withTrashed()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'category' => 'required|in:event,achievement,announcement,other',
            'author' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
            'gallery.*' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
            'is_featured' => 'boolean',
            'published' => 'boolean',
            'published_at' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->except(['image', 'gallery', 'remove_image', 'remove_gallery']);

            // Update Gambar Utama dengan WebP conversion
            if ($request->hasFile('image')) {
                // Hapus gambar lama
                $this->deleteImage($news->image);
                $data['image'] = $this->uploadNewsImage($request->file('image'), 'news');
            }

            // Hapus Gambar Utama (Manual)
            if ($request->input('remove_image') && $news->image) {
                $this->deleteImage($news->image);
                $data['image'] = null;
            }

            // Tambah ke Galeri dengan WebP conversion
            if ($request->hasFile('gallery')) {
                $galleryPaths = $news->gallery ?? [];
                foreach ($request->file('gallery') as $image) {
                    $galleryPaths[] = $this->uploadNewsImage($image, 'news/gallery');
                }
                $data['gallery'] = $galleryPaths;
            }

            // Hapus item dari Galeri
            if ($request->has('remove_gallery')) {
                $removeGallery = $request->input('remove_gallery', []);
                $currentGallery = $news->gallery ?? [];

                foreach ($removeGallery as $imagePath) {
                    $this->deleteImage($imagePath);
                    $currentGallery = array_filter($currentGallery, function ($path) use ($imagePath) {
                        return $path !== $imagePath;
                    });
                }

                $data['gallery'] = array_values($currentGallery);
            }

            // Update Slug jika Title berubah
            if (isset($data['title']) && $data['title'] !== $news->title) {
                $newSlug = Str::slug($data['title']);
                if ($newSlug !== $news->slug) {
                    $data['slug'] = $this->generateUniqueSlug($newSlug, $news->id);
                }
            }

            // Store old values for logging
            $oldValues = $news->toArray();

            $news->update($data);

            // ✅ Log activity
            $this->logUpdate('Berita', $news, $oldValues);

            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil diupdate',
                'data' => $this->formatNewsItem($news, true)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate berita: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Soft Delete berita
     */
    public function destroy($id)
    {
        try {
            $news = News::withTrashed()->findOrFail($id);

            if ($news->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Berita sudah dihapus sebelumnya'
                ], 400);
            }

            // ✅ Log activity before delete
            $this->logDelete('Berita', $news);

            $news->delete();

            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil dihapus (soft delete)'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus berita: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil daftar berita di Trash
     */
    public function trash(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $news = News::onlyTrashed()
            ->latest('deleted_at')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $news->map(function ($item) {
                return $this->formatNewsItem($item);
            }),
            'meta' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
                'from' => $news->firstItem(),
                'to' => $news->lastItem(),
            ]
        ]);
    }

    /**
     * Restore berita dari Trash
     */
    public function restore($id)
    {
        try {
            $news = News::onlyTrashed()->findOrFail($id);
            $news->restore();

            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil dipulihkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulihkan berita: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Force Delete (Hapus Permanen)
     */
    public function forceDelete($id)
    {
        try {
            $news = News::onlyTrashed()->findOrFail($id);

            // Hapus file fisik gambar utama
            $this->deleteImage($news->image);

            // Hapus file fisik galeri
            if ($news->gallery && is_array($news->gallery)) {
                foreach ($news->gallery as $image) {
                    $this->deleteImage($image);
                }
            }

            $news->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil dihapus permanen'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus berita: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload content image for TipTap editor
     */
    public function uploadContentImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png,webp,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.webp';
            $path = 'news/content/' . $filename;

            // Create image manager with GD driver
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);

            // Resize if larger than 800px width (content images don't need to be huge)
            $image->scale(width: 800);

            // Encode to WebP with 85% quality
            $encoded = $image->toWebp(85);

            // Save to public storage
            Storage::disk('public')->put($path, (string) $encoded);

            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'path' => $path
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload gambar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Upload News Image with WebP conversion
     */
    private function uploadNewsImage($file, $folder = 'news'): string
    {
        $filename = time() . '_' . Str::random(10) . '.webp';
        $path = $folder . '/' . $filename;

        // Create image manager with GD driver
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);

        // Resize if larger than 1200px
        $image->scale(width: 1200, height: 1200);

        // Encode to WebP with 85% quality
        $encoded = $image->toWebp(85);

        // Save to public storage
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    /**
     * Generate unique slug for news article
     */
    private function generateUniqueSlug($slug, $excludeId = null)
    {
        $originalSlug = $slug;

        $existingSlugs = News::withTrashed()
            ->where('slug', 'like', $originalSlug . '%')
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->pluck('slug')
            ->toArray();

        if (!in_array($slug, $existingSlugs)) {
            return $slug;
        }

        $count = 1;
        while (in_array($originalSlug . '-' . $count, $existingSlugs)) {
            $count++;
        }

        return $originalSlug . '-' . $count;
    }

    /**
     * Helper: Format News Resource
     */
    private function formatNewsItem($news, $detailed = false)
    {
        $data = [
            'id' => $news->id,
            'title' => $news->title,
            'slug' => $news->slug,
            'excerpt' => $news->excerpt,
            'location' => $news->location,
            'image' => $news->image ? Storage::url($news->image) : null,
            'category' => $news->category,
            'author' => $news->author,
            'is_featured' => $news->is_featured,
            'published' => $news->published,
            'views' => $news->getRealViews(),
            'published_at' => $news->published_at?->format('Y-m-d H:i:s'),
            'created_at' => $news->created_at->format('Y-m-d H:i:s'),
            'deleted_at' => $news->deleted_at?->format('Y-m-d H:i:s'),
        ];

        if ($detailed) {
            $data['content'] = $news->content;
            $data['gallery'] = $news->gallery ? array_map(function ($img) {
                return Storage::url($img);
            }, $news->gallery) : [];
        }

        return $data;
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Traits\ProcessesImages;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminTestimonialController extends Controller
{
    use ProcessesImages;
    use LogsActivity;

    /**
     * Display a listing of all testimonials (including inactive)
     */
    public function index()
    {
        try {
            $testimonials = Testimonial::ordered()->get();

            return response()->json([
                'success' => true,
                'data' => $testimonials
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data testimoni'
            ], 500);
        }
    }

    /**
     * Store a newly created testimonial with WebP conversion
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'author' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'text' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
            'is_active' => 'boolean'
        ]);

        // Handle photo upload with WebP conversion
        if ($request->hasFile('photo')) {
            $validated['photo'] = $this->uploadImage(
                $request->file('photo'),
                'public/testimonials',
                800,
                800,
                85
            );
        }

        // Set order to last position if not provided
        if (!isset($validated['order'])) {
            $maxOrder = Testimonial::max('order') ?? -1;
            $validated['order'] = $maxOrder + 1;
        }

        // Set is_active default to true if not provided
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        $testimonial = Testimonial::create($validated);

        // ✅ Log activity
        $this->logCreate('Testimoni', $testimonial);

        return response()->json([
            'success' => true,
            'message' => 'Testimoni berhasil ditambahkan',
            'data' => $testimonial
        ], 201);
    }

    /**
     * Display the specified testimonial
     */
    public function show($id)
    {
        try {
            $testimonial = Testimonial::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $testimonial
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Testimoni tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update the specified testimonial with WebP conversion
     */
    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::findOrFail($id);

        $validated = $request->validate([
            'author' => 'sometimes|required|string|max:255',
            'role' => 'sometimes|required|string|max:255',
            'text' => 'sometimes|required|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
            'is_active' => 'boolean'
        ]);

        // Handle photo upload with WebP conversion
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            $this->deleteImage($testimonial->photo);

            $validated['photo'] = $this->uploadImage(
                $request->file('photo'),
                'public/testimonials',
                800,
                800,
                85
            );
        }

        // Handle photo removal if explicitly requested
        if ($request->has('remove_photo') && $request->remove_photo) {
            $this->deleteImage($testimonial->photo);
            $validated['photo'] = null;
        }

        // Store old values for logging
        $oldValues = $testimonial->toArray();

        $testimonial->update($validated);

        // ✅ Log activity
        $this->logUpdate('Testimoni', $testimonial, $oldValues);

        return response()->json([
            'success' => true,
            'message' => 'Testimoni berhasil diupdate',
            'data' => $testimonial
        ]);
    }

    /**
     * Remove the specified testimonial
     */
    public function destroy($id)
    {
        $testimonial = Testimonial::findOrFail($id);

        // ✅ Log activity before delete
        $this->logDelete('Testimoni', $testimonial);

        // Delete photo if exists
        $this->deleteImage($testimonial->photo);

        $testimonial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Testimoni berhasil dihapus'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->is_active = !$testimonial->is_active;
        $testimonial->save();

        return response()->json([
            'success' => true,
            'message' => 'Status testimoni berhasil diubah',
            'data' => $testimonial
        ]);
    }

    /**
     * Update order of testimonials (for drag & drop)
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:testimonials,id',
            'items.*.order' => 'required|integer|min:0'
        ]);

        try {
            foreach ($validated['items'] as $item) {
                Testimonial::where('id', $item['id'])
                    ->update(['order' => $item['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Urutan testimoni berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate urutan testimoni'
            ], 500);
        }
    }

    /**
     * Public testimonial submission (no auth required) with WebP conversion
     */
    public function submitPublic(Request $request)
    {
        $validated = $request->validate([
            'author' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'text' => 'required|string|min:10',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:5120'
        ]);

        // Handle photo upload with WebP conversion
        if ($request->hasFile('photo')) {
            $validated['photo'] = $this->uploadImage(
                $request->file('photo'),
                'public/testimonials',
                800,
                800,
                85
            );
        }

        // Set as inactive (pending review)
        $validated['is_active'] = false;

        // Set order to last position
        $maxOrder = Testimonial::max('order') ?? -1;
        $validated['order'] = $maxOrder + 1;

        $testimonial = Testimonial::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih! Testimoni Anda telah diterima dan akan ditinjau oleh admin.',
            'data' => $testimonial
        ], 201);
    }
}

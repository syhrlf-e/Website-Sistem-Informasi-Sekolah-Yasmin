<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prestasi;
use App\Traits\ProcessesImages;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PrestasiController extends Controller
{
    use ProcessesImages;
    use LogsActivity;

    /**
     * Get list of prestasi with optional filters (cached when no filters)
     */
    public function index(Request $request)
    {
        $hasFilters = $request->has(['kategori', 'tingkat', 'tahun']);

        if ($hasFilters) {
            $query = Prestasi::query();
            $query->filter($request->only(['kategori', 'tingkat', 'tahun']));
            $prestasi = $query->orderBy('tahun', 'desc')->limit(100)->get();
        } else {
            $prestasi = Cache::remember('prestasi_all', 3600, function () {
                return Prestasi::orderBy('tahun', 'desc')->limit(100)->get();
            });
        }

        return response()->json([
            'success' => true,
            'data' => $prestasi
        ]);
    }

    public function show($slug)
    {
        $prestasi = Prestasi::where('slug', $slug)->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $prestasi
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_prestasi' => 'required|string|max:255',
            'kategori' => 'required|in:Akademik,Olahraga,Seni',
            'tingkat' => 'required|in:Sekolah,Kota,Provinsi,Nasional,Internasional',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'peserta' => 'required|array',
            'peserta.*' => 'string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:2048',
            'deskripsi' => 'required|string'
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['nama_prestasi']);

        // Handle image upload with WebP conversion
        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadImage(
                $request->file('image'),
                'prestasi',
                1200,
                1200,
                85
            );
        }

        $prestasi = Prestasi::create($validated);

        // ✅ Log activity
        $this->logCreate('Prestasi', $prestasi);

        // Clear cache (both API and homepage cache)
        Cache::forget('prestasi_all');
        Cache::forget('prestasi_home');

        return response()->json([
            'success' => true,
            'message' => 'Prestasi berhasil ditambahkan',
            'data' => $prestasi
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $prestasi = Prestasi::findOrFail($id);

        $validated = $request->validate([
            'nama_prestasi' => 'sometimes|required|string|max:255',
            'kategori' => 'sometimes|required|in:Akademik,Olahraga,Seni',
            'tingkat' => 'sometimes|required|in:Sekolah,Kota,Provinsi,Nasional,Internasional',
            'tahun' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
            'peserta' => 'sometimes|required|array',
            'peserta.*' => 'string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:2048',
            'deskripsi' => 'sometimes|required|string'
        ]);

        // Update slug jika nama_prestasi berubah
        if (isset($validated['nama_prestasi'])) {
            $validated['slug'] = Str::slug($validated['nama_prestasi']);
        }

        // Handle image upload with WebP conversion
        if ($request->hasFile('image')) {
            // Delete old image
            $this->deleteImage($prestasi->image);

            $validated['image'] = $this->uploadImage(
                $request->file('image'),
                'prestasi',
                1200,
                1200,
                85
            );
        }

        // Store old values for logging
        $oldValues = $prestasi->toArray();

        $prestasi->update($validated);

        // ✅ Log activity
        $this->logUpdate('Prestasi', $prestasi, $oldValues);

        // Clear cache (both API and homepage cache)
        Cache::forget('prestasi_all');
        Cache::forget('prestasi_home');

        return response()->json([
            'success' => true,
            'message' => 'Prestasi berhasil diupdate',
            'data' => $prestasi
        ]);
    }

    public function destroy($id)
    {
        $prestasi = Prestasi::findOrFail($id);

        // ✅ Log activity before delete
        $this->logDelete('Prestasi', $prestasi);

        // Delete image
        $this->deleteImage($prestasi->image);

        $prestasi->delete();

        // Clear cache (both API and homepage cache)
        Cache::forget('prestasi_all');
        Cache::forget('prestasi_home');

        return response()->json([
            'success' => true,
            'message' => 'Prestasi berhasil dihapus'
        ]);
    }
}
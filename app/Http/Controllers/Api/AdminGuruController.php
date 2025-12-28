<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AdminGuruController extends Controller
{
    use LogsActivity;
    /**
     * Get list of guru with pagination and search
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = Guru::query()->latest('created_at');

        // Filter: Search
        if ($search) {
            $query->search($search);
        }

        $guru = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $guru->map(function ($item) {
                return $this->formatGuruItem($item);
            }),
            'meta' => [
                'current_page' => $guru->currentPage(),
                'last_page' => $guru->lastPage(),
                'per_page' => $guru->perPage(),
                'total' => $guru->total(),
                'from' => $guru->firstItem(),
                'to' => $guru->lastItem(),
            ]
        ]);
    }

    /**
     * Get single guru details
     */
    public function show($id)
    {
        $guru = Guru::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->formatGuruItem($guru)
        ]);
    }

    /**
     * Create new guru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'pelajaran' => 'required|string|max:255',
            'foto' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->only(['nama', 'pelajaran']);

            // Upload and convert to WebP
            if ($request->hasFile('foto')) {
                $data['foto'] = $this->uploadAndConvertToWebP($request->file('foto'));
            }

            $guru = Guru::create($data);

            // ✅ Log activity
            $this->logCreate('Guru', $guru);

            return response()->json([
                'success' => true,
                'message' => 'Guru berhasil ditambahkan',
                'data' => $this->formatGuruItem($guru)
            ], 201);

        } catch (\Exception $e) {
            // Rollback: Delete uploaded image if exists
            if (isset($data['foto'])) {
                Storage::delete($data['foto']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan guru: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update guru
     */
    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'pelajaran' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->only(['nama', 'pelajaran']);

            // Update photo if new one uploaded
            if ($request->hasFile('foto')) {
                // Delete old photo
                if ($guru->foto && Storage::exists($guru->foto)) {
                    Storage::delete($guru->foto);
                }

                $data['foto'] = $this->uploadAndConvertToWebP($request->file('foto'));
            }

            // Store old values for logging
            $oldValues = $guru->toArray();

            $guru->update($data);

            // ✅ Log activity
            $this->logUpdate('Guru', $guru, $oldValues);

            return response()->json([
                'success' => true,
                'message' => 'Guru berhasil diupdate',
                'data' => $this->formatGuruItem($guru)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate guru: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete guru
     */
    public function destroy($id)
    {
        try {
            $guru = Guru::findOrFail($id);

            // ✅ Log activity before delete
            $this->logDelete('Guru', $guru);

            // Delete photo file
            if ($guru->foto && Storage::exists($guru->foto)) {
                Storage::delete($guru->foto);
            }

            $guru->delete();

            return response()->json([
                'success' => true,
                'message' => 'Guru berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus guru: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Upload and convert image to WebP
     */
    private function uploadAndConvertToWebP($file)
    {
        // Generate unique filename
        $filename = time() . '_' . Str::random(10) . '.webp';
        $path = 'guru/' . $filename;

        // Create image manager with GD driver
        $manager = new ImageManager(new Driver());

        // Read and process image
        $image = $manager->read($file);

        // Resize if larger than 800x800 (maintain aspect ratio)
        $image->scale(width: 800, height: 800);

        // Encode to WebP with 85% quality
        $encoded = $image->toWebp(85);

        // Save to storage
        Storage::put($path, (string) $encoded);

        return $path;
    }

    /**
     * Helper: Format guru resource
     */
    private function formatGuruItem($guru)
    {
        return [
            'id' => $guru->id,
            'nama' => $guru->nama,
            'pelajaran' => $guru->pelajaran,
            'foto' => $guru->foto ? Storage::url($guru->foto) : null,
            'created_at' => $guru->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $guru->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}

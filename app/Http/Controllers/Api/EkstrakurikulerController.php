<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use App\Traits\ProcessesImages;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class EkstrakurikulerController extends Controller
{
    use ProcessesImages;
    use LogsActivity;

    /**
     * Get list of ekstrakurikuler (cached)
     */
    public function index(Request $request)
    {
        // Direct query without cache to avoid stale data issues
        $query = Ekstrakurikuler::withCount([
            'registrations as approved_count' => fn($q) => $q->where('status', 'approved')
        ]);

        if ($request->has('active')) {
            $query->where('is_active', $request->active);
        }

        $ekskul = $query->orderBy('created_at', 'asc')->limit(30)->get();

        return response()->json([
            'success' => true,
            'data' => $ekskul
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'badge' => 'required|in:Akademik,Olahraga,Seni,Kepemimpinan',
            'deskripsi' => 'required|string',
            'benefits' => 'nullable|array',
            'benefits.*' => 'string|max:255',
            'jadwal' => 'required|string|max:255',
            'pembina' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'max_peserta' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('gambar');

        // Handle image upload with WebP conversion
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $this->uploadImage(
                $request->file('gambar'),
                'public/ekstrakurikuler',
                1200,
                1200,
                85
            );
        }

        $ekskul = Ekstrakurikuler::create($data);

        // ✅ Log activity
        $this->logCreate('Ekstrakurikuler', $ekskul);

        // Clear all ekstrakurikuler caches
        $this->clearEkskulCaches();

        return response()->json([
            'success' => true,
            'message' => 'Ekstrakurikuler berhasil ditambahkan',
            'data' => $ekskul
        ], 201);
    }

    public function show($id)
    {
        $ekskul = Ekstrakurikuler::find($id);
        if (!$ekskul) {
            return response()->json([
                'success' => false,
                'message' => 'Ekstrakurikuler tidak ditemukan'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $ekskul
        ]);
    }

    public function update(Request $request, $id)
    {
        $ekskul = Ekstrakurikuler::find($id);
        if (!$ekskul) {
            return response()->json([
                'success' => false,
                'message' => 'Ekstrakurikuler tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'badge' => 'required|in:Akademik,Olahraga,Seni,Kepemimpinan',
            'deskripsi' => 'required|string',
            'benefits' => 'nullable|array',
            'benefits.*' => 'string|max:255',
            'jadwal' => 'required|string|max:255',
            'pembina' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'max_peserta' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('gambar');

        // Handle image upload with WebP conversion
        if ($request->hasFile('gambar')) {
            // Delete old image
            $this->deleteImage($ekskul->gambar);

            $data['gambar'] = $this->uploadImage(
                $request->file('gambar'),
                'public/ekstrakurikuler',
                1200,
                1200,
                85
            );
        }

        // Store old values for logging
        $oldValues = $ekskul->toArray();

        $ekskul->update($data);

        // ✅ Log activity
        $this->logUpdate('Ekstrakurikuler', $ekskul, $oldValues);

        // Clear all ekstrakurikuler caches
        $this->clearEkskulCaches();

        return response()->json([
            'success' => true,
            'message' => 'Ekstrakurikuler berhasil diupdate',
            'data' => $ekskul->fresh()
        ]);
    }

    public function destroy($id)
    {
        $ekskul = Ekstrakurikuler::find($id);
        if (!$ekskul) {
            return response()->json([
                'success' => false,
                'message' => 'Ekstrakurikuler tidak ditemukan'
            ], 404);
        }

        // ✅ Log activity before delete
        $this->logDelete('Ekstrakurikuler', $ekskul);

        // Delete image
        $this->deleteImage($ekskul->gambar);

        $ekskul->delete();

        // Clear all ekstrakurikuler caches
        $this->clearEkskulCaches();

        return response()->json([
            'success' => true,
            'message' => 'Ekstrakurikuler berhasil dihapus'
        ]);
    }

    /**
     * Generate registration token for ekstrakurikuler
     */
    public function generateToken($id)
    {
        $ekskul = Ekstrakurikuler::find($id);

        if (!$ekskul) {
            return response()->json([
                'success' => false,
                'message' => 'Ekstrakurikuler tidak ditemukan'
            ], 404);
        }

        // Check if can generate token (24-hour cooldown)
        if (!$ekskul->canGenerateToken()) {
            $nextGenerationTime = $ekskul->token_generated_at->addHours(24);
            $hoursRemaining = now()->diffInHours($nextGenerationTime, false);

            return response()->json([
                'success' => false,
                'message' => 'Token hanya dapat di-generate setiap 24 jam',
                'can_generate_at' => $nextGenerationTime->toIso8601String(),
                'hours_remaining' => ceil($hoursRemaining)
            ], 429);
        }

        // Generate token
        $words = [
            'TIGER',
            'EAGLE',
            'WOLF',
            'LION',
            'HAWK',
            'BEAR',
            'FOX',
            'SHARK',
            'DRAGON',
            'PHOENIX',
            'FALCON',
            'PANTHER',
            'COBRA',
            'VIPER',
            'RAVEN',
            'THUNDER',
            'STORM',
            'BLAZE',
            'FROST',
            'SHADOW',
            'LIGHT',
            'STAR',
            'MOON',
            'SUN',
            'WIND',
            'FIRE',
            'WATER',
            'EARTH',
            'SKY',
            'OCEAN'
        ];

        $ekskulName = strtoupper(str_replace(' ', '', substr($ekskul->nama, 0, 10)));
        $randomWord = $words[array_rand($words)];
        $randomNumber = rand(10, 99);
        $token = "{$ekskulName}-{$randomWord}-{$randomNumber}";

        // Update ekstrakurikuler with new token and timestamps
        $ekskul->update([
            'registration_token' => $token,
            'token_generated_at' => now(),
            'token_expires_at' => now()->addHours(24)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token berhasil di-generate',
            'data' => [
                'token' => $token,
                'generated_at' => $ekskul->token_generated_at->toIso8601String(),
                'expires_at' => $ekskul->token_expires_at->toIso8601String(),
                'can_generate_next_at' => $ekskul->token_generated_at->addHours(24)->toIso8601String()
            ]
        ]);
    }

    /**
     * Toggle registration status for ekstrakurikuler
     */
    public function toggleRegistration($id)
    {
        $ekskul = Ekstrakurikuler::find($id);

        if (!$ekskul) {
            return response()->json([
                'success' => false,
                'message' => 'Ekstrakurikuler tidak ditemukan'
            ], 404);
        }

        // If trying to open registration, check if deadline has passed
        if (!$ekskul->enable_registration && $ekskul->isDeadlinePassed()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat membuka pendaftaran karena batas waktu sudah lewat',
                'deadline' => $ekskul->registration_deadline->format('d M Y H:i')
            ], 422);
        }

        // Toggle the registration status
        $ekskul->enable_registration = !$ekskul->enable_registration;
        $ekskul->save();

        // Clear all ekstrakurikuler caches
        $this->clearEkskulCaches();

        return response()->json([
            'success' => true,
            'message' => $ekskul->enable_registration
                ? 'Pendaftaran dibuka'
                : 'Pendaftaran ditutup',
            'data' => $ekskul->fresh()
        ]);
    }

    /**
     * Clear all ekstrakurikuler caches
     */
    private function clearEkskulCaches(): void
    {
        Cache::forget('ekskul_home');
        Cache::forget('ekskul_list_all');
        Cache::forget('ekskul_list_1');
        Cache::forget('ekskul_list_0');
    }
}
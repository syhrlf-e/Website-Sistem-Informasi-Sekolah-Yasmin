<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEkstrakurikulerRegistrationRequest;
use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EkstrakurikulerRegistrationController extends Controller
{
    /**
     * Store a new registration with race condition prevention
     */
    public function store(StoreEkstrakurikulerRegistrationRequest $request): JsonResponse
    {
        try {
            // Use database transaction with row locking to prevent race condition
            $result = DB::transaction(function () use ($request) {
                // Lock the ekstrakurikuler row to prevent concurrent slot checks
                $ekstrakurikuler = Ekstrakurikuler::where('id', $request->ekstrakurikuler_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Check if registration is still open (slot available)
                if ($ekstrakurikuler->is_slot_full) {
                    return [
                        'success' => false,
                        'message' => 'Maaf, slot pendaftaran sudah penuh.',
                        'status' => 400
                    ];
                }

                $registration = EkstrakurikulerRegistration::create([
                    'ekstrakurikuler_id' => $request->ekstrakurikuler_id,
                    'nama_lengkap' => $request->nama_lengkap,
                    'kelas' => $request->kelas,
                    'email' => $request->email,
                    'no_whatsapp' => $request->no_whatsapp,
                    'alasan_bergabung' => $request->alasan_bergabung,
                    'registration_token' => $request->registration_token,
                    'status' => 'pending',
                ]);

                // Load relationship for response
                $registration->load('ekstrakurikuler');

                return [
                    'success' => true,
                    'message' => 'Pendaftaran berhasil! Silakan tunggu konfirmasi dari admin.',
                    'data' => [
                        'id' => $registration->id,
                        'ekstrakurikuler' => $registration->ekstrakurikuler->nama,
                        'nama_lengkap' => $registration->nama_lengkap,
                        'status' => $registration->status,
                        'created_at' => $registration->created_at->format('d M Y H:i'),
                    ],
                    'status' => 201
                ];
            });

            // Return response based on transaction result
            return response()->json(
                collect($result)->except('status')->toArray(),
                $result['status']
            );

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pendaftaran',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error',
            ], 500);
        }
    }
}
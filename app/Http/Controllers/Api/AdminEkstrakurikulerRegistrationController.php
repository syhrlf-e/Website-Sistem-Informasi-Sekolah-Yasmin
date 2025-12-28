<?php

namespace App\Http\Controllers\Api;

use App\Events\EkskulSlotUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveRegistrationRequest;
use App\Http\Requests\RejectRegistrationRequest;
use App\Mail\EkskulApprovalMail;
use App\Models\EkstrakurikulerRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
class AdminEkstrakurikulerRegistrationController extends Controller
{
    /**
     * Get all registrations with filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = EkstrakurikulerRegistration::with(['ekstrakurikuler', 'approver']);
            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
            // Filter by ekstrakurikuler
            if ($request->has('ekstrakurikuler_id')) {
                $query->where('ekstrakurikuler_id', $request->ekstrakurikuler_id);
            }
            // Search
            if ($request->has('search') && $request->search) {
                $query->search($request->search);
            }
            // Sort by latest
            $query->orderBy('created_at', 'desc');
            // Paginate
            $perPage = $request->get('per_page', 15);
            $registrations = $query->paginate($perPage);
            // Transform data
            $registrations->getCollection()->transform(function ($registration) {
                return [
                    'id' => $registration->id,
                    'nama_lengkap' => $registration->nama_lengkap,
                    'kelas' => $registration->kelas,
                    'email' => $registration->email,
                    'no_whatsapp' => $registration->no_whatsapp,
                    'alasan_bergabung' => $registration->alasan_bergabung,
                    'ekstrakurikuler' => [
                        'id' => $registration->ekstrakurikuler->id,
                        'nama' => $registration->ekstrakurikuler->nama,
                    ],
                    'status' => $registration->status,
                    'admin_notes' => $registration->admin_notes,
                    'approved_by' => $registration->approver ? $registration->approver->name : null,
                    'approved_at' => $registration->approved_at ? $registration->approved_at->format('d M Y H:i') : null,
                    'created_at' => $registration->created_at->format('d M Y H:i'),
                    'created_at_relative' => $registration->created_at->diffForHumans(),
                ];
            });
            return response()->json([
                'success' => true,
                'data' => $registrations->items(),
                'meta' => [
                    'current_page' => $registrations->currentPage(),
                    'last_page' => $registrations->lastPage(),
                    'per_page' => $registrations->perPage(),
                    'total' => $registrations->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pendaftar',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Get single registration detail
     */
    public function show($id): JsonResponse
    {
        try {
            $registration = EkstrakurikulerRegistration::with(['ekstrakurikuler', 'approver'])
                ->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $registration->id,
                    'nama_lengkap' => $registration->nama_lengkap,
                    'kelas' => $registration->kelas,
                    'email' => $registration->email,
                    'no_whatsapp' => $registration->no_whatsapp,
                    'alasan_bergabung' => $registration->alasan_bergabung,
                    'ekstrakurikuler' => [
                        'id' => $registration->ekstrakurikuler->id,
                        'nama' => $registration->ekstrakurikuler->nama,
                        'kategori' => $registration->ekstrakurikuler->kategori,
                    ],
                    'status' => $registration->status,
                    'admin_notes' => $registration->admin_notes,
                    'approved_by' => $registration->approver ? $registration->approver->name : null,
                    'approved_at' => $registration->approved_at ? $registration->approved_at->format('d M Y H:i') : null,
                    'created_at' => $registration->created_at->format('d M Y, H:i'),
                    'created_at_relative' => $registration->created_at->diffForHumans(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftar tidak ditemukan',
            ], 404);
        }
    }
    public function approve($id, ApproveRegistrationRequest $request): JsonResponse
    {
        try {
            $registration = EkstrakurikulerRegistration::with('ekstrakurikuler')->findOrFail($id);

            if (!$registration->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pendaftaran sudah diproses sebelumnya',
                ], 400);
            }

            // Check if slot is still available
            $ekskul = $registration->ekstrakurikuler;
            if ($ekskul->max_participants && $ekskul->is_slot_full) {
                return response()->json([
                    'success' => false,
                    'message' => 'Slot sudah penuh! Tidak bisa menyetujui pendaftaran lagi. (Maks: ' . $ekskul->max_participants . ' peserta)',
                ], 400);
            }

            $registration->update([
                'status' => 'approved',
                'admin_notes' => $request->admin_notes,
                // 'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Clear caches to reflect new count immediately
            \Cache::forget('dashboard_stats');
            \Cache::forget('ekskul_home');
            \Cache::forget('ekskul_list_all');
            \Cache::forget('ekskul_list_1');
            \Cache::forget('ekskul_list_0');

            // Broadcast real-time update to public page (fail silently if Pusher is not configured)
            try {
                $ekskul->refresh(); // Refresh to get latest counts
                event(new EkskulSlotUpdated($ekskul));
            } catch (\Exception $e) {
                \Log::warning('Pusher broadcast failed: ' . $e->getMessage());
            }

            // Send approval email notification (fail silently if email fails)
            try {
                if ($registration->email) {
                    Mail::to($registration->email)->send(
                        new EkskulApprovalMail($registration, $ekskul)
                    );
                    \Log::info('Approval email sent to: ' . $registration->email);
                }
            } catch (\Exception $e) {
                \Log::warning('Approval email failed: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil disetujui',
                'data' => [
                    'id' => $registration->id,
                    'status' => $registration->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui pendaftaran',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function reject($id, RejectRegistrationRequest $request): JsonResponse
    {
        try {
            $registration = EkstrakurikulerRegistration::findOrFail($id);
            if (!$registration->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pendaftaran sudah diproses sebelumnya',
                ], 400);
            }
            $registration->update([
                'status' => 'rejected',
                'admin_notes' => $request->admin_notes,
                // 'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Clear caches to reflect new count immediately
            \Cache::forget('dashboard_stats');
            \Cache::forget('ekskul_home');
            \Cache::forget('ekskul_list_all');
            \Cache::forget('ekskul_list_1');
            \Cache::forget('ekskul_list_0');

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil ditolak',
                'data' => [
                    'id' => $registration->id,
                    'status' => $registration->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak pendaftaran',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Delete registration
     */
    public function destroy($id): JsonResponse
    {
        try {
            $registration = EkstrakurikulerRegistration::findOrFail($id);
            $registration->delete();
            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pendaftaran',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

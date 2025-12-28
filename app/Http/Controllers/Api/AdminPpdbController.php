<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PpdbRegistration;
use App\Models\PpdbWave;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * AdminPpdbController
 * 
 * Handles admin operations for PPDB registrations (pendaftar)
 * Protected by admin middleware
 */
class AdminPpdbController extends Controller
{
    use LogsActivity;

    /**
     * Dashboard stats for PPDB
     */
    public function dashboard()
    {
        $totalRegistrations = PpdbRegistration::count();
        $pendingCount = PpdbRegistration::where('status', 'pending')->count();
        $verifiedCount = PpdbRegistration::where('status', 'verified')->count();
        $selectionCount = PpdbRegistration::where('status', 'selection')->count();
        $acceptedCount = PpdbRegistration::where('status', 'accepted')->count();
        $rejectedCount = PpdbRegistration::where('status', 'rejected')->count();

        $activeWaves = PpdbWave::open()->count();
        $totalWaves = PpdbWave::count();

        // Get registrations by wave
        $waveStats = PpdbWave::withCount('registrations')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Recent registrations
        $recentRegistrations = PpdbRegistration::with('wave:id,name')
            ->select('id', 'registration_number', 'nama_lengkap', 'status', 'wave_id', 'created_at')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $totalRegistrations,
                'by_status' => [
                    'pending' => $pendingCount,
                    'verified' => $verifiedCount,
                    'selection' => $selectionCount,
                    'accepted' => $acceptedCount,
                    'rejected' => $rejectedCount,
                ],
                'waves' => [
                    'active' => $activeWaves,
                    'total' => $totalWaves,
                ],
                'wave_stats' => $waveStats,
                'recent' => $recentRegistrations,
            ]
        ]);
    }

    /**
     * List all registrations with filters
     */
    public function index(Request $request)
    {
        $query = PpdbRegistration::with('wave:id,name');

        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->withStatus($request->status);
        }

        // Filter by wave
        if ($request->wave_id) {
            $query->forWave($request->wave_id);
        }

        // Search
        if ($request->search) {
            $query->search($request->search);
        }

        // Sort
        $sortField = $request->sort ?? 'created_at';
        $sortOrder = $request->order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        $registrations = $query->paginate($request->per_page ?? 15);

        // Transform for list view (minimal data)
        $registrations->getCollection()->transform(function ($reg) {
            return [
                'id' => $reg->id,
                'registration_number' => $reg->registration_number,
                'nama_lengkap' => $reg->nama_lengkap,
                'jenis_kelamin' => $reg->jenis_kelamin,
                'asal_sekolah' => $reg->asal_sekolah,
                'jurusan_pilihan' => $reg->jurusan_pilihan,
                'status' => $reg->status,
                'status_label' => $reg->status_label,
                'status_color' => $reg->status_color,
                'wave' => $reg->wave,
                'created_at' => $reg->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $registrations
        ]);
    }

    /**
     * Get single registration with full details
     */
    public function show(PpdbRegistration $registration)
    {
        $registration->load(['wave', 'verifier:id,name']);

        return response()->json([
            'success' => true,
            'data' => $registration
        ]);
    }

    /**
     * Update registration status
     */
    public function updateStatus(Request $request, PpdbRegistration $registration)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,verified,selection,accepted,rejected',
            'catatan_admin' => 'nullable|string',
        ]);

        $oldStatus = $registration->status;

        $registration->updateStatus(
            $validated['status'],
            $request->user()->id
        );

        if (isset($validated['catatan_admin'])) {
            $registration->catatan_admin = $validated['catatan_admin'];
            $registration->save();
        }

        $this->logUpdate('PpdbRegistration', $registration, ['status' => $oldStatus]);

        // Send email notification if email exists and status actually changed
        if ($registration->email && $oldStatus !== $validated['status']) {
            try {
                \Illuminate\Support\Facades\Mail::to($registration->email)
                    ->send(new \App\Mail\PpdbStatusUpdateMail($registration, $oldStatus));
            } catch (\Exception $e) {
                \Log::warning('Failed to send PPDB status update email: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diupdate!',
            'data' => [
                'id' => $registration->id,
                'status' => $registration->status,
                'status_label' => $registration->status_label,
            ]
        ]);
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ppdb_registrations,id',
            'status' => 'required|in:pending,verified,selection,accepted,rejected',
        ]);

        $userId = $request->user()->id;
        $count = 0;

        foreach ($validated['ids'] as $id) {
            $registration = PpdbRegistration::find($id);
            if ($registration) {
                $registration->updateStatus($validated['status'], $userId);
                $count++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$count} pendaftar berhasil diupdate!",
        ]);
    }

    /**
     * Delete registration
     */
    public function destroy(PpdbRegistration $registration)
    {
        // Delete associated files if any
        if ($registration->foto_path) {
            Storage::delete($registration->foto_path);
        }
        if ($registration->dokumen_path) {
            Storage::delete($registration->dokumen_path);
        }

        $this->logDelete('PpdbRegistration', $registration);
        $registration->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pendaftar berhasil dihapus!'
        ]);
    }

    /**
     * Export registrations to Excel/CSV
     * TODO: Implement with Laravel Excel package
     */
    public function export(Request $request)
    {
        // Placeholder for export functionality
        return response()->json([
            'success' => false,
            'message' => 'Export feature coming soon!'
        ], 501);
    }

    /**
     * Get status options for dropdown
     */
    public function getStatusOptions()
    {
        return response()->json([
            'success' => true,
            'data' => [
                ['value' => 'pending', 'label' => 'Menunggu Verifikasi', 'color' => 'yellow'],
                ['value' => 'verified', 'label' => 'Terverifikasi', 'color' => 'blue'],
                ['value' => 'selection', 'label' => 'Tahap Seleksi', 'color' => 'purple'],
                ['value' => 'accepted', 'label' => 'Diterima', 'color' => 'green'],
                ['value' => 'rejected', 'label' => 'Ditolak', 'color' => 'red'],
            ]
        ]);
    }

    /**
     * Get PPDB landing page settings
     */
    public function getLandingSettings()
    {
        $biayaFormulir = \App\Models\Setting::get('ppdb_biaya_formulir', '250000');
        $sppBulanan = \App\Models\Setting::get('ppdb_spp_bulanan', '850000');
        $persyaratan = \App\Models\Setting::get('ppdb_persyaratan');
        $jadwalTambahan = \App\Models\Setting::get('ppdb_jadwal_tambahan');

        // Parse persyaratan JSON, fallback to default if empty
        $persyaratanList = $persyaratan ? json_decode($persyaratan, true) : [
            'Scan Rapor Semester 1-5 (SMP/MTs)',
            'Scan Akta Kelahiran & Kartu Keluarga',
            'Pas Foto Terbaru (4x6)',
            'Surat Keterangan Lulus (SKL)',
            'Sertifikat Prestasi (Jika Ada)'
        ];

        // Parse jadwal tambahan JSON
        $jadwalTambahanList = $jadwalTambahan ? json_decode($jadwalTambahan, true) : [];

        return response()->json([
            'success' => true,
            'data' => [
                'biaya_formulir' => $biayaFormulir,
                'spp_bulanan' => $sppBulanan,
                'persyaratan' => $persyaratanList,
                'jadwal_tambahan' => $jadwalTambahanList
            ]
        ]);
    }

    /**
     * Update PPDB landing page settings
     */
    public function updateLandingSettings(Request $request)
    {
        $validated = $request->validate([
            'biaya_formulir' => 'required|numeric|min:0',
            'spp_bulanan' => 'required|numeric|min:0',
            'persyaratan' => 'required|array',
            'persyaratan.*' => 'required|string|max:255',
            'jadwal_tambahan' => 'nullable|array',
            'jadwal_tambahan.*.label' => 'nullable|string|max:255',
            'jadwal_tambahan.*.value' => 'nullable|string|max:255',
        ]);

        \App\Models\Setting::set('ppdb_biaya_formulir', $validated['biaya_formulir']);
        \App\Models\Setting::set('ppdb_spp_bulanan', $validated['spp_bulanan']);
        \App\Models\Setting::set('ppdb_persyaratan', json_encode($validated['persyaratan']));
        \App\Models\Setting::set('ppdb_jadwal_tambahan', json_encode($validated['jadwal_tambahan'] ?? []));

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan berhasil disimpan!',
            'data' => [
                'biaya_formulir' => $validated['biaya_formulir'],
                'spp_bulanan' => $validated['spp_bulanan'],
                'persyaratan' => $validated['persyaratan'],
                'jadwal_tambahan' => $validated['jadwal_tambahan'] ?? []
            ]
        ]);
    }
}

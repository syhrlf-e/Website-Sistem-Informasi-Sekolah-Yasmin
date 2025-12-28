<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PpdbWave;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

/**
 * AdminPpdbWaveController
 * 
 * Handles CRUD operations for PPDB waves (gelombang pendaftaran)
 * Protected by admin middleware
 */
class AdminPpdbWaveController extends Controller
{
    use LogsActivity;

    /**
     * List all waves with registration counts
     */
    public function index(Request $request)
    {
        $query = PpdbWave::withCount('registrations');

        // Filter by academic year
        if ($request->academic_year) {
            $query->forAcademicYear($request->academic_year);
        }

        // Filter by status
        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $waves = $query->orderBy('start_date', 'desc')->paginate(10);

        // Add computed attributes
        $waves->getCollection()->transform(function ($wave) {
            $wave->is_open = $wave->isOpen();
            $wave->status_counts = $wave->status_counts;
            $wave->available_slots = $wave->available_slots;
            return $wave;
        });

        return response()->json([
            'success' => true,
            'data' => $waves
        ]);
    }

    /**
     * Create new wave
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'quota' => 'nullable|integer|min:1',
            'fee' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $wave = PpdbWave::create($validated);

        $this->logCreate('PpdbWave', $wave);

        return response()->json([
            'success' => true,
            'message' => 'Gelombang pendaftaran berhasil dibuat!',
            'data' => $wave
        ], 201);
    }

    /**
     * Get single wave with details
     */
    public function show(PpdbWave $wave)
    {
        $wave->loadCount('registrations');
        $wave->is_open = $wave->isOpen();
        $wave->status_counts = $wave->status_counts;
        $wave->available_slots = $wave->available_slots;

        return response()->json([
            'success' => true,
            'data' => $wave
        ]);
    }

    /**
     * Update wave
     */
    public function update(Request $request, PpdbWave $wave)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'quota' => 'nullable|integer|min:1',
            'fee' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $oldValues = $wave->toArray();
        $wave->update($validated);

        $this->logUpdate('PpdbWave', $wave, $oldValues);

        return response()->json([
            'success' => true,
            'message' => 'Gelombang pendaftaran berhasil diupdate!',
            'data' => $wave
        ]);
    }

    /**
     * Delete wave (only if no registrations)
     */
    public function destroy(PpdbWave $wave)
    {
        // Prevent deletion if wave has registrations
        if ($wave->registrations()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa menghapus gelombang yang sudah memiliki pendaftar.'
            ], 422);
        }

        $this->logDelete('PpdbWave', $wave);
        $wave->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gelombang pendaftaran berhasil dihapus!'
        ]);
    }

    /**
     * Toggle wave active status
     */
    public function toggleActive(PpdbWave $wave)
    {
        $oldValues = $wave->toArray();
        $wave->is_active = !$wave->is_active;
        $wave->save();

        $this->logUpdate('PpdbWave', $wave, $oldValues);

        return response()->json([
            'success' => true,
            'message' => $wave->is_active
                ? 'Gelombang diaktifkan!'
                : 'Gelombang dinonaktifkan!',
            'data' => $wave
        ]);
    }

    /**
     * Get currently open wave for registration
     */
    public function getOpenWave()
    {
        $wave = PpdbWave::open()->first();

        if (!$wave) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada gelombang pendaftaran yang sedang dibuka.',
                'data' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $wave
        ]);
    }
}

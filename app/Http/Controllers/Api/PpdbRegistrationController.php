<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PpdbRegistration;
use App\Models\PpdbWave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * PpdbRegistrationController
 * 
 * Handles public-facing PPDB registration operations:
 * - Submit new registration
 * - Check registration status
 * - Get active wave info
 */
class PpdbRegistrationController extends Controller
{
    /**
     * Get currently active wave for registration
     */
    public function getActiveWave()
    {
        $wave = PpdbWave::open()->first();

        if (!$wave) {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran belum dibuka.',
                'data' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $wave->id,
                'name' => $wave->name,
                'academic_year' => $wave->academic_year,
                'start_date' => $wave->start_date->format('Y-m-d'),
                'end_date' => $wave->end_date->format('Y-m-d'),
                'description' => $wave->description,
                'quota' => $wave->quota,
                'available_slots' => $wave->available_slots,
                'is_quota_full' => $wave->isQuotaFull(),
            ]
        ]);
    }

    /**
     * Submit new PPDB registration
     */
    public function store(Request $request)
    {
        // Check if there's an active wave
        $wave = PpdbWave::open()->first();

        if (!$wave) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, pendaftaran belum dibuka atau sudah ditutup.'
            ], 422);
        }

        // Check quota
        if ($wave->isQuotaFull()) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, kuota pendaftaran untuk gelombang ini sudah penuh.'
            ], 422);
        }

        // Validate input
        $validator = Validator::make($request->all(), $this->getValidationRules());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate NIK
        if (PpdbRegistration::where('nik', $request->nik)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'NIK sudah terdaftar.',
                'errors' => ['nik' => ['NIK sudah terdaftar dalam sistem.']]
            ], 422);
        }

        // Check for duplicate NISN (if provided)
        if ($request->nisn && PpdbRegistration::where('nisn', $request->nisn)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'NISN sudah terdaftar.',
                'errors' => ['nisn' => ['NISN sudah terdaftar dalam sistem.']]
            ], 422);
        }

        // Create registration
        $data = $validator->validated();
        $data['wave_id'] = $wave->id;
        $data['status'] = 'pending';

        $registration = PpdbRegistration::create($data);

        // Send confirmation email if email is provided
        if ($registration->email) {
            try {
                \Illuminate\Support\Facades\Mail::to($registration->email)
                    ->send(new \App\Mail\PpdbRegistrationMail($registration));
            } catch (\Exception $e) {
                // Log error but don't fail registration
                \Log::warning('Failed to send PPDB registration email: ' . $e->getMessage());
            }
        }

        // Return success with credentials
        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil!',
            'data' => [
                'registration_number' => $registration->registration_number,
                'token' => $registration->token,
                'nama' => $registration->nama_lengkap,
                'wave' => $wave->name,
            ]
        ], 201);
    }

    /**
     * Check registration status
     */
    public function checkStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'registration_number' => 'required|string',
            'token' => 'required|string|size:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $registration = PpdbRegistration::verifyCredentials(
            $request->registration_number,
            $request->token
        );

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor registrasi atau token tidak valid.'
            ], 404);
        }

        $registration->load('wave:id,name,academic_year');

        return response()->json([
            'success' => true,
            'data' => [
                'registration_number' => $registration->registration_number,
                'nama_lengkap' => $registration->nama_lengkap,
                'status' => $registration->status,
                'status_label' => $registration->status_label,
                'status_color' => $registration->status_color,
                'wave' => $registration->wave,
                'jurusan_pilihan' => $registration->jurusan_pilihan,
                'asal_sekolah' => $registration->asal_sekolah,
                'registered_at' => $registration->created_at->format('d F Y H:i'),
                'catatan_admin' => $registration->catatan_admin,
            ]
        ]);
    }

    /**
     * Find registration by name and NISN (for users who forgot credentials)
     */
    public function findByNameAndNisn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|min:3',
            'nisn' => 'required|string|size:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $registration = PpdbRegistration::where('nisn', $request->nisn)
            ->whereRaw('LOWER(nama_lengkap) LIKE ?', ['%' . strtolower($request->nama) . '%'])
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Data pendaftaran tidak ditemukan. Pastikan nama dan NISN sudah benar.'
            ], 404);
        }

        $registration->load('wave:id,name,academic_year');

        return response()->json([
            'success' => true,
            'data' => [
                'registration_number' => $registration->registration_number,
                'token' => $registration->token,
                'nama_lengkap' => $registration->nama_lengkap,
                'status' => $registration->status,
                'status_label' => $registration->status_label,
                'status_color' => $registration->status_color,
                'wave' => $registration->wave,
                'jurusan_pilihan' => $registration->jurusan_pilihan,
                'asal_sekolah' => $registration->asal_sekolah,
                'registered_at' => $registration->created_at->format('d F Y H:i'),
                'catatan_admin' => $registration->catatan_admin,
            ]
        ]);
    }

    /**
     * Get validation rules for registration form
     */
    private function getValidationRules(): array
    {
        return [
            // Identitas Diri
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'required|string|size:16',
            'nisn' => 'nullable|string|size:10',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'kewarganegaraan' => 'nullable|string|max:50',
            'anak_ke' => 'required|integer|min:1',
            'jumlah_saudara' => 'required|integer|min:0',
            'no_hp' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'hobi' => 'nullable|string|max:255',
            'cita_cita' => 'nullable|string|max:255',
            'prestasi_akademik' => 'nullable|string|max:500',
            'prestasi_non_akademik' => 'nullable|string|max:500',

            // Tempat Tinggal
            'alamat_lengkap' => 'required|string',
            'rt' => 'required|string|max:5',
            'rw' => 'required|string|max:5',
            'kelurahan' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kota_kabupaten' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'kode_pos' => 'nullable|string|max:10',

            // Jurusan
            'jurusan_pilihan' => 'required|in:IPA,IPS',

            // Kesehatan
            'golongan_darah' => 'nullable|in:A,B,O,AB',
            'tinggi_badan' => 'nullable|numeric|min:50|max:250',
            'berat_badan' => 'nullable|numeric|min:20|max:200',
            'riwayat_penyakit' => 'nullable|string',
            'alergi' => 'boolean',
            'keterangan_alergi' => 'nullable|string',

            // Pendidikan
            'asal_sekolah' => 'required|string|max:255',
            'npsn_sekolah' => 'nullable|string|max:20',
            'alamat_sekolah' => 'nullable|string',
            'tahun_lulus' => 'required|digits:4|integer|min:2020|max:' . (date('Y') + 1),

            // Ayah
            'nama_ayah' => 'required|string|max:255',
            'nik_ayah' => 'nullable|string|size:16',
            'pekerjaan_ayah' => 'required|string|max:100',
            'pendidikan_ayah' => 'required|string|max:50',
            'penghasilan_ayah' => 'nullable|string|max:50',
            'no_hp_ayah' => 'nullable|string|max:20',
            'ayah_masih_hidup' => 'boolean',

            // Ibu
            'nama_ibu' => 'required|string|max:255',
            'nik_ibu' => 'nullable|string|size:16',
            'pekerjaan_ibu' => 'required|string|max:100',
            'pendidikan_ibu' => 'required|string|max:50',
            'penghasilan_ibu' => 'nullable|string|max:50',
            'no_hp_ibu' => 'nullable|string|max:20',
            'ibu_masih_hidup' => 'boolean',

            // Wali (all nullable)
            'nama_wali' => 'nullable|string|max:255',
            'nik_wali' => 'nullable|string|size:16',
            'hubungan_wali' => 'nullable|string|max:50',
            'pekerjaan_wali' => 'nullable|string|max:100',
            'pendidikan_wali' => 'nullable|string|max:50',
            'penghasilan_wali' => 'nullable|string|max:50',
            'no_hp_wali' => 'nullable|string|max:20',
        ];
    }
}

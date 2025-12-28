<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Get all settings
     */
    public function index()
    {
        $settings = Setting::getAll();
        
        return response()->json($settings);
    }

    /**
     * Update settings (hanya super admin)
     */
    public function update(Request $request)
    {
        // Validasi hanya super admin yang bisa update
        if (!$request->user()->isSuperAdmin()) {
            return response()->json([
                'message' => 'Forbidden. Hanya Super Admin yang bisa update settings.'
            ], 403);
        }

        $validated = $request->validate([
            'nama_sekolah' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|string|max:255',
            'youtube' => 'nullable|url|max:255',
            'twitter' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'tagline' => 'nullable|string|max:255',
        ]);

        // Update text settings
        foreach ($validated as $key => $value) {
            if ($value !== null) {
                Setting::set($key, $value);
            }
        }

        return response()->json([
            'message' => 'Settings berhasil diperbarui',
            'settings' => Setting::getAll()
        ]);
    }

    /**
     * Upload logo
     */
    public function uploadLogo(Request $request)
    {
        // Validasi hanya super admin
        if (!$request->user()->isSuperAdmin()) {
            return response()->json([
                'message' => 'Forbidden. Hanya Super Admin yang bisa upload logo.'
            ], 403);
        }

        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg|max:2048' // 2MB max
        ]);

        // Hapus logo lama jika ada
        $oldLogo = Setting::get('logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }

        // Upload logo baru
        $path = $request->file('logo')->store('logos', 'public');

        // Save ke database
        Setting::set('logo', $path, 'image');

        return response()->json([
            'message' => 'Logo berhasil diupload',
            'logo' => $path
        ]);
    }

    /**
     * Delete logo
     */
    public function deleteLogo(Request $request)
    {
        // Validasi hanya super admin
        if (!$request->user()->isSuperAdmin()) {
            return response()->json([
                'message' => 'Forbidden. Hanya Super Admin yang bisa hapus logo.'
            ], 403);
        }

        $logo = Setting::get('logo');
        
        if ($logo && Storage::disk('public')->exists($logo)) {
            Storage::disk('public')->delete($logo);
        }

        Setting::set('logo', null, 'image');

        return response()->json([
            'message' => 'Logo berhasil dihapus'
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuruController extends Controller
{
    /**
     * Get list of guru for public display (no auth required)
     * Supports search by nama or pelajaran
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Guru::query()->latest('created_at');

        // Search filter
        if ($search) {
            $query->search($search);
        }

        // Get all guru (no pagination for frontend)
        $guru = $query->get();

        return response()->json([
            'success' => true,
            'data' => $guru->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->nama,
                    'subject' => $item->pelajaran,
                    'photo' => $item->foto ? Storage::url($item->foto) : null,
                ];
            })
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of active documents for public
     */
    public function index()
    {
        try {
            $documents = Document::active()
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $documents
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dokumen'
            ], 500);
        }
    }

    /**
     * Download document
     */
    public function download($id)
    {
        try {
            $document = Document::findOrFail($id);
            
            $filePath = storage_path('app/public/documents/' . $document->file_path);
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            return response()->download($filePath, $document->file_name);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh dokumen'
            ], 500);
        }
    }
}

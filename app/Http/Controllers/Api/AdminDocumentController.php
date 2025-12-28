<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminDocumentController extends Controller
{
    public function index()
    {
        try {
            $documents = Document::latest()->get();
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // 10MB
        ]);

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('documents', $fileName, 'public');

                $document = Document::create([
                    'title' => $validated['title'],
                    'description' => $validated['description'] ?? null,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $fileName,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                    'is_active' => true
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen berhasil diupload',
                    'data' => $document
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload dokumen'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $document = Document::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $document
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $document = Document::findOrFail($id);
            $document->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diupdate',
                'data' => $document
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate dokumen'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $document = Document::findOrFail($id);
            
            // Delete file
            if (Storage::disk('public')->exists('documents/' . $document->file_path)) {
                Storage::disk('public')->delete('documents/' . $document->file_path);
            }

            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen'
            ], 500);
        }
    }

    public function toggleActive($id)
    {
        try {
            $document = Document::findOrFail($id);
            $document->is_active = !$document->is_active;
            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'Status dokumen berhasil diubah',
                'data' => $document
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status dokumen'
            ], 500);
        }
    }
}

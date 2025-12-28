<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SekolahController extends Controller
{
    /**
     * Kemendikdasmen API base URL
     */
    private string $apiUrl = 'https://sekolah.data.kemendikdasmen.go.id/v1/sekolah-service/sekolah/cari-sekolah';

    /**
     * Search schools from Kemendikdasmen API
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $keyword = $request->input('keyword', '');
        $bentuk = $request->input('bentuk', ''); // SMP, MTs, etc.
        $page = $request->input('page', 0);
        $size = $request->input('size', 15);

        if (strlen($keyword) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Keyword minimal 3 karakter',
                'data' => []
            ]);
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'page' => (int) $page,
                    'size' => (int) $size,
                    'keyword' => $keyword,
                    'kabupaten_kota' => '',
                    'bentuk_pendidikan' => $bentuk,
                    'status_sekolah' => ''
                ]);

            if ($response->successful()) {
                $data = $response->json();

                // Filter only SMP/MTs if bentuk not specified
                $schools = $data['content'] ?? [];

                if (empty($bentuk)) {
                    $schools = array_filter($schools, function ($school) {
                        $bentuk = strtoupper($school['bentukPendidikan'] ?? '');
                        return in_array($bentuk, ['SMP', 'MTS']);
                    });
                    $schools = array_values($schools);
                }

                return response()->json([
                    'success' => true,
                    'data' => $schools,
                    'total' => $data['totalElements'] ?? count($schools)
                ]);
            }

            Log::warning('Kemendikdasmen API returned error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dari server',
                'data' => []
            ], 500);

        } catch (\Exception $e) {
            Log::error('Kemendikdasmen API exception', [
                'message' => $e->getMessage(),
                'keyword' => $keyword
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan koneksi',
                'data' => []
            ], 500);
        }
    }
}

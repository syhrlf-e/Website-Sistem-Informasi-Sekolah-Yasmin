<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SekolahController extends Controller
{
    /**
     * Kemendikdasmen API base URL (fallback)
     */
    private string $apiUrl = 'https://sekolah.data.kemendikdasmen.go.id/v1/sekolah-service/sekolah/cari-sekolah';

    /**
     * Search schools - uses local database first, falls back to API
     */
    public function search(Request $request): JsonResponse
    {
        $keyword = trim($request->input('keyword', ''));

        if (strlen($keyword) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Keyword minimal 3 karakter',
                'data' => []
            ]);
        }

        // Try local database first
        $localResults = Sekolah::search($keyword, 20);

        if ($localResults->count() > 0) {
            return response()->json([
                'success' => true,
                'source' => 'local',
                'data' => $localResults->map(function ($school) {
                    return [
                        'npsn' => $school->npsn,
                        'nama' => $school->nama,
                        'bentuk_pendidikan' => $school->bentuk,
                        'status_sekolah' => $school->status,
                        'akreditasi' => $school->akreditasi,
                        'alamat_jalan' => $school->alamat_jalan,
                        'kecamatan' => $school->kecamatan,
                        'kabupaten' => $school->kabupaten,
                        'provinsi' => $school->provinsi,
                    ];
                }),
                'total' => $localResults->count()
            ]);
        }

        // Fallback to external API
        return $this->searchExternal($keyword);
    }

    /**
     * Search from external Kemendikdasmen API
     */
    private function searchExternal(string $keyword): JsonResponse
    {
        try {
            // Extract location word for better search
            $words = explode(' ', $keyword);
            $skipWords = ['smp', 'smpn', 'mts', 'negeri', 'swasta', 'islam', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];

            $uniqueWord = null;
            for ($i = count($words) - 1; $i >= 0; $i--) {
                if (!in_array(strtolower($words[$i]), $skipWords) && strlen($words[$i]) > 2) {
                    $uniqueWord = $words[$i];
                    break;
                }
            }

            $allSchools = [];

            // Search with full keyword
            $response1 = $this->fetchFromApi($keyword);
            if ($response1) {
                $allSchools = array_merge($allSchools, $response1);
            }

            // Search with location word if found
            if ($uniqueWord && strtolower($uniqueWord) !== strtolower($keyword)) {
                $response2 = $this->fetchFromApi($uniqueWord);
                if ($response2) {
                    foreach ($response2 as $school) {
                        $npsn = $school['npsn'] ?? '';
                        $exists = false;
                        foreach ($allSchools as $existing) {
                            if (($existing['npsn'] ?? '') === $npsn) {
                                $exists = true;
                                break;
                            }
                        }
                        if (!$exists) {
                            $allSchools[] = $school;
                        }
                    }
                }
            }

            // Filter SMP/MTs only
            $allSchools = array_filter($allSchools, function ($school) {
                $bentuk = strtoupper($school['bentuk_pendidikan'] ?? '');
                return in_array($bentuk, ['SMP', 'MTS']);
            });
            $allSchools = array_values($allSchools);

            // Smart sorting
            $keywordParts = array_filter(explode(' ', strtolower($keyword)));
            usort($allSchools, function ($a, $b) use ($keywordParts) {
                $nameA = strtolower($a['nama'] ?? '');
                $nameB = strtolower($b['nama'] ?? '');

                $matchA = 0;
                $matchB = 0;

                foreach ($keywordParts as $part) {
                    if (str_contains($nameA, $part))
                        $matchA++;
                    if (str_contains($nameB, $part))
                        $matchB++;
                }

                return $matchB - $matchA;
            });

            return response()->json([
                'success' => true,
                'source' => 'api',
                'data' => array_slice($allSchools, 0, 20),
                'total' => count($allSchools)
            ]);

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

    /**
     * Fetch from API
     */
    private function fetchFromApi(string $keyword): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->post($this->apiUrl, [
                    'page' => 0,
                    'size' => 50,
                    'keyword' => $keyword,
                    'kabupaten_kota' => '',
                    'bentuk_pendidikan' => '',
                    'status_sekolah' => ''
                ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            Log::warning('API fetch failed', ['keyword' => $keyword]);
        }

        return null;
    }
}

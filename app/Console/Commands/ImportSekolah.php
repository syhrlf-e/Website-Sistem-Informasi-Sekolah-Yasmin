<?php

namespace App\Console\Commands;

use App\Models\Sekolah;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportSekolah extends Command
{
    protected $signature = 'sekolah:import {--region=jabodetabek : Region to import (jabodetabek or all)}';
    protected $description = 'Import SMP/MTs data from Kemendikdasmen API';

    private string $apiUrl = 'https://sekolah.data.kemendikdasmen.go.id/v1/sekolah-service/sekolah/cari-sekolah';

    // Jabodetabek regions
    private array $jabodetabekRegions = [
        // DKI Jakarta (format: "Kota Adm. ...")
        'Kota Adm. Jakarta Pusat',
        'Kota Adm. Jakarta Utara',
        'Kota Adm. Jakarta Barat',
        'Kota Adm. Jakarta Selatan',
        'Kota Adm. Jakarta Timur',
        'Kab. Adm. Kepulauan Seribu',
        // Bogor
        'Kab. Bogor',
        'Kota Bogor',
        // Depok
        'Kota Depok',
        // Tangerang
        'Kab. Tangerang',
        'Kota Tangerang',
        'Kota Tangerang Selatan',
        // Bekasi
        'Kab. Bekasi',
        'Kota Bekasi',
    ];

    public function handle()
    {
        $region = $this->option('region');

        $this->info('🏫 Starting school data import...');
        $this->newLine();

        $regions = $region === 'jabodetabek' ? $this->jabodetabekRegions : $this->jabodetabekRegions;
        $totalImported = 0;
        $totalSkipped = 0;

        foreach ($regions as $kabupaten) {
            $this->info("📍 Processing: {$kabupaten}");

            // Import SMP
            $smpResult = $this->importByRegion($kabupaten, 'SMP');
            $totalImported += $smpResult['imported'];
            $totalSkipped += $smpResult['skipped'];

            // Import MTs
            $mtsResult = $this->importByRegion($kabupaten, 'MTs');
            $totalImported += $mtsResult['imported'];
            $totalSkipped += $mtsResult['skipped'];

            $this->info("   ✅ SMP: {$smpResult['imported']} | MTs: {$mtsResult['imported']}");
        }

        $this->newLine();
        $this->info("🎉 Import completed!");
        $this->info("   Total imported: {$totalImported}");
        $this->info("   Total skipped (duplicates): {$totalSkipped}");

        return Command::SUCCESS;
    }

    private function importByRegion(string $kabupaten, string $bentuk): array
    {
        $imported = 0;
        $skipped = 0;
        $page = 0;
        $size = 100;
        $hasMore = true;

        while ($hasMore) {
            try {
                $response = Http::timeout(30)
                    ->withoutVerifying()
                    ->post($this->apiUrl, [
                        'page' => $page,
                        'size' => $size,
                        'keyword' => '',
                        'kabupaten_kota' => $kabupaten,
                        'bentuk_pendidikan' => $bentuk,
                        'status_sekolah' => ''
                    ]);

                if (!$response->successful()) {
                    $this->warn("   ⚠️ API error for {$kabupaten} ({$bentuk})");
                    break;
                }

                $data = $response->json();
                $schools = $data['data'] ?? [];

                if (empty($schools)) {
                    $hasMore = false;
                    continue;
                }

                foreach ($schools as $school) {
                    $result = $this->saveSchool($school);
                    if ($result === 'imported') {
                        $imported++;
                    } else {
                        $skipped++;
                    }
                }

                // Check if more pages
                $total = $data['total'] ?? 0;
                $fetched = ($page + 1) * $size;
                $hasMore = $fetched < $total;
                $page++;

                // Rate limiting
                usleep(100000); // 100ms delay

            } catch (\Exception $e) {
                $this->warn("   ⚠️ Error: " . $e->getMessage());
                Log::error('Sekolah import error', [
                    'kabupaten' => $kabupaten,
                    'bentuk' => $bentuk,
                    'error' => $e->getMessage()
                ]);
                break;
            }
        }

        return ['imported' => $imported, 'skipped' => $skipped];
    }

    private function saveSchool(array $data): string
    {
        $npsn = $data['npsn'] ?? null;

        if (!$npsn) {
            return 'skipped';
        }

        // Check if exists
        if (Sekolah::where('npsn', $npsn)->exists()) {
            return 'skipped';
        }

        // Determine bentuk
        $bentukRaw = strtoupper($data['bentuk_pendidikan'] ?? '');
        $bentuk = in_array($bentukRaw, ['SMP', 'SMPS']) ? 'SMP' : 'MTs';

        // Determine status
        $statusRaw = strtoupper($data['status_sekolah'] ?? '');
        $status = $statusRaw === 'NEGERI' ? 'NEGERI' : 'SWASTA';

        try {
            Sekolah::create([
                'npsn' => $npsn,
                'nama' => $data['nama'] ?? '',
                'nama_normalized' => Sekolah::normalizeName($data['nama'] ?? ''),
                'bentuk' => $bentuk,
                'status' => $status,
                'akreditasi' => $data['akreditasi'] ?? null,
                'alamat_jalan' => $data['alamat_jalan'] ?? null,
                'nama_dusun' => $data['nama_dusun'] ?? null,
                'kecamatan' => $this->cleanLocationName($data['kecamatan'] ?? ''),
                'kabupaten' => $this->cleanLocationName($data['kabupaten'] ?? ''),
                'provinsi' => $this->cleanLocationName($data['provinsi'] ?? ''),
                'kode_pos' => $data['kode_pos'] ?? null,
                'lintang' => $data['lintang'] ?? null,
                'bujur' => $data['bujur'] ?? null,
            ]);

            return 'imported';
        } catch (\Exception $e) {
            Log::warning('Failed to save school', ['npsn' => $npsn, 'error' => $e->getMessage()]);
            return 'skipped';
        }
    }

    private function cleanLocationName(string $name): string
    {
        // Remove "Kec. ", "Kab. ", "Kota ", "Prov. " prefixes for cleaner display
        return preg_replace('/^(Kec\.|Kab\.|Kota|Prov\.)\s*/i', '', trim($name));
    }
}

<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Ekstrakurikuler;
use App\Models\Gallery;
use App\Models\Prestasi;
use App\Models\EkstrakurikulerRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics (cached 5 minutes)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        // News stats - single query with conditional counts
        $newsStats = News::whereNull('deleted_at')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN published = 1 THEN 1 ELSE 0 END) as published,
                SUM(CASE WHEN published = 0 THEN 1 ELSE 0 END) as draft,
                SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured
            ')
            ->first();

        // Registration stats - single query with conditional counts
        $regStats = EkstrakurikulerRegistration::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected
            ')
            ->first();

        // Combined count for ekskul, gallery, prestasi in single query
        // Before: 3 separate queries (3 round trips)
        // After: 1 raw query with subqueries (1 round trip)
        $otherStats = \DB::selectOne("
            SELECT 
                (SELECT COUNT(*) FROM ekstrakurikuler WHERE is_active = 1) as ekskul,
                (SELECT COUNT(*) FROM galleries) as galeri,
                (SELECT COUNT(*) FROM prestasi) as prestasi
        ");

        $stats = [
            'berita' => (int) $newsStats->total,
            'berita_published' => (int) $newsStats->published,
            'berita_draft' => (int) $newsStats->draft,
            'berita_featured' => (int) $newsStats->featured,

            'ekskul' => (int) $otherStats->ekskul,
            'galeri' => (int) $otherStats->galeri,
            'prestasi' => (int) $otherStats->prestasi,

            'pendaftar' => (int) $regStats->total,
            'pendaftar_pending' => (int) $regStats->pending,
            'pendaftar_approved' => (int) $regStats->approved,
            'pendaftar_rejected' => (int) $regStats->rejected,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get recent pendaftar (for dashboard)
     */
    public function pendaftar()
    {
        $pendaftar = EkstrakurikulerRegistration::with('ekstrakurikuler')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($registration) {
                return [
                    'id' => $registration->id,
                    'nama' => $registration->nama_lengkap,
                    'kelas' => $registration->kelas,
                    'ekskul' => $registration->ekstrakurikuler->nama,
                    'waktu' => $registration->created_at->diffForHumans(),
                    'created_at' => $registration->created_at->format('d M Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $pendaftar
        ]);
    }
}
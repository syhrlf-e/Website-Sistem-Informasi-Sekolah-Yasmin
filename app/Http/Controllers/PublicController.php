<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Prestasi;
use App\Models\Ekstrakurikuler;
use App\Models\Gallery;
use App\Models\Testimonial;
use App\Models\Document;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

/**
 * PublicController
 * 
 * Handles all public-facing pages with Inertia.js and SEO optimization.
 * All meta tags are rendered server-side for maximum SEO benefits.
 */
class PublicController extends Controller
{
    /**
     * Home Page
     */
    public function home()
    {
        $featuredNews = Cache::remember('featured_news_home', 600, function () {
            return News::published()
                ->featured()
                ->latest('published_at')
                ->limit(6)
                ->get()
                ->map(fn($news) => $this->formatNewsItem($news));
        });

        $galleries = Cache::remember('galleries_home', 600, function () {
            return Gallery::with('images')
                ->where('is_active', true)
                ->whereNotNull('grid_position')
                ->whereNotNull('image_path')
                ->where('image_path', '!=', '')
                ->orderBy('grid_position')
                ->get()
                ->map(fn($gallery) => [
                    'id' => $gallery->id,
                    'title' => $gallery->title ?? 'Galeri',
                    'image' => $gallery->image_path ? Storage::url($gallery->image_path) : null,
                    'grid_position' => $gallery->grid_position,
                    'all_images' => $gallery->all_images, // All images for slideshow
                    'image_count' => count($gallery->all_images)
                ])
                ->filter(fn($g) => $g['image'] !== null)
                ->values();
        });

        $prestasi = Cache::remember('prestasi_home', 600, function () {
            return Prestasi::latest()
                ->limit(6)
                ->get()
                ->map(fn($item) => $this->formatPrestasiItem($item));
        });

        $ekstrakurikuler = Cache::remember('ekskul_home', 600, function () {
            return Ekstrakurikuler::withCount([
                'registrations as approved_count' => fn($q) => $q->where('status', 'approved')
            ])
                ->where('is_active', true)
                ->whereNotNull('nama')
                ->where('nama', '!=', '')
                ->get()
                ->map(fn($item) => [
                    'id' => $item->id,
                    'name' => $item->nama ?? 'Ekstrakurikuler',
                    'category' => $item->badge ?? 'Akademik', // badge field = category
                    'tagline' => $item->tagline ?? '',
                    'description' => $item->deskripsi ?? '',
                    'image' => $item->gambar ? Storage::url($item->gambar) : null,
                    'schedule' => $item->jadwal ?? '',
                    'location' => $item->lokasi ?? '',
                    'mentor' => $item->pembina ?? '',
                    'benefits' => is_string($item->benefits) ? json_decode($item->benefits, true) : ($item->benefits ?? []),
                    'max_participants' => $item->max_participants ?? null,
                    'approved_registrations_count' => $item->approved_registrations_count,
                    'available_slots' => $item->available_slots,
                    'is_slot_full' => $item->is_slot_full,
                    'is_registration_open' => $item->is_registration_open,
                    'registration_deadline' => $item->registration_deadline ?? null,
                    'registration_closure_reason' => $item->registration_closure_reason ?? null,
                ]);
        });

        $testimonials = Cache::remember('testimonials_home', 600, function () {
            return Testimonial::where('is_active', true)
                ->whereNotNull('author')
                ->whereNotNull('text')
                ->where('author', '!=', '')
                ->where('text', '!=', '')
                ->orderBy('order')
                ->get()
                ->map(fn($item) => [
                    'id' => $item->id,
                    'name' => $item->author ?? 'Anonim',
                    'role' => $item->role ?? '',
                    'content' => $item->text ?? '',
                    'image' => $item->photo ? Storage::url($item->photo) : null,
                ]);
        });

        return Inertia::render('Home', [
            'featuredNews' => $featuredNews,
            'galleries' => $galleries,
            'prestasi' => $prestasi,
            'ekstrakurikuler' => $ekstrakurikuler,
            'testimonials' => $testimonials,

            // SEO Meta Tags
            'meta' => [
                'title' => 'SMA Mutiara Insan Nusantara - Beranda',
                'description' => 'Sekolah menengah atas yang berkomitmen menghasilkan generasi cerdas, berkarakter, dan berprestasi di Tangerang.',
                'keywords' => 'SMA Mutiara Insan Nusantara, SMA Yasmin, SMA Rajeg, sekolah tangerang, sekolah terbaik tangerang',
                'og_type' => 'website',
            ],
            // Schema.org
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'EducationalOrganization',
                'name' => 'SMA Mutiara Insan Nusantara',
                'alternateName' => 'SMAS Mutiara',
                'url' => url('/'),
                'logo' => asset('img/logo_yasmin.png'),
                'description' => 'Sekolah menengah atas yang berkomitmen menghasilkan generasi cerdas, berkarakter, dan berprestasi.',
            ],
        ]);
    }

    /**
     * Profil Page
     */
    public function profil()
    {
        $guru = Guru::orderBy('id')->get()->map(fn($g) => [
            'id' => $g->id,
            'name' => $g->name,
            'position' => $g->position,
            'image' => $g->image ? Storage::url($g->image) : null,
        ]);

        return Inertia::render('Profil', [
            'guru' => $guru,

            'meta' => [
                'title' => 'Profil Sekolah - SMA Mutiara Insan Nusantara',
                'description' => 'Profil lengkap SMA Mutiara Insan Nusantara, visi misi, sejarah, dan tenaga pendidik.',
                'og_type' => 'website',
            ],
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'AboutPage',
                'name' => 'Profil SMA Mutiara Insan Nusantara',
                'mainEntity' => [
                    '@type' => 'EducationalOrganization',
                    'name' => 'SMA Mutiara Insan Nusantara',
                ],
            ],
        ]);
    }

    /**
     * News List Page
     */
    public function news(Request $request)
    {
        $perPage = $request->get('per_page', 12);
        $search = $request->get('search');
        $category = $request->get('category');

        $query = News::published()->latest('published_at');

        if ($search) {
            $query->search($search);
        }

        if ($category && $category !== 'all') {
            $query->byCategory($category);
        }

        $news = $query->paginate($perPage);

        return Inertia::render('News/Index', [
            'news' => $news->through(fn($item) => $this->formatNewsItem($item)),
            'filters' => [
                'search' => $search,
                'category' => $category,
            ],

            'meta' => [
                'title' => 'Berita Sekolah - SMA Mutiara Insan Nusantara',
                'description' => 'Berita terbaru dan informasi kegiatan SMA Mutiara Insan Nusantara.',
                'og_type' => 'website',
            ],
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => 'Berita SMA Mutiara Insan Nusantara',
            ],
        ]);
    }

    /**
     * News Detail Page (Most important for SEO!)
     */
    public function newsDetail($slug)
    {
        $news = News::published()->where('slug', $slug)->firstOrFail();
        $news->incrementViews();

        $relatedNews = $news->getRelatedNews(3)->map(fn($item) => $this->formatNewsItem($item));

        $newsData = [
            'id' => $news->id,
            'title' => $news->title,
            'slug' => $news->slug,
            'content' => $news->content,
            'excerpt' => $news->excerpt,
            'location' => $news->location,
            'image' => $news->image ? Storage::url($news->image) : null,
            'gallery' => $news->gallery ? array_map(fn($img) => Storage::url($img), $news->gallery) : [],
            'category' => $news->category,
            'author' => $news->author,
            'views' => $news->getRealViews(),
            'date' => $news->published_at->format('Y-m-d'),
            'formatted_date' => $news->published_at->format('d F Y'),
        ];

        $ogImage = $news->image ? Storage::url($news->image) : asset('images/og-image.png');

        return Inertia::render('News/NewsDetail', [
            'news' => $newsData,
            'relatedNews' => $relatedNews,

            'meta' => [
                'title' => $news->title . ' - SMA Mutiara Insan Nusantara',
                'description' => Str::limit(strip_tags($news->content), 160),
                'og_type' => 'article',
                'og_title' => $news->title,
                'og_description' => Str::limit(strip_tags($news->content), 160),
                'og_image' => $ogImage,
                'canonical' => route('news.show', $news->slug),
            ],
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $news->title,
                'description' => Str::limit(strip_tags($news->content), 160),
                'image' => $ogImage,
                'datePublished' => $news->published_at->toIso8601String(),
                'dateModified' => $news->updated_at->toIso8601String(),
                'author' => [
                    '@type' => 'Organization',
                    'name' => 'SMA Mutiara Insan Nusantara',
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'SMA Mutiara Insan Nusantara',
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => asset('img/logo_yasmin.png'),
                    ],
                ],
            ],
        ]);
    }

    /**
     * Prestasi Page
     */
    public function prestasi(Request $request)
    {
        $perPage = $request->get('per_page', 12);

        $prestasi = Prestasi::latest()
            ->paginate($perPage);

        return Inertia::render('Prestasi/Index', [
            'prestasi' => $prestasi->through(fn($item) => $this->formatPrestasiItem($item)),

            'meta' => [
                'title' => 'Prestasi Siswa - SMA Mutiara Insan Nusantara',
                'description' => 'Daftar prestasi dan pencapaian siswa-siswi SMA Mutiara Insan Nusantara di berbagai bidang.',
                'og_type' => 'website',
            ],
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => 'Prestasi Siswa SMA Mutiara Insan Nusantara',
            ],
        ]);
    }

    /**
     * PPDB Page
     */
    public function ppdb()
    {
        $documents = Document::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($doc) => [
                'id' => $doc->id,
                'title' => $doc->title,
                'description' => $doc->description,
                'file_name' => $doc->file_name,
                'file_type' => $doc->file_type,
                'file_size' => $doc->file_size,
                'created_at' => $doc->created_at->format('Y-m-d'),
            ]);

        return Inertia::render('PPDB', [
            'documents' => $documents,

            'meta' => [
                'title' => 'Informasi PPDB - SMA Mutiara Insan Nusantara',
                'description' => 'Informasi Penerimaan Peserta Didik Baru (PPDB) SMA Mutiara Insan Nusantara. Download formulir dan persyaratan pendaftaran.',
                'keywords' => 'PPDB SMA Yasmin, pendaftaran siswa baru tangerang, PPDB SMA Mutiara',
                'og_type' => 'website',
            ],
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'PPDB SMA Mutiara Insan Nusantara',
                'description' => 'Informasi pendaftaran siswa baru SMA Mutiara Insan Nusantara',
            ],
        ]);
    }

    /**
     * PPDB Landing Page
     */
    public function ppdbLanding()
    {
        return Inertia::render('PpdbLanding', [
            'meta' => [
                'title' => 'PPDB - SMA Mutiara Insan Nusantara',
                'description' => 'Pendaftaran Peserta Didik Baru SMA Mutiara Insan Nusantara',
            ],
        ]);
    }

    /**
     * PPDB Registration Form Page
     */
    public function ppdbDaftar()
    {
        return Inertia::render('PpdbDaftar', [
            'meta' => [
                'title' => 'Daftar PPDB - SMA Mutiara Insan Nusantara',
                'description' => 'Formulir pendaftaran PPDB SMA Mutiara Insan Nusantara',
            ],
        ]);
    }

    /**
     * PPDB Success Page
     */
    public function ppdbSukses()
    {
        return Inertia::render('PpdbSukses', [
            'meta' => [
                'title' => 'Pendaftaran Berhasil - PPDB SMA Mutiara Insan Nusantara',
            ],
        ]);
    }

    /**
     * PPDB Status Check Page
     */
    public function ppdbStatus()
    {
        return Inertia::render('PpdbStatus', [
            'meta' => [
                'title' => 'Cek Status PPDB - SMA Mutiara Insan Nusantara',
                'description' => 'Cek status pendaftaran PPDB SMA Mutiara Insan Nusantara',
            ],
        ]);
    }

    /**
     * Guru Page
     */
    public function guru()
    {
        $guru = Guru::orderBy('id')->get()->map(fn($g) => [
            'id' => $g->id,
            'name' => $g->name,
            'position' => $g->position,
            'nip' => $g->nip,
            'education' => $g->education,
            'image' => $g->image ? Storage::url($g->image) : null,
        ]);

        return Inertia::render('Guru', [
            'guru' => $guru,

            'meta' => [
                'title' => 'Tenaga Pendidik - SMA Mutiara Insan Nusantara',
                'description' => 'Daftar guru dan tenaga pendidik SMA Mutiara Insan Nusantara.',
                'og_type' => 'website',
            ],
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => 'Tenaga Pendidik SMA Mutiara Insan Nusantara',
            ],
        ]);
    }

    /**
     * Sitemap.xml - Generate XML directly without Blade view
     */
    public function sitemap()
    {
        // Only select columns needed for sitemap to prevent memory exhaustion
        // Before: loaded ALL columns for ALL news (dangerous for large datasets)
        // After: only slug and updated_at, which are the only fields used
        $news = News::published()
            ->select('slug', 'updated_at')
            ->latest()
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Static pages
        $staticPages = [
            ['loc' => url('/'), 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => url('/profil'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => url('/news'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => url('/prestasi'), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => url('/ppdb'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => url('/guru'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ];

        foreach ($staticPages as $page) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($page['loc']) . '</loc>';
            $xml .= '<changefreq>' . $page['changefreq'] . '</changefreq>';
            $xml .= '<priority>' . $page['priority'] . '</priority>';
            $xml .= '</url>';
        }

        // News articles
        foreach ($news as $item) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars(url('/news/' . $item->slug)) . '</loc>';
            $xml .= '<lastmod>' . $item->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.7</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Format news item for response
     */
    private function formatNewsItem($news)
    {
        return [
            'id' => $news->id,
            'title' => $news->title,
            'slug' => $news->slug,
            'excerpt' => $news->excerpt,
            'location' => $news->location,
            'image' => $news->image ? Storage::url($news->image) : '/images/placeholder-news.jpg',
            'category' => $news->category,
            'date' => $news->published_at->format('Y-m-d'),
            'formatted_date' => $news->published_at->format('d F Y'),
            'views' => $news->getRealViews(),
        ];
    }

    /**
     * Format prestasi item for response
     */
    private function formatPrestasiItem($prestasi)
    {
        // Ensure image path includes 'prestasi/' folder
        $imagePath = null;
        if ($prestasi->image) {
            // If path already starts with 'prestasi/', use as is
            // Otherwise, prepend 'prestasi/' folder
            $imagePath = str_starts_with($prestasi->image, 'prestasi/')
                ? $prestasi->image
                : 'prestasi/' . $prestasi->image;
        }

        return [
            'id' => $prestasi->id,
            'title' => $prestasi->nama_prestasi,
            'description' => $prestasi->deskripsi,
            'category' => $prestasi->kategori,
            'level' => $prestasi->tingkat,
            'year' => $prestasi->tahun,
            'participants' => $prestasi->peserta,
            'image' => $imagePath ? Storage::url($imagePath) : null,
            'image_crop' => $prestasi->image_crop, // Crop coordinates for display
            'slug' => $prestasi->slug,
        ];
    }
}

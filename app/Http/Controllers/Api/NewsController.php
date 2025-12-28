<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{
    /**
     * Get featured news for homepage (cached)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function featured(Request $request)
    {
        $limit = $request->get('limit', 8);
        $cacheKey = "featured_news_{$limit}";

        $news = Cache::remember($cacheKey, 600, function () use ($limit) {
            return News::published()
                ->featured()
                ->latest('published_at')
                ->limit($limit)
                ->get()
                ->map(function ($item) {
                    return $this->formatNewsItem($item);
                });
        });

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    /**
     * Get all news with pagination and filters
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 12);
        $search = $request->get('search');
        $category = $request->get('category');
        $date = $request->get('date');

        $query = News::published()
            ->latest('published_at');

        // Apply search
        if ($search) {
            $query->search($search);
        }

        // Apply category filter
        if ($category) {
            $query->byCategory($category);
        }

        // Apply date filter
        if ($date) {
            $query->whereDate('published_at', $date);
        }

        $news = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $news->map(function ($item) {
                return $this->formatNewsItem($item);
            }),
            'meta' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
                'from' => $news->firstItem(),
                'to' => $news->lastItem()
            ]
        ]);
    }

    /**
     * Get single news detail by slug
     */
    public function show($slug)
    {
        $news = News::published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views
        $news->incrementViews();

        // Get related news
        $relatedNews = $news->getRelatedNews(3);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $news->id,
                'title' => $news->title,
                'slug' => $news->slug,
                'content' => $news->content,
                'excerpt' => $news->excerpt,
                'location' => $news->location,
                'image' => $news->image ? Storage::url($news->image) : null,
                'gallery' => $news->gallery ? array_map(function ($img) {
                    return Storage::url($img);
                }, $news->gallery) : [],
                'category' => $news->category,
                'author' => $news->author,
                'views' => $news->views,
                'date' => $news->published_at->format('Y-m-d'),
                'formatted_date' => $news->published_at->format('d F Y'),
                'related_news' => $relatedNews->map(function ($item) {
                    return $this->formatNewsItem($item);
                })
            ]
        ]);
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
            'views' => $news->views
        ];
    }
}
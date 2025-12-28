<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory, SoftDeletes, Prunable;

    /**
     * Cache key prefix for view counts
     */
    public const VIEW_CACHE_PREFIX = 'news_views_';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'location',
        'image',
        'gallery',
        'category',
        'author',
        'is_featured',
        'published',
        'views',
        'published_at'
    ];

    protected $casts = [
        'gallery' => 'array',
        'is_featured' => 'boolean',
        'published' => 'boolean',
        'published_at' => 'datetime',
        'views' => 'integer'
    ];

    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Automatically generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }

            if (empty($news->published_at)) {
                $news->published_at = now();
            }
        });

        static::updating(function ($news) {
            if ($news->isDirty('title') && !$news->isDirty('slug')) {
                $news->slug = Str::slug($news->title);
            }
        });
    }

    /**
     * Get the prunable model query.
     * Berita yang sudah di-soft-delete lebih dari 30 hari akan dihapus permanen.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function prunable()
    {
        return static::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays(30));
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('published', true)
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * Scope untuk pencarian berita menggunakan FULLTEXT index
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            if (strlen($search) < 3) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            }

            return $query->whereFullText(['title', 'content', 'location'], $search);
        }
        return $query;
    }

    /**
     * Increment view count for news article (DEFERRED - uses cache)
     * Uses session to prevent multiple counts from same user
     * Views are stored in cache and flushed to DB every 10 minutes
     * 
     * @return bool Returns true if view was counted, false if already viewed
     */
    public function incrementViews(): bool
    {
        // Get viewed articles from session
        $viewedArticles = session('viewed_articles', []);

        // Check if this article was already viewed in this session
        if (in_array($this->id, $viewedArticles)) {
            return false; // Already viewed, don't increment
        }

        // Add this article to viewed list
        $viewedArticles[] = $this->id;
        session(['viewed_articles' => $viewedArticles]);

        // Increment in cache instead of direct DB write
        // This prevents database locking on high traffic
        $cacheKey = self::VIEW_CACHE_PREFIX . $this->id;

        // Cache::increment() requires existing key, so check first
        if (Cache::has($cacheKey)) {
            Cache::increment($cacheKey);
        } else {
            Cache::put($cacheKey, 1, now()->addDay());
        }

        // Track which news IDs have pending views (for flush command)
        $pendingIds = Cache::get('news_views_pending_ids', []);
        if (!in_array($this->id, $pendingIds)) {
            $pendingIds[] = $this->id;
            Cache::put('news_views_pending_ids', $pendingIds, now()->addDay());
        }

        return true;
    }

    /**
     * Get cached view count for this article
     * 
     * @return int Cached views not yet flushed to DB
     */
    public function getCachedViews(): int
    {
        return (int) Cache::get(self::VIEW_CACHE_PREFIX . $this->id, 0);
    }

    /**
     * Get real-time view count (DB + cached)
     * Use this when displaying views to users
     * 
     * @return int Total views including pending cache
     */
    public function getRealViews(): int
    {
        return $this->views + $this->getCachedViews();
    }

    // Get related news (same category first, then fallback to any other news)
    public function getRelatedNews($limit = 3)
    {
        // First try to get news with same category
        $related = self::published()
            ->where('category', $this->category)
            ->where('id', '!=', $this->id)
            ->latest('published_at')
            ->limit($limit)
            ->get();

        // If not enough, fill with other news (different category)
        if ($related->count() < $limit) {
            $remaining = $limit - $related->count();
            $existingIds = $related->pluck('id')->push($this->id)->toArray();

            $otherNews = self::published()
                ->whereNotIn('id', $existingIds)
                ->latest('published_at')
                ->limit($remaining)
                ->get();

            $related = $related->concat($otherNews);
        }

        return $related;
    }
}
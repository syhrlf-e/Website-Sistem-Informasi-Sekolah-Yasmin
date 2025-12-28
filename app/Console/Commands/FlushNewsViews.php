<?php

namespace App\Console\Commands;

use App\Models\News;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FlushNewsViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:flush-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush cached news views to database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $pendingIds = Cache::get('news_views_pending_ids', []);

        if (empty($pendingIds)) {
            $this->info('No pending views to flush.');
            return Command::SUCCESS;
        }

        $this->info('Flushing views for ' . count($pendingIds) . ' articles...');

        $flushedCount = 0;
        $totalViews = 0;

        foreach ($pendingIds as $newsId) {
            $cacheKey = News::VIEW_CACHE_PREFIX . $newsId;
            $cachedViews = (int) Cache::get($cacheKey, 0);

            if ($cachedViews > 0) {
                // Batch update: increment views in database
                DB::table('news')
                    ->where('id', $newsId)
                    ->increment('views', $cachedViews);

                // Clear the cache for this article
                Cache::forget($cacheKey);

                $flushedCount++;
                $totalViews += $cachedViews;

                $this->line("  - News #{$newsId}: +{$cachedViews} views");
            }
        }

        // Clear the pending IDs tracker
        Cache::forget('news_views_pending_ids');

        $this->info("✅ Flushed {$totalViews} views for {$flushedCount} articles.");

        return Command::SUCCESS;
    }
}

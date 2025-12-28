<?php

namespace App\Console\Commands;

use App\Models\News;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanNewsContentImages extends Command
{
    protected $signature = 'news:clean-content-images {--dry-run : Run without making changes}';
    protected $description = 'Remove all img tags from news content and optionally delete orphaned files';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('Running in dry-run mode. No changes will be made.');
        }

        $news = News::withTrashed()->get();
        $totalNews = $news->count();
        $affectedNews = 0;
        $imagesFound = 0;

        $this->info("Processing {$totalNews} news articles...");
        $bar = $this->output->createProgressBar($totalNews);
        $bar->start();

        foreach ($news as $item) {
            // Find all img tags in content
            preg_match_all('/<img[^>]*src=["\']([^"\']+)["\'][^>]*>/i', $item->content, $matches);

            if (count($matches[0]) > 0) {
                $affectedNews++;
                $imagesFound += count($matches[0]);

                if (!$isDryRun) {
                    // Strip img tags from content
                    $cleanContent = preg_replace('/<img[^>]*>/i', '', $item->content);
                    $item->content = $cleanContent;
                    $item->save();
                }

                // Log found images
                foreach ($matches[1] as $src) {
                    $this->newLine();
                    $this->line("  Found image in news #{$item->id}: {$src}");
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Summary:");
        $this->line("  Total news: {$totalNews}");
        $this->line("  Affected news: {$affectedNews}");
        $this->line("  Images found: {$imagesFound}");

        if ($isDryRun) {
            $this->warn('This was a dry run. Run without --dry-run to apply changes.');
        } else {
            $this->info('Content cleaned successfully!');

            // Clean up orphaned files
            $this->cleanOrphanedFiles();
        }

        return Command::SUCCESS;
    }

    private function cleanOrphanedFiles()
    {
        $contentPath = 'news/content';

        if (Storage::disk('public')->exists($contentPath)) {
            $files = Storage::disk('public')->files($contentPath);
            $fileCount = count($files);

            if ($fileCount > 0) {
                if ($this->confirm("Found {$fileCount} files in {$contentPath}. Delete them?", true)) {
                    Storage::disk('public')->deleteDirectory($contentPath);
                    $this->info("Deleted {$fileCount} orphaned files.");
                }
            } else {
                $this->line('No orphaned files found.');
            }
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Menambahkan composite index untuk optimasi query featured news di homepage:
     * News::published()->featured()->latest('published_at')
     * 
     * Query yang dioptimasi:
     * SELECT * FROM news 
     * WHERE published = 1 AND is_featured = 1 AND published_at <= NOW()
     * ORDER BY published_at DESC
     * 
     * Urutan kolom dalam index mengikuti:
     * 1. WHERE clause columns (equality first)
     * 2. ORDER BY column (last)
     */
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            // Composite index untuk featured news query
            $table->index(
                ['published', 'is_featured', 'published_at'],
                'news_featured_composite_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex('news_featured_composite_index');
        });
    }
};

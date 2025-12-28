<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Menambahkan FULLTEXT index pada kolom title, content, dan location
     * untuk optimasi query pencarian yang sebelumnya menggunakan LIKE '%...%'
     */
    public function up(): void
    {
        // Menggunakan raw SQL karena Laravel Blueprint fullText() 
        // tidak support nama index custom dengan baik di semua versi
        DB::statement('ALTER TABLE news ADD FULLTEXT INDEX news_search_fulltext (title, content, location)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE news DROP INDEX news_search_fulltext');
    }
};

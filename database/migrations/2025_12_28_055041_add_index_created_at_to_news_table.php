<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Menambahkan index pada kolom created_at untuk optimasi query:
     * - News::latest('created_at') di admin panel
     * - Sorting berita berdasarkan waktu pembuatan
     * 
     * Sebelumnya query menggunakan File Sort (full table scan)
     * Setelah index: menggunakan B-Tree index untuk sorting
     */
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->index('created_at', 'news_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex('news_created_at_index');
        });
    }
};

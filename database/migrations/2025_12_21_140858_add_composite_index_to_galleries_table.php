<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Menambahkan composite index untuk optimasi query:
     * Gallery::where('is_active', true)->orderBy('order')->orderBy('created_at', 'desc')
     * 
     * Urutan kolom: WHERE clause dulu, lalu ORDER BY sesuai urutan
     */
    public function up(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->index(['is_active', 'order', 'created_at'], 'galleries_listing_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropIndex('galleries_listing_index');
        });
    }
};

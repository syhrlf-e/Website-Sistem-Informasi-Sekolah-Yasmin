<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Menambahkan composite index untuk optimasi query:
     * $this->hasMany(GalleryImage::class)->orderBy('order')
     * 
     * Query yang dioptimasi:
     * SELECT * FROM gallery_images WHERE gallery_id = ? ORDER BY order
     * 
     * Composite index (gallery_id, order) memungkinkan:
     * 1. Filter by gallery_id menggunakan index
     * 2. Sort by order tanpa File Sort (sudah terurut dalam index)
     */
    public function up(): void
    {
        Schema::table('gallery_images', function (Blueprint $table) {
            $table->index(['gallery_id', 'order'], 'gallery_images_gallery_order_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gallery_images', function (Blueprint $table) {
            $table->dropIndex('gallery_images_gallery_order_index');
        });
    }
};

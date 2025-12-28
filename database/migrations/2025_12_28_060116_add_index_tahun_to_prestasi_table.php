<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Menambahkan index pada kolom tahun untuk optimasi query:
     * Prestasi::orderBy('tahun', 'desc')
     * 
     * Sebelumnya MySQL melakukan File Sort (full table scan)
     * Setelah index: menggunakan B-Tree index untuk sorting
     */
    public function up(): void
    {
        Schema::table('prestasi', function (Blueprint $table) {
            $table->index('tahun', 'prestasi_tahun_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prestasi', function (Blueprint $table) {
            $table->dropIndex('prestasi_tahun_index');
        });
    }
};

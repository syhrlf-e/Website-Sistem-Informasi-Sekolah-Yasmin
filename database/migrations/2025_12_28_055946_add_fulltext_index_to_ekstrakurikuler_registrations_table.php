<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Menambahkan FULLTEXT index pada kolom nama_lengkap dan email
     * untuk optimasi pencarian pendaftar.
     * 
     * Query yang dioptimasi:
     * WHERE nama_lengkap LIKE '%...%' OR email LIKE '%...%'
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE ekstrakurikuler_registrations ADD FULLTEXT INDEX registrations_search_fulltext (nama_lengkap, email)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE ekstrakurikuler_registrations DROP INDEX registrations_search_fulltext');
    }
};

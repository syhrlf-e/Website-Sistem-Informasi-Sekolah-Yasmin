<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Menambahkan FULLTEXT index pada kolom description untuk optimasi pencarian.
     * 
     * Sebelumnya menggunakan LIKE '%...%' yang tidak bisa menggunakan index.
     * Setelah ini bisa menggunakan MATCH AGAINST untuk pencarian yang lebih cepat.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE activity_logs ADD FULLTEXT INDEX activity_logs_description_fulltext (description)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE activity_logs DROP INDEX activity_logs_description_fulltext');
    }
};

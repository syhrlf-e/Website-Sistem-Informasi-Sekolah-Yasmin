<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Copy data from max_peserta to max_participants, then drop max_peserta
     */
    public function up(): void
    {
        // First, copy any existing data from max_peserta to max_participants
        // Only update if max_participants is null but max_peserta has value
        DB::statement('
            UPDATE ekstrakurikuler 
            SET max_participants = max_peserta 
            WHERE max_participants IS NULL AND max_peserta IS NOT NULL
        ');

        // Then drop the old column
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            $table->dropColumn('max_peserta');
        });
    }

    /**
     * Reverse the migrations.
     * Re-create max_peserta and copy data back
     */
    public function down(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            $table->integer('max_peserta')->nullable()->after('gambar');
        });

        // Copy data back from max_participants to max_peserta
        DB::statement('
            UPDATE ekstrakurikuler 
            SET max_peserta = max_participants 
            WHERE max_participants IS NOT NULL
        ');
    }
};

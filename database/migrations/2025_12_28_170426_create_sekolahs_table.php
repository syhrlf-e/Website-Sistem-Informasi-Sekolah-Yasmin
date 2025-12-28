<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sekolahs', function (Blueprint $table) {
            $table->id();
            $table->string('npsn', 20)->unique();
            $table->string('nama', 255);
            $table->string('nama_normalized', 255)->index(); // For search (lowercase, no special chars)
            $table->enum('bentuk', ['SMP', 'MTs'])->index();
            $table->enum('status', ['NEGERI', 'SWASTA'])->index();
            $table->string('akreditasi', 10)->nullable();
            $table->text('alamat_jalan')->nullable();
            $table->string('nama_dusun', 100)->nullable();
            $table->string('kecamatan', 100)->index();
            $table->string('kabupaten', 100)->index();
            $table->string('provinsi', 100)->index();
            $table->string('kode_pos', 10)->nullable();
            $table->decimal('lintang', 10, 7)->nullable();
            $table->decimal('bujur', 10, 7)->nullable();
            $table->timestamps();

            // Full-text index for search
            $table->fullText(['nama', 'nama_normalized', 'kecamatan', 'kabupaten']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekolahs');
    }
};

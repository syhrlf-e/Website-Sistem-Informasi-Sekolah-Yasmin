<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prestasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_prestasi');
            $table->string('slug')->unique();
            $table->enum('kategori', ['Akademik', 'Olahraga', 'Seni']);
            $table->enum('tingkat', ['Sekolah', 'Kota', 'Provinsi', 'Nasional', 'Internasional']);
            $table->year('tahun');
            $table->json('peserta');
            $table->string('image')->nullable();
            $table->text('deskripsi');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasi');
    }
};
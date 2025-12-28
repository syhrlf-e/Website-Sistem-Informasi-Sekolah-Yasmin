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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('author'); // Nama pemberi testimoni
            $table->string('role'); // Jabatan/status (Alumni, Siswa, dll)
            $table->text('text'); // Isi testimoni
            $table->string('photo')->nullable(); // Foto opsional
            $table->boolean('is_active')->default(true); // Status aktif/nonaktif
            $table->integer('order')->default(0); // Urutan tampilan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image'); // Path to uploaded image
            $table->string('link')->nullable(); // Optional link untuk "Selengkapnya"
            $table->date('start_date'); // Tanggal mulai tampil
            $table->date('end_date'); // Tanggal berakhir
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // Untuk ordering (higher = lebih prioritas)
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
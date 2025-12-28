<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            $table->string('tagline')->nullable()->after('nama');
            $table->enum('badge', ['Akademik', 'Olahraga', 'Seni', 'Kepemimpinan'])->default('Akademik')->after('tagline');
            $table->json('benefits')->nullable()->after('deskripsi');
        });
    }
    public function down(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            $table->dropColumn(['tagline', 'badge', 'benefits']);
        });
    }
};
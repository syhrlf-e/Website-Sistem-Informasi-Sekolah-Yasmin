<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('pelajaran');
            $table->string('foto')->nullable();
            $table->timestamps();

            // Index for search optimization
            $table->index('nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru');
    }
};

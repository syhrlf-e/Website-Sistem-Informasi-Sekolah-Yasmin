<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Add unique constraints on NIK and NISN to prevent duplicate registrations
     */
    public function up(): void
    {
        Schema::table('ppdb_registrations', function (Blueprint $table) {
            // Only add NISN unique (NIK was already added in previous partial run)
            $table->unique('nisn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppdb_registrations', function (Blueprint $table) {
            $table->dropUnique(['nisn']);
        });
    }
};

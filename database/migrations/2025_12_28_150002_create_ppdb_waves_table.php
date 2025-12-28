<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create ppdb_waves (Gelombang Pendaftaran) table
 * 
 * Each wave has:
 * - Name (e.g., "Gelombang 1", "Gelombang 2")
 * - Start/End dates for auto-activation
 * - Quota (optional, placeholder for future)
 * - Fee (optional, placeholder for future)
 * - Active status
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('ppdb_waves', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // "Gelombang 1", "Gelombang 2", etc.
            $table->string('academic_year');             // "2025/2026"
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('quota')->nullable();        // Max pendaftar (placeholder)
            $table->decimal('fee', 12, 2)->nullable();   // Biaya pendaftaran (placeholder)
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();     // Info tambahan
            $table->timestamps();

            // Index for active wave lookup
            $table->index(['is_active', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_waves');
    }
};

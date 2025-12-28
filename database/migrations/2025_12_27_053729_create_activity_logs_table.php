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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable(); // Store name in case user deleted
            $table->string('action'); // create, update, delete, login, logout, etc.
            $table->string('model_type')->nullable(); // App\Models\News, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('description'); // Human readable description
            $table->json('old_values')->nullable(); // Before state (for updates)
            $table->json('new_values')->nullable(); // After state (for creates/updates)
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Indexes for common queries
            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

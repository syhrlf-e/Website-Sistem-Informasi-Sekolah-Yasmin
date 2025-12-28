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
        Schema::create('ekstrakurikuler_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekstrakurikuler_id')->constrained('ekstrakurikuler')->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('kelas', 50);
            $table->string('email');
            $table->string('no_whatsapp', 20);
            $table->text('alasan_bergabung');
            $table->string('registration_token');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            // Indexes for better query performance
            $table->index('status');
            $table->index('ekstrakurikuler_id');
            $table->index('email');
            
            // Prevent duplicate registrations
            $table->unique(['email', 'ekstrakurikuler_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekstrakurikuler_registrations');
    }
};

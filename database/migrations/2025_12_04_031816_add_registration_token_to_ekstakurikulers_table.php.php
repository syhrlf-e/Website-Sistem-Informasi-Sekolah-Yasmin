<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            $table->string('registration_token')->nullable()->unique();
            $table->timestamp('token_expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            $table->dropColumn(['registration_token', 'token_expires_at']);
        });
    }
};

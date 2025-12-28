<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop table if exists to recreate with new structure
        Schema::dropIfExists('news');
        
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('location')->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('category')->default('other'); // event, achievement, announcement, other
            $table->string('author')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('published')->default(true);
            $table->integer('views')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('slug');
            $table->index('category');
            $table->index('is_featured');
            $table->index('published');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
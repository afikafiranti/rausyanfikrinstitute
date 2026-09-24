<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playlist_id')->constrained('playlists')->cascadeOnDelete();
            $table->string('title', 180);
            $table->string('youtube_video_id', 64)->unique();
            $table->unsignedInteger('duration_sec')->nullable();
            $table->integer('order_index')->default(0);
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->index(['playlist_id', 'order_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};

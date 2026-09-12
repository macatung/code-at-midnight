<?php

declare(strict_types=1);

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
        if (!Schema::hasTable('article_shorts')) {
            Schema::create('article_shorts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
                $table->string('title', 255);
                $table->string('focus_hook', 255)->nullable();
                $table->string('visual_style', 100)->nullable()->default('Buddha Majestic Golden Glow');
                $table->string('video_url', 500);
                $table->string('thumbnail_url', 500)->nullable();
                $table->string('duration', 50)->nullable()->default('00:30');
                $table->text('script')->nullable();
                $table->text('display_text')->nullable();
                $table->text('spoken_text')->nullable();
                $table->string('youtube_shorts_url', 500)->nullable();
                $table->string('tiktok_url', 500)->nullable();
                $table->string('reels_url', 500)->nullable();
                $table->unsignedInteger('order_index')->default(0);
                $table->string('status', 50)->default('completed');
                $table->timestamps();

                $table->index(['article_id', 'order_index']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_shorts');
    }
};
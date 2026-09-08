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
        Schema::table('articles', function (Blueprint $table) {
            $table->string('video_status', 32)->default('draft')->index()->after('is_published');
            $table->string('video_long_url', 500)->nullable()->after('video_status');
            $table->string('video_short_url', 500)->nullable()->after('video_long_url');
            $table->string('thumbnail_long_url', 500)->nullable()->after('video_short_url');
            $table->string('thumbnail_short_url', 500)->nullable()->after('thumbnail_long_url');
            $table->string('video_long_duration', 32)->nullable()->after('thumbnail_short_url');
            $table->string('video_short_duration', 32)->nullable()->after('video_long_duration');
            $table->longText('script_long')->nullable()->after('video_short_duration');
            $table->text('script_short')->nullable()->after('script_long');
            $table->string('seo_title', 255)->nullable()->after('script_short');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->text('social_caption')->nullable()->after('seo_description');
            $table->json('hashtags')->nullable()->after('social_caption');
            $table->string('pipeline_task_id', 64)->nullable()->after('hashtags');
            $table->timestamp('pipeline_started_at')->nullable()->after('pipeline_task_id');
            $table->timestamp('pipeline_completed_at')->nullable()->after('pipeline_started_at');
            $table->text('pipeline_error')->nullable()->after('pipeline_completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'video_status',
                'video_long_url',
                'video_short_url',
                'thumbnail_long_url',
                'thumbnail_short_url',
                'video_long_duration',
                'video_short_duration',
                'script_long',
                'script_short',
                'seo_title',
                'seo_description',
                'social_caption',
                'hashtags',
                'pipeline_task_id',
                'pipeline_started_at',
                'pipeline_completed_at',
                'pipeline_error',
            ]);
        });
    }
};

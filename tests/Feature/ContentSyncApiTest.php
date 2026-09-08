<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentSyncApiTest extends TestCase
{
    use RefreshDatabase;

    protected string $secretToken = 'macatung_cms_sync_token_secret_2026';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.cms.sync_secret' => $this->secretToken]);
    }

    public function test_sync_fails_without_authentication(): void
    {
        $response = $this->postJson('/api/v1/content-sync', [
            'slug' => 'test-article',
            'status' => 'completed',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'error' => 'Unauthorized',
            ]);
    }

    public function test_sync_fails_with_invalid_bearer_token(): void
    {
        $response = $this->withToken('wrong_invalid_secret_token')
            ->postJson('/api/v1/content-sync', [
                'slug' => 'test-article',
                'status' => 'completed',
            ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'error' => 'Unauthorized',
            ]);
    }

    public function test_sync_fails_validation_when_identifiers_missing(): void
    {
        $response = $this->withToken($this->secretToken)
            ->postJson('/api/v1/content-sync', [
                'status' => 'completed',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => 'Validation failed',
            ]);
    }

    public function test_sync_fails_validation_with_invalid_status(): void
    {
        $response = $this->withToken($this->secretToken)
            ->postJson('/api/v1/content-sync', [
                'slug' => 'test-article',
                'status' => 'unknown_invalid_status',
            ]);

        $response->assertStatus(422);
    }

    public function test_sync_returns_404_when_article_not_found(): void
    {
        $response = $this->withToken($this->secretToken)
            ->postJson('/api/v1/content-sync', [
                'slug' => 'non-existent-article-slug',
                'status' => 'completed',
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'error' => 'Article not found',
            ]);
    }

    public function test_sync_succeeds_and_updates_article_by_id(): void
    {
        $article = Article::create([
            'site_domain' => 'theravada',
            'title' => 'Dập Tắt Ngọn Lửa Sân',
            'slug' => 'dap-tat-ngon-lua-san',
            'category' => 'phap-thoai',
            'content' => 'Nội dung bài viết giáo lý...',
            'reading_time_min' => 10,
            'is_published' => true,
            'video_status' => 'processing',
        ]);

        $payload = [
            'job_id' => 'job_20260907_173',
            'article_id' => $article->id,
            'slug' => $article->slug,
            'status' => 'completed',
            'media' => [
                'video_16x9' => [
                    'cdn_url' => 'https://storage.googleapis.com/nendoi-marketing-assets/videos/dap_tat_ngon_lua_san_long_1080p.mp4',
                    'duration_seconds' => 842.0,
                    'resolution' => '1920x1080',
                ],
                'video_9x16' => [
                    'cdn_url' => 'https://storage.googleapis.com/nendoi-marketing-assets/videos/dap_tat_ngon_lua_san_short_9x16.mp4',
                    'duration_seconds' => 42.0,
                    'resolution' => '1080x1920',
                ],
                'thumbnails' => [
                    'thumb_16x9_url' => 'https://storage.googleapis.com/nendoi-marketing-assets/thumbnails/dap_tat_ngon_lua_san_thumb_16x9.jpg',
                    'thumb_9x16_url' => 'https://storage.googleapis.com/nendoi-marketing-assets/thumbnails/dap_tat_ngon_lua_san_thumb_9x16.jpg',
                ],
            ],
            'scripts' => [
                'longform' => [
                    'title' => 'Tâm An Vạn Sự An | Dập Tắt Ngọn Lửa Sân',
                    'word_count' => 2240,
                    'content_markdown' => '# Kịch bản dài 16:9...',
                ],
                'reel_short' => [
                    'hook' => '🌿 NẮM THAN HỒNG TỰ ĐỐT',
                    'word_count' => 135,
                    'content_text' => 'Kịch bản reel 9:16 ngắn gọn...',
                ],
            ],
            'seo_metadata' => [
                'brand_title' => 'Tâm An Vạn Sự An | Dập Tắt Ngọn Lửa Sân',
                'youtube_title' => 'Tâm An Vạn Sự An — Dập Tắt Ngọn Lửa Sân Giữ Tâm Bình An',
                'youtube_description' => 'Mô tả video bài học Phật pháp...',
                'captions' => [
                    'facebook' => 'Caption Facebook chi tiết...',
                    'tiktok' => 'Caption TikTok...',
                ],
                'hashtags' => ['#TamAnVanSuAn', '#LoiPhatDay', '#Theravada'],
            ],
            'timings' => [
                'triggered_at' => '2026-09-07T08:15:25Z',
                'completed_at' => '2026-09-07T08:21:40Z',
            ],
        ];

        $response = $this->withToken($this->secretToken)
            ->postJson('/api/v1/content-sync', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'article_id' => $article->id,
                'slug' => 'dap-tat-ngon-lua-san',
                'video_status' => 'completed',
            ]);

        $article->refresh();

        $this->assertSame('completed', $article->video_status);
        $this->assertSame('job_20260907_173', $article->pipeline_task_id);
        $this->assertSame('https://storage.googleapis.com/nendoi-marketing-assets/videos/dap_tat_ngon_lua_san_long_1080p.mp4', $article->video_long_url);
        $this->assertSame('https://storage.googleapis.com/nendoi-marketing-assets/videos/dap_tat_ngon_lua_san_short_9x16.mp4', $article->video_short_url);
        $this->assertSame('https://storage.googleapis.com/nendoi-marketing-assets/thumbnails/dap_tat_ngon_lua_san_thumb_16x9.jpg', $article->thumbnail_long_url);
        $this->assertSame('https://storage.googleapis.com/nendoi-marketing-assets/thumbnails/dap_tat_ngon_lua_san_thumb_9x16.jpg', $article->thumbnail_short_url);
        $this->assertSame('# Kịch bản dài 16:9...', $article->script_long);
        $this->assertSame('Kịch bản reel 9:16 ngắn gọn...', $article->script_short);
        $this->assertSame('Tâm An Vạn Sự An | Dập Tắt Ngọn Lửa Sân', $article->seo_title);
        $this->assertSame('Mô tả video bài học Phật pháp...', $article->seo_description);
        $this->assertSame('Caption Facebook chi tiết...', $article->social_caption);
        $this->assertSame(['#TamAnVanSuAn', '#LoiPhatDay', '#Theravada'], $article->hashtags);
        $this->assertNotNull($article->pipeline_started_at);
        $this->assertNotNull($article->pipeline_completed_at);
    }

    public function test_sync_succeeds_by_slug_and_with_custom_header(): void
    {
        $article = Article::create([
            'site_domain' => 'theravada',
            'title' => 'Quán Chiếu Vô Thường',
            'slug' => 'quan-chieu-vo-thuong',
            'content' => 'Nội dung...',
            'reading_time_min' => 5,
            'is_published' => true,
            'video_status' => 'draft',
        ]);

        $response = $this->withHeaders(['X-Content-Sync-Token' => $this->secretToken])
            ->postJson('/api/v1/content-sync', [
                'slug' => 'quan-chieu-vo-thuong',
                'status' => 'completed',
                'video_long_url' => 'https://cdn.example.com/video169.mp4',
                'video_short_url' => 'https://cdn.example.com/video916.mp4',
            ]);

        $response->assertStatus(200);

        $article->refresh();
        $this->assertSame('completed', $article->video_status);
        $this->assertSame('https://cdn.example.com/video169.mp4', $article->video_long_url);
        $this->assertSame('https://cdn.example.com/video916.mp4', $article->video_short_url);
    }

    public function test_sync_handles_pipeline_failure_status(): void
    {
        $article = Article::create([
            'site_domain' => 'theravada',
            'title' => 'Bài Học Thử Thách',
            'slug' => 'bai-hoc-thu-thach',
            'content' => 'Nội dung...',
            'video_status' => 'processing',
        ]);

        $response = $this->withToken($this->secretToken)
            ->postJson('/api/v1/content-sync', [
                'slug' => 'bai-hoc-thu-thach',
                'status' => 'failed',
                'error_message' => 'FFmpeg out-of-memory error during 1080p render',
            ]);

        $response->assertStatus(200);

        $article->refresh();
        $this->assertSame('failed', $article->video_status);
        $this->assertSame('FFmpeg out-of-memory error during 1080p render', $article->pipeline_error);
    }
}

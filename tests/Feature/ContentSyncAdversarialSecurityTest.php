<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentSyncAdversarialSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected string $validSecretToken = 'macatung_cms_sync_token_secret_2026';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.cms.sync_secret' => $this->validSecretToken]);
    }

    /**
     * 1. Test missing Authorization header -> assert 401 Unauthorized.
     */
    public function test_missing_authorization_header_returns_401(): void
    {
        $response = $this->postJson('/api/v1/content-sync', [
            'slug' => 'sample-article',
            'status' => 'completed',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'error' => 'Unauthorized',
            ]);
    }

    /**
     * 2. Test invalid / corrupted Bearer token -> assert 401 Unauthorized.
     */
    public function test_invalid_and_corrupted_bearer_tokens_return_401(): void
    {
        // Case 2a: Empty Bearer token
        $resEmpty = $this->withToken('')
            ->postJson('/api/v1/content-sync', ['slug' => 'sample-article', 'status' => 'completed']);
        $resEmpty->assertStatus(401)
            ->assertJson(['success' => false, 'error' => 'Unauthorized']);

        // Case 2b: Wrong / Malicious Bearer token
        $resWrong = $this->withToken('attacker_supplied_fake_secret_token_12345')
            ->postJson('/api/v1/content-sync', ['slug' => 'sample-article', 'status' => 'completed']);
        $resWrong->assertStatus(401)
            ->assertJson(['success' => false, 'error' => 'Unauthorized']);

        // Case 2c: Partially matching token (prefix match attempt)
        $partialToken = substr($this->validSecretToken, 0, 10);
        $resPartial = $this->withToken($partialToken)
            ->postJson('/api/v1/content-sync', ['slug' => 'sample-article', 'status' => 'completed']);
        $resPartial->assertStatus(401)
            ->assertJson(['success' => false, 'error' => 'Unauthorized']);

        // Case 2d: Wrong custom header value
        $resBadHeader = $this->withHeaders(['X-Content-Sync-Token' => 'invalid_header_token'])
            ->postJson('/api/v1/content-sync', ['slug' => 'sample-article', 'status' => 'completed']);
        $resBadHeader->assertStatus(401)
            ->assertJson(['success' => false, 'error' => 'Unauthorized']);
    }

    /**
     * 3. Test missing required fields -> assert 422 Unprocessable Entity.
     */
    public function test_missing_required_fields_return_422(): void
    {
        // Case 3a: Missing status field completely
        $resNoStatus = $this->withToken($this->validSecretToken)
            ->postJson('/api/v1/content-sync', [
                'slug' => 'sample-article',
            ]);
        $resNoStatus->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => 'Validation failed',
            ]);
        $this->assertArrayHasKey('status', $resNoStatus->json('messages'));

        // Case 3b: Missing both article_id and slug
        $resNoIdentifier = $this->withToken($this->validSecretToken)
            ->postJson('/api/v1/content-sync', [
                'status' => 'completed',
            ]);
        $resNoIdentifier->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => 'Validation failed',
            ]);
        $this->assertArrayHasKey('slug', $resNoIdentifier->json('messages'));

        // Case 3c: Invalid status enum value
        $resInvalidStatus = $this->withToken($this->validSecretToken)
            ->postJson('/api/v1/content-sync', [
                'slug' => 'sample-article',
                'status' => 'invalid_enum_not_allowed',
            ]);
        $resInvalidStatus->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => 'Validation failed',
            ]);
        $this->assertArrayHasKey('status', $resInvalidStatus->json('messages'));
    }

    /**
     * 4. Test non-existent article slug -> assert 404 Not Found.
     */
    public function test_non_existent_article_returns_404(): void
    {
        // Case 4a: Non-existent slug
        $resSlug = $this->withToken($this->validSecretToken)
            ->postJson('/api/v1/content-sync', [
                'slug' => 'absolutely-non-existent-slug-xyz-999',
                'status' => 'completed',
            ]);
        $resSlug->assertStatus(404)
            ->assertJson([
                'success' => false,
                'error' => 'Article not found',
            ]);

        // Case 4b: Non-existent article_id
        $resId = $this->withToken($this->validSecretToken)
            ->postJson('/api/v1/content-sync', [
                'article_id' => 999999,
                'status' => 'completed',
            ]);
        $resId->assertStatus(404)
            ->assertJson([
                'success' => false,
                'error' => 'Article not found',
            ]);

        // Case 4c: Both non-existent article_id and slug
        $resBoth = $this->withToken($this->validSecretToken)
            ->postJson('/api/v1/content-sync', [
                'article_id' => 999999,
                'slug' => 'non-existent-slug',
                'status' => 'completed',
            ]);
        $resBoth->assertStatus(404)
            ->assertJson([
                'success' => false,
                'error' => 'Article not found',
            ]);
    }

    /**
     * 5. Test SQL injection payloads in slug or job_id -> assert safe rejection.
     */
    public function test_sql_injection_payloads_safely_rejected(): void
    {
        // Create a legitimate baseline article
        $legitArticle = Article::create([
            'site_domain' => 'theravada',
            'title' => 'Chánh Niệm Tỉnh Giác',
            'slug' => 'chanh-niem-tinh-giac',
            'content' => 'Nội dung giáo lý chánh niệm...',
            'reading_time_min' => 7,
            'is_published' => true,
            'video_status' => 'draft',
        ]);

        $initialCount = Article::count();

        $sqliPayloads = [
            "' OR '1'='1",
            "chanh-niem-tinh-giac' OR 1=1 --",
            "'; DROP TABLE articles; --",
            "chanh-niem' UNION SELECT 1, 'hacked', 'hacked', 'hacked', 1, 1, 'hacked' --",
            "\" OR \"\"=\"",
            "admin'--",
            "1; WAITFOR DELAY '0:0:5'--",
            "'; DELETE FROM articles WHERE 1=1; --",
        ];

        foreach ($sqliPayloads as $payload) {
            $response = $this->withToken($this->validSecretToken)
                ->postJson('/api/v1/content-sync', [
                    'slug' => $payload,
                    'status' => 'completed',
                ]);

            // Must NOT crash with 500 error
            $this->assertNotSame(500, $response->status(), "SQLi payload in slug caused 500 error: {$payload}");

            // Must safely return 404 (or 422 if string validation triggered), never 200 modifying legit article
            $this->assertSame(404, $response->status(), "SQLi payload in slug unexpectedly bypassed 404: {$payload}");
        }

        // Test SQL injection payload in job_id
        $jobIdPayload = "job_test'; DROP TABLE articles; --";
        $responseJob = $this->withToken($this->validSecretToken)
            ->postJson('/api/v1/content-sync', [
                'article_id' => $legitArticle->id,
                'slug' => $legitArticle->slug,
                'job_id' => $jobIdPayload,
                'status' => 'completed',
            ]);

        // Must succeed without executing SQL injection
        $responseJob->assertStatus(200);

        // Verify database table and count remain completely intact
        $this->assertSame($initialCount, Article::count(), "Articles count changed after SQLi attempt!");

        // Verify that the job_id was stored literally as a string, not executed
        $legitArticle->refresh();
        $this->assertSame($jobIdPayload, $legitArticle->pipeline_task_id);
        $this->assertSame('completed', $legitArticle->video_status);
    }

    /**
     * 6. Test massive/nested JSON payload -> assert robust handling without server crash.
     */
    public function test_massive_and_nested_json_payloads_handled_robustly(): void
    {
        $article = Article::create([
            'site_domain' => 'theravada',
            'title' => 'Tâm Từ Quán Chiếu',
            'slug' => 'tam-tu-quan-chieu',
            'content' => 'Nội dung tâm từ quán...',
            'reading_time_min' => 12,
            'is_published' => true,
            'video_status' => 'draft',
        ]);

        // Generate 300KB of markdown script text (massive longform)
        $massiveMarkdown = str_repeat("## Phân Cảnh Quán Niệm Tâm Từ\n\nNội dung phân cảnh dài mô tả chi tiết với đầy đủ lời bình tâm từ...\n\n", 2000);

        // Deeply nested arbitrary metadata structures
        $deeplyNestedMedia = [
            'video_16x9' => [
                'cdn_url' => 'https://storage.googleapis.com/nendoi-marketing-assets/videos/tam_tu_long_1080p.mp4',
                'duration_seconds' => 890.5,
                'nested_layer_1' => [
                    'nested_layer_2' => [
                        'nested_layer_3' => [
                            'codec' => 'h264',
                            'bitrate' => 8000,
                            'custom_filters' => ['zoompan', 'fade', 'eq'],
                        ],
                    ],
                ],
            ],
            'video_9x16' => [
                'cdn_url' => 'https://storage.googleapis.com/nendoi-marketing-assets/videos/tam_tu_short_9x16.mp4',
                'duration_seconds' => 45.0,
            ],
            'thumbnails' => [
                'thumb_16x9_url' => 'https://storage.googleapis.com/nendoi-marketing-assets/thumbnails/tam_tu_16x9.jpg',
                'thumb_9x16_url' => 'https://storage.googleapis.com/nendoi-marketing-assets/thumbnails/tam_tu_9x16.jpg',
            ],
        ];

        // 200 hashtags
        $largeHashtagList = array_map(fn($i) => "#TagNumber{$i}", range(1, 200));

        $massivePayload = [
            'article_id' => $article->id,
            'slug' => $article->slug,
            'status' => 'completed',
            'job_id' => 'job_massive_stress_001',
            'media' => $deeplyNestedMedia,
            'scripts' => [
                'longform' => [
                    'title' => 'Tâm Từ Quán Chiếu',
                    'word_count' => 12000,
                    'content_markdown' => $massiveMarkdown,
                ],
                'reel_short' => [
                    'hook' => '🌿 TÂM TỪ AN LẠC',
                    'word_count' => 140,
                    'content_text' => 'Ngắn gọn...',
                ],
            ],
            'seo_metadata' => [
                'brand_title' => 'Tâm An Vạn Sự An | Tâm Từ Quán Chiếu',
                'youtube_description' => str_repeat("Mô tả chi tiết với đầy đủ liên kết và hướng dẫn thực hành thiền định tâm từ...\n", 100),
                'captions' => [
                    'facebook' => 'Facebook caption test...',
                ],
                'hashtags' => $largeHashtagList,
            ],
            'unexpected_injected_root_key' => [
                'attack_simulation' => 'should_be_ignored_by_controller',
            ],
        ];

        $response = $this->withToken($this->validSecretToken)
            ->postJson('/api/v1/content-sync', $massivePayload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'article_id' => $article->id,
                'video_status' => 'completed',
            ]);

        $article->refresh();
        $this->assertSame('completed', $article->video_status);
        $this->assertSame(trim($massiveMarkdown), $article->script_long);
        $this->assertCount(200, $article->hashtags);
    }

    /**
     * 7. Test idempotent retry: dispatching the same completed payload twice -> assert both return 200 without duplicate records.
     */
    public function test_idempotent_retry_dispatching_same_payload_twice(): void
    {
        $article = Article::create([
            'site_domain' => 'theravada',
            'title' => 'Quán Thân Bất Tịnh',
            'slug' => 'quan-than-bat-tinh',
            'content' => 'Nội dung bài viết giáo lý...',
            'reading_time_min' => 8,
            'is_published' => true,
            'video_status' => 'processing',
        ]);

        $initialArticleCount = Article::count();

        $completedPayload = [
            'job_id' => 'job_idempotent_retry_999',
            'article_id' => $article->id,
            'slug' => $article->slug,
            'status' => 'completed',
            'media' => [
                'video_16x9' => [
                    'cdn_url' => 'https://storage.googleapis.com/nendoi-marketing-assets/videos/quan_than_long.mp4',
                    'duration_seconds' => 750.0,
                ],
                'video_9x16' => [
                    'cdn_url' => 'https://storage.googleapis.com/nendoi-marketing-assets/videos/quan_than_short.mp4',
                    'duration_seconds' => 38.0,
                ],
                'thumbnails' => [
                    'thumb_16x9_url' => 'https://storage.googleapis.com/nendoi-marketing-assets/thumbnails/quan_than_16x9.jpg',
                    'thumb_9x16_url' => 'https://storage.googleapis.com/nendoi-marketing-assets/thumbnails/quan_than_9x16.jpg',
                ],
            ],
            'scripts' => [
                'longform' => ['content_markdown' => '# Kịch bản dài quán thân'],
                'reel_short' => ['content_text' => 'Kịch bản ngắn quán thân'],
            ],
            'seo_metadata' => [
                'brand_title' => 'Tâm An Vạn Sự An | Quán Thân',
                'hashtags' => ['#Theravada', '#TamAn'],
            ],
        ];

        // First dispatch
        $response1 = $this->withToken($this->validSecretToken)
            ->postJson('/api/v1/content-sync', $completedPayload);

        $response1->assertStatus(200)
            ->assertJson([
                'success' => true,
                'article_id' => $article->id,
                'slug' => $article->slug,
                'video_status' => 'completed',
            ]);

        $article->refresh();
        $this->assertSame('completed', $article->video_status);
        $this->assertSame('job_idempotent_retry_999', $article->pipeline_task_id);
        $firstCompletedAt = $article->pipeline_completed_at;
        $this->assertNotNull($firstCompletedAt);

        // Second dispatch (identical payload - simulation of network retry or duplicate n8n webhook)
        $response2 = $this->withToken($this->validSecretToken)
            ->postJson('/api/v1/content-sync', $completedPayload);

        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'article_id' => $article->id,
                'slug' => $article->slug,
                'video_status' => 'completed',
            ]);

        // Assert no duplicate records were created
        $this->assertSame($initialArticleCount, Article::count(), 'Idempotent retries created duplicate articles!');

        $article->refresh();
        $this->assertSame('completed', $article->video_status);
        $this->assertSame('job_idempotent_retry_999', $article->pipeline_task_id);
        $this->assertSame('https://storage.googleapis.com/nendoi-marketing-assets/videos/quan_than_long.mp4', $article->video_long_url);
    }

    /**
     * 8. Test boundary condition: job_id string length boundary (64 chars vs >64 chars).
     */
    public function test_job_id_boundary_lengths(): void
    {
        $article = Article::create([
            'site_domain' => 'theravada',
            'title' => 'Boundary Testing Article',
            'slug' => 'boundary-testing-article',
            'content' => 'Content...',
            'reading_time_min' => 5,
            'video_status' => 'draft',
        ]);

        // 64-char job_id: fits within VARCHAR(64) schema
        $jobId64 = str_repeat('a', 64);
        $res64 = $this->withToken($this->validSecretToken)
            ->postJson('/api/v1/content-sync', [
                'article_id' => $article->id,
                'status' => 'completed',
                'job_id' => $jobId64,
            ]);

        $res64->assertStatus(200);
        $article->refresh();
        $this->assertSame($jobId64, $article->pipeline_task_id);

        // 129-char job_id: exceeds validator max:128 -> returns 422
        $jobId129 = str_repeat('b', 129);
        $res129 = $this->withToken($this->validSecretToken)
            ->postJson('/api/v1/content-sync', [
                'article_id' => $article->id,
                'status' => 'completed',
                'job_id' => $jobId129,
            ]);

        $res129->assertStatus(422);
        $this->assertArrayHasKey('job_id', $res129->json('messages'));
    }
}

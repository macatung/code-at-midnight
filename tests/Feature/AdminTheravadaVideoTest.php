<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Article;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminTheravadaVideoTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateAdmin(): void
    {
        SiteSetting::set('admin_password', 'macatung@midnight2026');
        $this->withSession(['admin_authenticated' => true]);
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin/theravada/videos');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_videos_index(): void
    {
        $this->authenticateAdmin();

        Article::create([
            'site_domain' => 'theravada',
            'title' => 'Bài Giảng Một',
            'slug' => 'bai-giang-mot',
            'content' => 'Nội dung một...',
            'video_status' => 'draft',
        ]);

        Article::create([
            'site_domain' => 'theravada',
            'title' => 'Bài Giảng Hai',
            'slug' => 'bai-giang-hai',
            'content' => 'Nội dung hai...',
            'video_status' => 'completed',
        ]);

        $response = $this->get('/admin/theravada/videos');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Theravada/Videos/Index')
            ->has('articles.data', 2)
            ->has('statusCounts')
            ->where('statusCounts.completed', 1)
            ->where('statusCounts.draft', 1)
        );
    }

    public function test_admin_can_filter_and_search_videos(): void
    {
        $this->authenticateAdmin();

        Article::create([
            'site_domain' => 'theravada',
            'title' => 'Dập Tắt Ngọn Lửa Sân',
            'slug' => 'dap-tat-ngon-lua-san',
            'content' => 'Nội dung...',
            'video_status' => 'completed',
        ]);

        Article::create([
            'site_domain' => 'theravada',
            'title' => 'Tứ Diệu Đế',
            'slug' => 'tu-dieu-de',
            'content' => 'Nội dung...',
            'video_status' => 'draft',
        ]);

        // Filter by status completed
        $response = $this->get('/admin/theravada/videos?status=completed');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Theravada/Videos/Index')
            ->has('articles.data', 1)
            ->where('articles.data.0.slug', 'dap-tat-ngon-lua-san')
        );

        // Search by keyword
        $responseSearch = $this->get('/admin/theravada/videos?search=Diệu');
        $responseSearch->assertStatus(200);
        $responseSearch->assertInertia(fn ($page) => $page
            ->component('Admin/Theravada/Videos/Index')
            ->has('articles.data', 1)
            ->where('articles.data.0.slug', 'tu-dieu-de')
        );
    }

    public function test_admin_can_view_video_show_detail(): void
    {
        $this->authenticateAdmin();

        $article = Article::create([
            'site_domain' => 'theravada',
            'title' => 'Dập Tắt Ngọn Lửa Sân',
            'pali_title' => 'Kodhana Sutta',
            'slug' => 'dap-tat-ngon-lua-san',
            'content' => 'Nội dung gốc...',
            'video_status' => 'completed',
            'video_long_url' => 'https://cdn.example.com/long.mp4',
            'video_short_url' => 'https://cdn.example.com/short.mp4',
            'thumbnail_long_url' => 'https://cdn.example.com/thumb169.jpg',
            'thumbnail_short_url' => 'https://cdn.example.com/thumb916.jpg',
            'script_long' => 'Kịch bản dài...',
            'script_short' => 'Kịch bản ngắn...',
            'seo_title' => 'Tâm An Vạn Sự An | Dập Tắt Ngọn Lửa Sân',
            'hashtags' => ['#TamAnVanSuAn', '#LoiPhatDay'],
        ]);

        $response = $this->get("/admin/theravada/videos/{$article->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Theravada/Videos/Show')
            ->where('article.id', $article->id)
            ->where('article.title', 'Dập Tắt Ngọn Lửa Sân')
            ->where('article.video_long_url', 'https://cdn.example.com/long.mp4')
            ->where('article.seo_title', 'Tâm An Vạn Sự An | Dập Tắt Ngọn Lửa Sân')
        );
    }

    public function test_admin_can_trigger_video_pipeline(): void
    {
        $this->authenticateAdmin();
        Http::fake([
            'https://workflow.macatung.dev/*' => Http::response([
                'success' => true,
                'job_id' => 'job_test_12345',
                'status' => 'ACCEPTED',
            ], 202),
        ]);

        $article = Article::create([
            'site_domain' => 'theravada',
            'title' => 'Pháp Thoại Mới',
            'slug' => 'phap-thoai-moi',
            'content' => 'Nội dung pháp thoại...',
            'video_status' => 'draft',
        ]);

        $response = $this->from("/admin/theravada/videos/{$article->id}")
            ->post("/admin/theravada/videos/{$article->id}/trigger");

        $response->assertRedirect("/admin/theravada/videos/{$article->id}");
        $response->assertSessionHas('success');

        $article->refresh();
        $this->assertSame('processing', $article->video_status);
        $this->assertSame('job_test_12345', $article->pipeline_task_id);
        $this->assertNotNull($article->pipeline_started_at);
    }

    public function test_admin_can_toggle_publish_status(): void
    {
        $this->authenticateAdmin();

        $article = Article::create([
            'site_domain' => 'theravada',
            'title' => 'Pháp Thoại Hoàn Thành',
            'slug' => 'phap-thoai-hoan-thanh',
            'content' => 'Nội dung...',
            'video_status' => 'completed',
        ]);

        // 1. First toggle -> Published
        $response = $this->from("/admin/theravada/videos/{$article->id}")
            ->patch("/admin/theravada/videos/{$article->id}/publish");

        $response->assertRedirect("/admin/theravada/videos/{$article->id}");
        $response->assertSessionHas('success');

        $article->refresh();
        $this->assertSame('published', $article->video_status);
        $this->assertTrue($article->is_published);

        // 2. Second toggle -> Completed
        $response2 = $this->from("/admin/theravada/videos/{$article->id}")
            ->patch("/admin/theravada/videos/{$article->id}/publish");

        $response2->assertRedirect("/admin/theravada/videos/{$article->id}");

        $article->refresh();
        $this->assertSame('completed', $article->video_status);
    }

    public function test_subdomain_routes_work(): void
    {
        $this->authenticateAdmin();

        $article = Article::create([
            'site_domain' => 'theravada',
            'title' => 'Kinh Pháp Cú',
            'slug' => 'kinh-phap-cu',
            'content' => 'Nội dung kinh...',
            'video_status' => 'completed',
        ]);

        $response = $this->get('http://theravada.macatung.dev/admin/videos');
        $response->assertStatus(200);

        $responseShow = $this->get("http://theravada.macatung.dev/admin/videos/{$article->id}");
        $responseShow->assertStatus(200);
    }
}

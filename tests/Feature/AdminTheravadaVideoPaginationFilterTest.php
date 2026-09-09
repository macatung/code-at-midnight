<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Article;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTheravadaVideoPaginationFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateAdmin(): void
    {
        SiteSetting::set('admin_password', 'macatung@midnight2026');
        $this->withSession(['admin_authenticated' => true]);
    }

    /**
     * Test pagination with 25 articles verifies 12 per page and query string retention.
     */
    public function test_pagination_breaks_at_12_and_preserves_query_string(): void
    {
        $this->authenticateAdmin();

        // Create 25 articles (15 completed, 10 draft)
        for ($i = 1; $i <= 15; $i++) {
            Article::create([
                'site_domain' => 'theravada',
                'title' => "Bài Giảng Completed {$i}",
                'slug' => "bai-giang-completed-{$i}",
                'content' => "Nội dung {$i}...",
                'video_status' => 'completed',
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            Article::create([
                'site_domain' => 'theravada',
                'title' => "Bài Giảng Draft {$i}",
                'slug' => "bai-giang-draft-{$i}",
                'content' => "Nội dung draft {$i}...",
                'video_status' => 'draft',
            ]);
        }

        // 1. Unfiltered page 1: 12 items, 3 pages total (25 items)
        $response = $this->get('/admin/theravada/videos');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Theravada/Videos/Index')
            ->has('articles.data', 12)
            ->where('articles.total', 25)
            ->where('articles.per_page', 12)
            ->where('articles.current_page', 1)
            ->where('articles.last_page', 3)
            ->where('statusCounts.all', 25)
            ->where('statusCounts.completed', 15)
            ->where('statusCounts.draft', 10)
        );

        // 2. Filtered by status=completed: 15 items -> page 1 has 12 items, page 2 has 3 items
        $responseFilteredPage1 = $this->get('/admin/theravada/videos?status=completed');
        $responseFilteredPage1->assertStatus(200);
        $responseFilteredPage1->assertInertia(fn ($page) => $page
            ->component('Admin/Theravada/Videos/Index')
            ->has('articles.data', 12)
            ->where('articles.total', 15)
            ->where('articles.last_page', 2)
            ->where('statusCounts.all', 25) // global counts unaffected
            ->where('statusCounts.completed', 15)
        );

        // Check page 2 of filtered results
        $responseFilteredPage2 = $this->get('/admin/theravada/videos?status=completed&page=2');
        $responseFilteredPage2->assertStatus(200);
        $responseFilteredPage2->assertInertia(fn ($page) => $page
            ->component('Admin/Theravada/Videos/Index')
            ->has('articles.data', 3)
            ->where('articles.current_page', 2)
        );
    }

    /**
     * Test combined search and status filter with pagination query string.
     */
    public function test_search_and_status_combined_filtering(): void
    {
        $this->authenticateAdmin();

        Article::create([
            'site_domain' => 'theravada',
            'title' => 'Kinh Bát Nhã Tâm Kinh',
            'pali_title' => 'Prajnaparamita Hrdaya',
            'slug' => 'bat-nha-tam-kinh',
            'content' => 'Nội dung...',
            'video_status' => 'completed',
        ]);

        Article::create([
            'site_domain' => 'theravada',
            'title' => 'Kinh Bát Nhã Phần 2',
            'pali_title' => 'Prajnaparamita Dutiya',
            'slug' => 'bat-nha-phan-2',
            'content' => 'Nội dung...',
            'video_status' => 'draft',
        ]);

        Article::create([
            'site_domain' => 'theravada',
            'title' => 'Kinh Pháp Cú Câu 1',
            'pali_title' => 'Dhammapada Gatha 1',
            'slug' => 'phap-cu-cau-1',
            'content' => 'Nội dung...',
            'video_status' => 'completed',
        ]);

        // Search "Bát Nhã" + status "completed" -> should only return 1
        $response = $this->get('/admin/theravada/videos?search=Bát+Nhã&status=completed');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Theravada/Videos/Index')
            ->has('articles.data', 1)
            ->where('articles.data.0.slug', 'bat-nha-tam-kinh')
            ->where('filters.status', 'completed')
            ->where('filters.search', 'Bát Nhã')
        );

        // Search by Pali title
        $responsePali = $this->get('/admin/theravada/videos?search=Dhammapada');
        $responsePali->assertStatus(200);
        $responsePali->assertInertia(fn ($page) => $page
            ->component('Admin/Theravada/Videos/Index')
            ->has('articles.data', 1)
            ->where('articles.data.0.slug', 'phap-cu-cau-1')
        );

        // Search by slug
        $responseSlug = $this->get('/admin/theravada/videos?search=bat-nha-phan-2');
        $responseSlug->assertStatus(200);
        $responseSlug->assertInertia(fn ($page) => $page
            ->component('Admin/Theravada/Videos/Index')
            ->has('articles.data', 1)
            ->where('articles.data.0.slug', 'bat-nha-phan-2')
        );
    }
}

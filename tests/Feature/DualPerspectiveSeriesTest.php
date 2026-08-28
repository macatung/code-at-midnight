<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DualPerspectiveSeriesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test all 4 pairs exist and are bidirectionally linked.
     */
    public function test_dual_perspective_series_seeding_and_relationships(): void
    {
        $pairs = [
            ['chiec-man-hinh-khong-bao-gio-tat-fomo-doi-song', 'ai-thu-con-khat-trien-mien-fomo-vipassana'],
            ['cuoc-dua-khong-co-vach-dich-ap-luc-thanh-cong', 'day-dan-gay-vua-van-trung-dao-chua-lanh-burnout'],
            ['tam-guong-vo-cua-su-so-sanh-vo-boc-xa-hoi', 'can-benh-so-sanh-va-bay-nga-man-theravada'],
            ['dung-giua-nhung-dieu-khong-the-doan-truoc-bat-an', 'duyen-khoi-tu-tai-truoc-bat-toan-ai-theravada'],
        ];

        foreach ($pairs as [$lifeSlug, $theravadaSlug]) {
            $lifeArticle = Article::with('pairedArticle')->where('slug', $lifeSlug)->first();
            $theravadaArticle = Article::with('pairedArticle')->where('slug', $theravadaSlug)->first();

            $this->assertNotNull($lifeArticle, "Life article [{$lifeSlug}] must exist.");
            $this->assertNotNull($theravadaArticle, "Theravada article [{$theravadaSlug}] must exist.");

            $this->assertEquals('main', $lifeArticle->site_domain);
            $this->assertEquals('theravada', $theravadaArticle->site_domain);

            $this->assertEquals($theravadaArticle->id, $lifeArticle->paired_article_id);
            $this->assertEquals($lifeArticle->id, $theravadaArticle->paired_article_id);

            $this->assertStringContainsString(':::perspective', $lifeArticle->content);
            $this->assertStringContainsString(':::perspective', $theravadaArticle->content);
        }
    }

    /**
     * Test Life Article endpoint loads with paired_article data.
     */
    public function test_life_article_endpoint_returns_paired_article_prop(): void
    {
        $response = $this->get('/blog/chiec-man-hinh-khong-bao-gio-tat-fomo-doi-song');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Blog/Show')
            ->has('article')
            ->has('paired_article')
            ->where('paired_article.slug', 'ai-thu-con-khat-trien-mien-fomo-vipassana')
            ->where('paired_article.site_domain', 'theravada')
        );
    }

    /**
     * Test Theravada Article endpoint loads with paired_article data.
     */
    public function test_theravada_article_endpoint_returns_paired_article_prop(): void
    {
        $response = $this->get('/theravada/kinh/ai-thu-con-khat-trien-mien-fomo-vipassana');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Theravada/Show')
            ->has('article')
            ->has('paired_article')
            ->where('paired_article.slug', 'chiec-man-hinh-khong-bao-gio-tat-fomo-doi-song')
            ->where('paired_article.site_domain', 'main')
        );
    }
}

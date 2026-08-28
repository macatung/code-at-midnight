<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use Database\Seeders\TheravadaContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TheravadaContentValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Canonical 15 Deep-Dive Expansion Slugs.
     * When present, these articles must strictly satisfy >1000 words.
     */
    protected array $expansionSlugs = [
        'hai-muoi-bon-duyen-he-patthana-catu-visatipaccaya-vi-dieu-phap',
        'sac-phap-chan-de-rupa-paramattha-cau-truc-bon-sac-kalapa',
        'nam-muoi-hai-so-huu-tam-cetasika-quy-luat-phoi-hop-tam-thuc',
        'tien-trinh-can-tu-va-tai-sinh-cuti-patisandhi-vithi-31-coi',
        'duyen-khoi-lien-hoan-paticcasamuppada-12-chi-phan-va-3-luan-chuyen',
        'lich-su-phan-phai-phat-giao-so-khai-theravada-va-mahasanghika',
        'dai-truong-lao-xa-loi-phat-va-muc-kien-lien-hai-vi-thuong-thu-thinh-van',
        'ky-ket-tap-lan-thu-tu-aluvihara-khac-tam-tang-len-la-boi-tich-lan',
        'ky-ket-tap-lan-ba-va-chin-phai-doan-hoang-phap-thoi-vua-asoka',
        'truong-lao-mahinda-va-ni-truong-sanghamitta-khai-sang-phat-giao-tich-lan',
        'toan-thu-40-de-muc-thien-dinh-samatha-kammatthana-visuddhimagga',
        'lo-trinh-16-tang-tue-minh-sat-solasa-nana-va-that-thanh-tinh',
        'phuong-phap-quan-32-the-trong-cua-than-dvattimsakara-kayagatasati',
        'cam-nang-thuc-hanh-gioi-can-ban-va-bat-quan-trai-gioi-uposatha',
        'phuong-phap-quan-tu-dai-catudhatuvavatthana-12-dac-tinh-chan-de',
    ];

    protected array $validCategories = [
        'phap-hoc',
        'phap-hanh',
        'lich-su',
        'kinh-tung',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TheravadaContentSeeder::class);
    }

    /**
     * Helper to compute Vietnamese word count by stripping code blocks, HTML, and markdown links.
     */
    protected function calculateVietnameseWordCount(?string $rawContent): int
    {
        if (empty($rawContent)) {
            return 0;
        }

        // 1. Strip fenced code blocks (Mermaid, code snippets)
        $noCode = preg_replace('/```[\s\S]*?```/u', '', $rawContent);

        // 2. Strip HTML tags
        $noHtml = preg_replace('/<[^>]+>/u', ' ', $noCode);

        // 3. Strip Markdown links [text](url) -> text
        $noMdLinks = preg_replace('/\[([^\]]+)\]\([^)]+\)/u', '$1', $noHtml);

        // 4. Tokenize by whitespace
        $words = preg_split('/\s+/u', trim($noMdLinks), -1, PREG_SPLIT_NO_EMPTY);

        return is_array($words) ? count($words) : 0;
    }

    /**
     * Tier 1: Assert seeder execution populates DB with >= 50 articles (and >= 65 once fully expanded).
     */
    public function test_theravada_seeder_populates_database_successfully(): void
    {
        $count = Article::where('site_domain', 'theravada')->count();

        // Baseline is at least 50 articles; with full expansion it reaches 65
        $this->assertGreaterThanOrEqual(50, $count, "Theravāda article count must be at least 50.");
    }

    /**
     * Tier 1: Schema conformance test for every seeded Theravāda article.
     */
    public function test_all_theravada_articles_conform_to_mandatory_schema(): void
    {
        $articles = Article::where('site_domain', 'theravada')->get();
        $this->assertNotEmpty($articles);

        foreach ($articles as $article) {
            $identifier = $article->slug ?? "ID #{$article->id}";

            // 1. site_domain
            $this->assertEquals('theravada', $article->site_domain, "Article [{$identifier}] must have site_domain 'theravada'.");

            // 2. title
            $this->assertNotEmpty($article->title, "Article [{$identifier}] must have a non-empty title.");
            $this->assertGreaterThanOrEqual(3, mb_strlen($article->title), "Article [{$identifier}] title too short.");

            // 3. pali_title
            $this->assertNotEmpty($article->pali_title, "Article [{$identifier}] must have a non-empty pali_title.");
            $this->assertGreaterThanOrEqual(2, mb_strlen($article->pali_title), "Article [{$identifier}] pali_title too short.");

            // 4. slug
            $this->assertNotEmpty($article->slug, "Article [{$identifier}] must have a slug.");
            $this->assertMatchesRegularExpression('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $article->slug, "Slug [{$article->slug}] is not valid kebab-case.");

            // 5. category
            $this->assertNotEmpty($article->category, "Article [{$identifier}] must have a category.");
            $this->assertContains($article->category, $this->validCategories, "Article [{$identifier}] category [{$article->category}] is invalid.");

            // 6. excerpt
            $this->assertNotEmpty($article->excerpt, "Article [{$identifier}] must have an excerpt.");
            $this->assertGreaterThanOrEqual(10, mb_strlen($article->excerpt), "Article [{$identifier}] excerpt too short.");

            // 7. author
            $this->assertNotEmpty($article->author, "Article [{$identifier}] must have an author.");

            // 8. content
            $this->assertNotEmpty($article->content, "Article [{$identifier}] must have content body.");

            // 9. is_published
            $this->assertTrue((bool)$article->is_published, "Article [{$identifier}] must be published.");
        }
    }

    /**
     * Tier 1: Assert 100% slug uniqueness across all Theravāda articles in database.
     */
    public function test_theravada_slugs_are_strictly_unique(): void
    {
        $slugs = Article::where('site_domain', 'theravada')->pluck('slug')->toArray();
        $uniqueSlugs = array_unique($slugs);

        $this->assertCount(
            count($slugs),
            $uniqueSlugs,
            "Detected duplicate slug(s) in Theravāda articles database!"
        );
    }

    /**
     * Tier 1: Verify canonical categories span all 4 required categories.
     */
    public function test_theravada_articles_span_all_required_categories(): void
    {
        $categories = Article::where('site_domain', 'theravada')->pluck('category')->toArray();
        $uniqueCategories = array_unique($categories);

        foreach ($this->validCategories as $expectedCat) {
            $this->assertContains(
                $expectedCat,
                $uniqueCategories,
                "Theravāda content must include category [{$expectedCat}]."
            );
        }

        $phapHocCount = count(array_filter($categories, fn($c) => $c === 'phap-hoc'));
        $phapHanhCount = count(array_filter($categories, fn($c) => $c === 'phap-hanh'));
        $lichSuCount = count(array_filter($categories, fn($c) => $c === 'lich-su'));
        $kinhTungCount = count(array_filter($categories, fn($c) => $c === 'kinh-tung'));

        $this->assertGreaterThanOrEqual(18, $phapHocCount);
        $this->assertGreaterThanOrEqual(7, $phapHanhCount);
        $this->assertGreaterThanOrEqual(6, $lichSuCount);
        $this->assertGreaterThanOrEqual(9, $kinhTungCount);
    }

    /**
     * Tier 2: Word count validation for deep-dive expansion articles (>1000 words).
     */
    public function test_deep_dive_articles_satisfy_minimum_word_count_requirement(): void
    {
        $articles = Article::where('site_domain', 'theravada')->get();

        foreach ($articles as $article) {
            $wordCount = $this->calculateVietnameseWordCount($article->content);

            // Baseline check for all articles
            $this->assertGreaterThan(
                200,
                $wordCount,
                "Article [{$article->slug}] has insufficient content length ({$wordCount} words)."
            );

            // Strict check for deep-dive expansion articles
            if (in_array($article->slug, $this->expansionSlugs, true)) {
                $this->assertGreaterThan(
                    1000,
                    $wordCount,
                    "Deep-dive article [{$article->slug}] must have > 1,000 words. Actual: {$wordCount} words."
                );
            }
        }
    }

    /**
     * Tier 3: Test Public Inertia Route and View Component for Theravāda article.
     */
    public function test_theravada_article_public_endpoint_renders_successfully(): void
    {
        $firstArticle = Article::where('site_domain', 'theravada')->first();
        $this->assertNotNull($firstArticle, "At least one Theravāda article must exist in DB.");

        $response = $this->get("/theravada/kinh/{$firstArticle->slug}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Theravada/Show')
            ->has('article')
            ->where('article.slug', $firstArticle->slug)
            ->where('article.site_domain', 'theravada')
            ->has('article.title')
            ->has('article.content')
            ->has('article.category')
        );
    }

    /**
     * Tier 3: Test internal cross-linking integrity across all Theravāda articles.
     */
    public function test_internal_cross_links_target_valid_existing_articles(): void
    {
        $allSlugs = Article::where('site_domain', 'theravada')->pluck('slug')->flip()->toArray();
        $articles = Article::where('site_domain', 'theravada')->get();

        $totalInternalLinks = 0;

        foreach ($articles as $article) {
            preg_match_all('/\[([^\]]+)\]\(\/theravada\/kinh\/([a-z0-9-]+)\)/', $article->content, $matches);
            $targetSlugs = $matches[2] ?? [];

            foreach ($targetSlugs as $targetSlug) {
                $totalInternalLinks++;
                $this->assertArrayHasKey(
                    $targetSlug,
                    $allSlugs,
                    "Article [{$article->slug}] contains link to non-existent target slug [/theravada/kinh/{$targetSlug}]."
                );
            }
        }

        $this->assertGreaterThanOrEqual(100, $totalInternalLinks, "Expected robust mesh of internal cross-links (>100 total links).");
    }
}

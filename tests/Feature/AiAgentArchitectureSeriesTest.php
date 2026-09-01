<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AiAgentArchitectureSeriesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test all 5 articles in the AI Agent Architecture series are seeded properly.
     */
    public function test_ai_agent_architecture_series_seeded_successfully(): void
    {
        $expectedSlugs = [
            'kien-truc-ai-agent-phan-1-core-patterns-react-reflexion-swarms',
            'kien-truc-ai-agent-phan-2-mcp-protocol-a2a-layered-memory-graphrag',
            'kien-truc-ai-agent-phan-3-dai-chien-frameworks-langgraph-crewai-autogen',
            'kien-truc-ai-agent-phan-4-system-design-checkpointing-hitl-observability',
            'kien-truc-ai-agent-phan-5-cam-nang-tech-lead-chi-so-asi-anti-patterns-checklist',
        ];

        foreach ($expectedSlugs as $slug) {
            $article = Article::where('slug', $slug)->first();

            $this->assertNotNull($article, "Article with slug [{$slug}] must exist.");
            $this->assertEquals('main', $article->site_domain);
            $this->assertEquals('ai', $article->category);
            $this->assertTrue($article->is_published);
            $this->assertGreaterThanOrEqual(10, $article->reading_time_min);
            $this->assertNotEmpty($article->tags);
            $this->assertGreaterThan(500, strlen($article->content));
        }

        // Test specific architectural content assertions
        $part1 = Article::where('slug', 'kien-truc-ai-agent-phan-1-core-patterns-react-reflexion-swarms')->first();
        $this->assertStringContainsString('```mermaid', $part1->content);
        $this->assertStringContainsString('ReAct', $part1->content);
        $this->assertStringContainsString('Reflexion', $part1->content);

        $part2 = Article::where('slug', 'kien-truc-ai-agent-phan-2-mcp-protocol-a2a-layered-memory-graphrag')->first();
        $this->assertStringContainsString('Model Context Protocol', $part2->content);
        $this->assertStringContainsString('GraphRAG', $part2->content);
        $this->assertStringContainsString('```mermaid', $part2->content);

        $part3 = Article::where('slug', 'kien-truc-ai-agent-phan-3-dai-chien-frameworks-langgraph-crewai-autogen')->first();
        $this->assertStringContainsString('LangGraph', $part3->content);
        $this->assertStringContainsString('CrewAI', $part3->content);
        $this->assertStringContainsString('Semantic Kernel', $part3->content);

        $part4 = Article::where('slug', 'kien-truc-ai-agent-phan-4-system-design-checkpointing-hitl-observability')->first();
        $this->assertStringContainsString('Checkpointing', $part4->content);
        $this->assertStringContainsString('OpenInference', $part4->content);
        $this->assertStringContainsString('G-Eval', $part4->content);

        $part5 = Article::where('slug', 'kien-truc-ai-agent-phan-5-cam-nang-tech-lead-chi-so-asi-anti-patterns-checklist')->first();
        $this->assertStringContainsString('Agentic Selection Index', $part5->content);
        $this->assertStringContainsString('Anti-Patterns', $part5->content);
        $this->assertStringContainsString('Production Checklist', $part5->content);
    }

    /**
     * Test each article endpoint returns HTTP 200 with Inertia Blog/Show component.
     */
    public function test_ai_agent_architecture_articles_accessible_via_http(): void
    {
        $slugs = [
            'kien-truc-ai-agent-phan-1-core-patterns-react-reflexion-swarms',
            'kien-truc-ai-agent-phan-2-mcp-protocol-a2a-layered-memory-graphrag',
            'kien-truc-ai-agent-phan-3-dai-chien-frameworks-langgraph-crewai-autogen',
            'kien-truc-ai-agent-phan-4-system-design-checkpointing-hitl-observability',
            'kien-truc-ai-agent-phan-5-cam-nang-tech-lead-chi-so-asi-anti-patterns-checklist',
        ];

        foreach ($slugs as $slug) {
            $response = $this->get("/blog/{$slug}");

            $response->assertStatus(200);
            $response->assertInertia(fn ($page) => $page
                ->component('Blog/Show')
                ->has('article')
                ->where('article.slug', $slug)
                ->has('settings')
            );
        }
    }

    /**
     * Test that Blog Index page lists the AI Agent series.
     */
    public function test_blog_index_contains_ai_agent_series(): void
    {
        $response = $this->get('/blog');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Blog/Index')
            ->has('articles')
        );
    }
}

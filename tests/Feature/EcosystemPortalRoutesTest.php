<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class EcosystemPortalRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verify root landing page returns 200 and renders the ecosystem Home component.
     */
    public function test_root_hub_returns_200_and_renders_home(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->has('title')
        );
    }

    /**
     * Pillar 1: Theravāda Buddhist digital platform portal route.
     */
    public function test_theravada_portal_route_returns_200(): void
    {
        $response = $this->get('/theravada');
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Theravada/Index')
            ->has('articles')
        );
    }

    /**
     * Pillar 2: Decode media and strategic analysis series portal route.
     */
    public function test_decode_portal_route_returns_200(): void
    {
        $response = $this->get('/decode');
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Decode/Index')
            ->has('episodes')
        );
    }

    /**
     * Pillar 2: Decode Episode 1 canonical route and alias route both return 200.
     */
    public function test_decode_episode_routes_return_200(): void
    {
        // 1. Canonical path route
        $responseCanonical = $this->get('/decode/tap/tap-01-quet-the-visa-100k-2-giay-du-hanh');
        $responseCanonical->assertStatus(200);
        $responseCanonical->assertInertia(fn (Assert $page) => $page
            ->component('Decode/Show')
            ->has('episode')
        );

        // 2. Direct path alias route
        $responseAlias = $this->get('/decode/tap-01-quet-the-visa-100k-2-giay-du-hanh');
        $responseAlias->assertStatus(200);
        $responseAlias->assertInertia(fn (Assert $page) => $page
            ->component('Decode/Show')
            ->has('episode')
        );

        // 3. Subdomain alias route
        $responseSubdomainAlias = $this->get('http://decode.macatung.dev/tap-01-quet-the-visa-100k-2-giay-du-hanh');
        $responseSubdomainAlias->assertStatus(200);
        $responseSubdomainAlias->assertInertia(fn (Assert $page) => $page
            ->component('Decode/Show')
            ->has('episode')
        );
    }

    /**
     * Pillar 3: Shopee Cashback affiliate & tracking utility portal route.
     */
    public function test_cashback_portal_route_returns_200(): void
    {
        $response = $this->get('/hoantien');
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Cashback/Index')
            ->has('wallet')
        );
    }

    /**
     * Pillar 4: Task Companion desktop application product portal route.
     */
    public function test_desktop_portal_route_returns_200(): void
    {
        $response = $this->get('/desktop');
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Desktop/Index')
        );
    }

    /**
     * Pillar 5: Interactive digital tools and mini-apps (Rune Typer & Talisman Forge).
     */
    public function test_interactive_tools_portal_routes_return_200(): void
    {
        $responseGame = $this->get('/game');
        $responseGame->assertStatus(200);
        $responseGame->assertInertia(fn (Assert $page) => $page
            ->component('Game/Index')
        );

        $responseTalisman = $this->get('/talisman');
        $responseTalisman->assertStatus(200);
        $responseTalisman->assertInertia(fn (Assert $page) => $page
            ->component('Talisman/Index')
        );
    }

    /**
     * Verify all 3 primary ecosystem subdomains return HTTP 200 and render appropriate components.
     */
    public function test_ecosystem_subdomains_return_200(): void
    {
        $responseTheravada = $this->get('http://theravada.macatung.dev');
        $responseTheravada->assertStatus(200);
        $responseTheravada->assertInertia(fn (Assert $page) => $page
            ->component('Theravada/Index')
        );

        $responseDecode = $this->get('http://decode.macatung.dev');
        $responseDecode->assertStatus(200);
        $responseDecode->assertInertia(fn (Assert $page) => $page
            ->component('Decode/Index')
        );

        $responseCashback = $this->get('http://hoantien.macatung.dev');
        $responseCashback->assertStatus(200);
        $responseCashback->assertInertia(fn (Assert $page) => $page
            ->component('Cashback/Index')
        );
    }

    /**
     * Verify that public portal routes do not contain personal resume or CV keywords.
     */
    public function test_public_portal_routes_do_not_contain_personal_resume_keywords(): void
    {
        $routes = [
            '/',
            '/theravada',
            '/decode',
            '/hoantien',
            '/desktop',
            '/game',
            '/talisman',
            'http://theravada.macatung.dev',
            'http://decode.macatung.dev',
            'http://hoantien.macatung.dev',
        ];

        $disallowedKeywords = [
            'Curriculum Vitae',
            'proficiency-bar',
            'Senior Fullstack & AI Agent Architect',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
            $content = $response->getContent();

            foreach ($disallowedKeywords as $keyword) {
                $this->assertStringNotContainsString(
                    $keyword,
                    $content,
                    "Route [{$route}] unexpectedly contained personal resume keyword: [{$keyword}]"
                );
            }
        }
    }

    /**
     * Verify global and subdomain sitemaps index ecosystem URLs.
     */
    public function test_sitemap_verification_for_ecosystem_urls(): void
    {
        // 1. Global sitemap
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $this->assertMatchesRegularExpression('/(application|text)\/xml/i', (string) $response->headers->get('Content-Type'));
        $content = $response->getContent();

        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
        $this->assertStringContainsString('<urlset', $content);

        // Core interactive tools ecosystem routes in sitemap
        $this->assertStringContainsString('/game', $content);
        $this->assertStringContainsString('/talisman', $content);

        // If expanded pillar routes are present (after Milestone 3 sitemap update), verify them
        if (str_contains($content, '/desktop') || str_contains($content, '/theravada')) {
            $this->assertStringContainsString('/desktop', $content);
            $this->assertStringContainsString('/theravada', $content);
            $this->assertStringContainsString('/decode', $content);
            $this->assertStringContainsString('/hoantien', $content);
        }

        // 2. Subdomain sitemaps for Theravāda and Decode portals
        $theravadaSitemap = $this->get('http://theravada.macatung.dev/sitemap.xml');
        $theravadaSitemap->assertStatus(200);
        $this->assertStringContainsString('<?xml', $theravadaSitemap->getContent());
        $this->assertStringContainsString('<urlset', $theravadaSitemap->getContent());

        $decodeSitemap = $this->get('http://decode.macatung.dev/sitemap.xml');
        $decodeSitemap->assertStatus(200);
        $this->assertStringContainsString('<?xml', $decodeSitemap->getContent());
        $this->assertStringContainsString('<urlset', $decodeSitemap->getContent());
    }
}

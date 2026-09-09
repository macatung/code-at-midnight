<?php

namespace Tests\Feature;

use App\Models\CashbackWallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CashbackRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test fallback path /hoantien returns HTTP 200 and renders Cashback/Index.
     */
    public function test_cashback_path_fallback_returns_200_and_renders_inertia_page(): void
    {
        $response = $this->get('/hoantien');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Cashback/Index')
            ->has('wallet')
            ->has('wallet.sub_id')
            ->has('wallet.pending_balance')
            ->has('wallet.available_balance')
            ->has('wallet.withdrawn_balance')
            ->has('clicks')
            ->has('orders')
            ->has('withdrawals')
            ->has('stats')
        );
    }

    /**
     * Test localhost subdomain hoantien.localhost returns HTTP 200.
     */
    public function test_cashback_subdomain_localhost_returns_200(): void
    {
        $response = $this->get('http://hoantien.localhost:8000');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Cashback/Index')
            ->has('wallet')
            ->has('wallet.sub_id')
        );
    }

    /**
     * Test production subdomain hoantien.macatung.dev returns HTTP 200.
     */
    public function test_cashback_subdomain_production_domain_returns_200(): void
    {
        $response = $this->get('http://hoantien.macatung.dev');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Cashback/Index')
            ->has('wallet')
        );
    }

    /**
     * Test session persistence of wallet sub_id.
     */
    public function test_sub_id_is_stored_in_session_for_returning_visitors(): void
    {
        $response1 = $this->get('/hoantien');
        $response1->assertStatus(200);

        $subId = session('cashback_sub_id');
        $this->assertNotEmpty($subId);
        $this->assertStringStartsWith('mt_s_', $subId);

        // Second visit retains same wallet sub_id
        $response2 = $this->get('/hoantien');
        $response2->assertStatus(200);
        $this->assertEquals($subId, session('cashback_sub_id'));
    }

    /**
     * Adversarial Test: Named route cashback.domain.index must resolve to baseDomain (e.g. hoantien.macatung.dev),
     * NOT be overwritten by local development domain.
     */
    public function test_named_route_resolves_to_production_base_domain(): void
    {
        $url = route('cashback.domain.index');
        $expectedHost = 'hoantien.' . config('app.base_domain', 'macatung.dev');
        $this->assertStringContainsString($expectedHost, $url);
    }

    /**
     * Adversarial Test: Subdomain access with /hoantien prefix also returns 200 smoothly.
     */
    public function test_subdomain_with_hoantien_prefix_path_returns_200(): void
    {
        $response = $this->get('http://hoantien.localhost:8000/hoantien');
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page->component('Cashback/Index'));
    }

    /**
     * Adversarial Test: HTML response contains CSRF token meta tag and Cashback Portal branding metadata.
     */
    public function test_subdomain_and_fallback_routes_include_csrf_token_meta_tag_and_cashback_metadata(): void
    {
        $response = $this->get('/hoantien');
        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertStringContainsString('<meta name="csrf-token"', $content);
        $this->assertStringContainsString('Cổng Hoàn Tiền Shopee', $content);
    }

    /**
     * Adversarial Test: Subdomain /hoantien prefix routes for webhook and sync return 200 without 404.
     */
    public function test_subdomain_prefix_routes_for_webhook_and_sync_return_200(): void
    {
        $payload = ['orders' => []];

        $resWebhook = $this->postJson('http://hoantien.localhost:8000/hoantien/webhook', $payload);
        $resWebhook->assertStatus(200);
        $this->assertTrue($resWebhook->json('success'));

        $resSync = $this->postJson('http://hoantien.localhost:8000/hoantien/sync', []);
        $resSync->assertStatus(200);
        $this->assertTrue($resSync->json('success'));
    }
}


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
}

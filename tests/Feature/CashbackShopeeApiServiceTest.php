<?php

namespace Tests\Feature;

use App\Models\CashbackOrder;
use App\Models\CashbackWallet;
use App\Services\CashbackOrderSyncService;
use App\Services\ShopeeAffiliateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashbackShopeeApiServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test SHA256 signature conforms to Shopee Open Platform specification.
     */
    public function test_shopee_signature_algorithm_produces_exact_sha256_hash(): void
    {
        $appId = '1089234';
        $secret = 'shopee_secret_key_xyz_9988';
        $timestamp = 1725883200;
        $payload = json_encode(['test' => 123, 'action' => 'query']);

        $expectedHash = hash('sha256', $appId . $timestamp . $payload . $secret);
        $computedHash = ShopeeAffiliateService::generateSignature($appId, $secret, $timestamp, $payload);

        $this->assertEquals($expectedHash, $computedHash);
        $this->assertTrue(ShopeeAffiliateService::verifySignature($appId, $secret, $timestamp, $payload, $expectedHash));
        $this->assertFalse(ShopeeAffiliateService::verifySignature($appId, $secret, $timestamp, $payload, 'tampered_signature'));
    }

    /**
     * Test Authorization Header adheres to Shopee Open Platform standard format.
     */
    public function test_authorization_header_format(): void
    {
        $appId = 'test_app_id';
        $secret = 'test_secret_key';
        $timestamp = 1725900000;
        $payload = '{"query":"mutation"}';

        $header = ShopeeAffiliateService::generateAuthHeader($appId, $secret, $timestamp, $payload);
        $signature = ShopeeAffiliateService::generateSignature($appId, $secret, $timestamp, $payload);

        $this->assertEquals("SHA256 Credential={$appId}, Timestamp={$timestamp}, Signature={$signature}", $header);
        $this->assertStringStartsWith('SHA256 Credential=', $header);
        $this->assertStringContainsString("Timestamp={$timestamp}", $header);
        $this->assertStringContainsString("Signature={$signature}", $header);
    }

    /**
     * Test Shopee service returns mock tracking link when credentials are not configured.
     */
    public function test_shopee_service_generates_mock_link_without_production_credentials(): void
    {
        config([
            'cashback.shopee.app_id' => '',
            'cashback.shopee.secret' => '',
            'cashback.shopee.mock_enabled' => true,
        ]);

        $service = app(ShopeeAffiliateService::class);
        $shortLink = $service->generateShortLink('https://shopee.vn/product/112233', 'mt_test_user');

        $this->assertNotEmpty($shortLink);
        $this->assertStringContainsString('sub_id=mt_test_user', $shortLink);
        $this->assertStringStartsWith('https://s.shopee.vn/', $shortLink);
    }

    /**
     * Test processing conversion report nodes extracts sub_id, matches wallet and computes cashback.
     */
    public function test_order_sync_extracts_sub_id_and_calculates_cashback_rate(): void
    {
        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_test_order_sub',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        config(['cashback.rate' => 0.80]); // 80% share

        $nodes = [
            [
                'orderId' => '240909SHP888999',
                'purchaseTime' => time() - 100,
                'subIds' => ['mt_test_order_sub'],
                'orderStatus' => 'PENDING',
                'totalCommission' => 50000,
                'items' => [
                    [
                        'itemId' => '12345',
                        'itemName' => 'Tai Nghe Bluetooth TWS Shopee Mall',
                        'itemPrice' => 500000,
                        'itemCommission' => 50000,
                        'itemStatus' => 'PENDING',
                    ],
                ],
            ],
        ];

        $syncService = app(CashbackOrderSyncService::class);
        $processed = $syncService->processReportNodes($nodes);

        $this->assertCount(1, $processed);
        $order = $processed[0];

        $this->assertEquals('240909SHP888999', $order->shopee_order_id);
        $this->assertEquals('mt_test_order_sub', $order->sub_id);
        $this->assertEquals(500000, $order->gmv);
        $this->assertEquals(50000, $order->commission_shopee);
        $this->assertEquals(0.80, $order->cashback_rate);
        // 50,000 * 0.80 = 40,000
        $this->assertEquals(40000, $order->cashback_amount);
        $this->assertEquals('pending', $order->status);

        // Verify wallet pending balance was credited
        $wallet->refresh();
        $this->assertEquals(40000, $wallet->pending_balance);
        $this->assertEquals(0, $wallet->available_balance);
    }

    /**
     * Test webhook endpoint accepts payload and processes orders.
     */
    public function test_webhook_endpoint_processes_shopee_orders(): void
    {
        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_webhook_user',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        $payload = [
            'orders' => [
                [
                    'orderId' => 'WH_ORDER_7788',
                    'sub_id' => 'mt_webhook_user',
                    'orderStatus' => 'COMPLETED',
                    'totalCommission' => 20000,
                    'gmv' => 300000,
                    'product_name' => 'Ốp lưng iPhone 15 Pro Max',
                ],
            ],
        ];

        $response = $this->postJson('/hoantien/webhook', $payload);
        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertEquals(1, $response->json('processed_count'));

        $wallet->refresh();
        // 20,000 * 0.80 = 16,000 confirmed
        $this->assertEquals(16000.00, $wallet->available_balance);
    }

    /**
     * Test artisan command cashback:sync-orders runs cleanly.
     */
    public function test_artisan_sync_orders_command(): void
    {
        CashbackWallet::create([
            'sub_id' => 'mt_demo',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        $this->artisan('cashback:sync-orders')
            ->expectsOutput('Starting Shopee Cashback order synchronization...')
            ->assertExitCode(0);
    }

    /**
     * Adversarial Test: Shopee conversion report with 13-digit millisecond timestamp and subId/sub_ids variations.
     */
    public function test_order_sync_handles_millisecond_timestamp_and_sub_id_variations(): void
    {
        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_sub_variations',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        $nodes = [
            // Node 1: 13-digit millisecond purchaseTime and subId key
            [
                'order_id' => '240909SHP_MS_1',
                'purchaseTime' => 1725883200000, // milliseconds: 2024-09-09 12:00:00 UTC
                'subId' => 'mt_sub_variations',
                'orderStatus' => 'COMPLETED',
                'totalCommission' => 30000,
                'gmv' => 400000,
                'product_name' => 'Sản phẩm Shopee Test Milliseconds',
            ],
            // Node 2: purchase_time snake_case and subId1 key
            [
                'orderId' => '240909SHP_MS_2',
                'purchase_time' => 1725883200,
                'subId1' => 'mt_sub_variations',
                'orderStatus' => 'COMPLETED',
                'totalCommission' => 20000,
                'gmv' => 250000,
            ],
        ];

        $syncService = app(CashbackOrderSyncService::class);
        $processed = $syncService->processReportNodes($nodes);

        $this->assertCount(2, $processed);
        $order1 = $processed[0];
        // Year must be 2024 or 2026, never > 2100!
        $this->assertLessThan(2100, (int) date('Y', strtotime($order1->order_time)));
        $this->assertEquals('240909SHP_MS_1', $order1->shopee_order_id);

        $order2 = $processed[1];
        $this->assertEquals('240909SHP_MS_2', $order2->shopee_order_id);
    }

    /**
     * Adversarial Test: Webhook POST request passes through HTTP kernel without CSRF token (no 419).
     */
    public function test_webhook_and_sync_endpoints_exempt_from_csrf_verification(): void
    {
        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_csrf_exempt_user',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        $payload = json_encode([
            'orders' => [
                [
                    'orderId' => 'CSRF_EXEMPT_001',
                    'sub_id' => 'mt_csrf_exempt_user',
                    'orderStatus' => 'COMPLETED',
                    'totalCommission' => 10000,
                    'gmv' => 100000,
                ],
            ],
        ]);

        // 1. Path route /hoantien/webhook
        $req1 = \Illuminate\Http\Request::create('/hoantien/webhook', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], $payload);
        $res1 = app()->handle($req1);
        $this->assertEquals(200, $res1->getStatusCode());

        // 2. Subdomain route hoantien.macatung.dev/webhook
        $req2 = \Illuminate\Http\Request::create('http://hoantien.macatung.dev/webhook', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], $payload);
        $res2 = app()->handle($req2);
        $this->assertEquals(200, $res2->getStatusCode());
    }

    /**
     * Adversarial Test: Webhook enforces Shopee signature authentication when credentials configured.
     */
    public function test_webhook_verifies_shopee_signature_when_live_credentials_are_configured(): void
    {
        config([
            'cashback.shopee.app_id' => 'shopee_live_app',
            'cashback.shopee.secret' => 'super_secret_shopee_key_123',
            'cashback.shopee.mock_enabled' => false,
        ]);

        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_sig_user',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        $body = json_encode([
            'orders' => [
                [
                    'orderId' => 'AUTH_ORDER_01',
                    'sub_id' => 'mt_sig_user',
                    'orderStatus' => 'COMPLETED',
                    'totalCommission' => 10000,
                ],
            ],
        ]);

        $timestamp = time();
        $validSignature = ShopeeAffiliateService::generateSignature('shopee_live_app', 'super_secret_shopee_key_123', $timestamp, $body);

        // 1. Tampered signature -> 401
        $tamperedReq = \Illuminate\Http\Request::create('/hoantien/webhook', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => "SHA256 Credential=shopee_live_app, Timestamp={$timestamp}, Signature=tampered_sig_hash",
        ], $body);
        $tamperedRes = app()->handle($tamperedReq);
        $this->assertEquals(401, $tamperedRes->getStatusCode());

        // 2. Valid signature -> 200
        $validReq = \Illuminate\Http\Request::create('/hoantien/webhook', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => "SHA256 Credential=shopee_live_app, Timestamp={$timestamp}, Signature={$validSignature}",
        ], $body);
        $validRes = app()->handle($validReq);
        $this->assertEquals(200, $validRes->getStatusCode());
    }

    /**
     * Adversarial Test: Order sync extracts product image and multi-item count summary.
     */
    public function test_order_sync_extracts_product_image_and_multi_item_summary(): void
    {
        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_multi_item_user',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        $nodes = [
            [
                'orderId' => 'MULTI_ITEM_ORDER_01',
                'sub_id' => 'mt_multi_item_user',
                'orderStatus' => 'COMPLETED',
                'items' => [
                    [
                        'itemName' => 'Bàn phím không dây cơ',
                        'itemPrice' => 500000,
                        'itemCommission' => 30000,
                        'imageUrl' => 'https://cf.shopee.vn/file/keyboard.jpg',
                    ],
                    [
                        'itemName' => 'Chuột Gaming RGB',
                        'itemPrice' => 250000,
                        'itemCommission' => 15000,
                        'imageUrl' => 'https://cf.shopee.vn/file/mouse.jpg',
                    ],
                ],
            ],
        ];

        $syncService = app(CashbackOrderSyncService::class);
        $processed = $syncService->processReportNodes($nodes);

        $this->assertCount(1, $processed);
        $order = $processed[0];
        $this->assertEquals('https://cf.shopee.vn/file/keyboard.jpg', $order->product_image);
        $this->assertStringContainsString('Bàn phím không dây cơ', $order->product_name);
        $this->assertStringContainsString('+1 sp khác', $order->product_name);
        $this->assertEquals(750000, $order->gmv);
        $this->assertEquals(45000, $order->commission_shopee);
    }

    /**
     * Adversarial Test: Subsequent settlement report omitting sub_id still updates the order correctly.
     */
    public function test_order_sync_updates_status_even_when_sub_id_is_omitted_in_subsequent_report(): void
    {
        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_no_sub_callback',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        $syncService = app(CashbackOrderSyncService::class);

        // 1. Initial report with sub_id
        $syncService->processReportNodes([
            [
                'orderId' => 'NO_SUB_CALLBACK_01',
                'sub_id' => 'mt_no_sub_callback',
                'orderStatus' => 'PENDING',
                'totalCommission' => 50000,
                'gmv' => 600000,
                'product_name' => 'Màn hình 27 inch 4K',
            ],
        ]);

        $wallet->refresh();
        $this->assertEquals(40000.00, $wallet->pending_balance);
        $this->assertEquals(0.00, $wallet->available_balance);

        // 2. Shopee callback confirms order but omits sub_id
        $syncService->processReportNodes([
            [
                'orderId' => 'NO_SUB_CALLBACK_01',
                // sub_id is intentionally omitted
                'orderStatus' => 'COMPLETED',
                'totalCommission' => 50000,
                'gmv' => 600000,
            ],
        ]);

        $wallet->refresh();
        $this->assertEquals(0.00, $wallet->pending_balance);
        $this->assertEquals(40000.00, $wallet->available_balance);

        $order = CashbackOrder::where('shopee_order_id', 'NO_SUB_CALLBACK_01')->first();
        $this->assertEquals('confirmed', $order->status);
    }
}


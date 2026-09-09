<?php

namespace Tests\Feature;

use App\Models\CashbackClick;
use App\Models\CashbackWallet;
use App\Services\ShopeeAffiliateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashbackTrackingAndLinkTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test URL validation helper recognizes valid Shopee URLs.
     */
    public function test_url_validator_recognizes_valid_shopee_urls(): void
    {
        $validUrls = [
            'https://shopee.vn/product/12345/67890',
            'http://shopee.vn/Ao-Thun-Cotton-i.123.456',
            'https://s.shopee.vn/8fG1kLmNoP',
            'https://shope.ee/5AQw7xyz',
            'https://vn.shp.ee/m9pQ1z',
            'shopee.vn/flash_sale',
        ];

        foreach ($validUrls as $url) {
            $this->assertTrue(ShopeeAffiliateService::isValidShopeeUrl($url), "Failed validating valid URL: {$url}");
        }
    }

    /**
     * Test URL validator rejects non-Shopee URLs.
     */
    public function test_url_validator_rejects_non_shopee_urls(): void
    {
        $invalidUrls = [
            'https://lazada.vn/products/ao-thun-123.html',
            'https://tiki.vn/dien-thoai-iphone-15',
            'https://google.com',
            'https://fake-shopee.com/product/123',
            'not-a-url',
            '',
        ];

        foreach ($invalidUrls as $url) {
            $this->assertFalse(ShopeeAffiliateService::isValidShopeeUrl($url), "Should reject invalid URL: {$url}");
        }
    }

    /**
     * Test generating affiliate link with valid Shopee URL returns 200 and records click.
     */
    public function test_generate_link_creates_click_record_and_returns_short_link(): void
    {
        $originUrl = 'https://shopee.vn/Ao-Thun-Nam-Cotton-i.123.45678';

        $response = $this->postJson('/hoantien/generate-link', [
            'url' => $originUrl,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'short_link',
            'sub_id',
            'original_url',
            'click_id',
            'message',
        ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertNotEmpty($responseData['short_link']);
        $this->assertNotEmpty($responseData['sub_id']);
        $this->assertStringContainsString($responseData['sub_id'], $responseData['short_link']);

        // Check database insertion
        $this->assertDatabaseHas('cashback_clicks', [
            'original_url' => $originUrl,
            'sub_id' => $responseData['sub_id'],
        ]);

        $click = CashbackClick::find($responseData['click_id']);
        $this->assertNotNull($click);
        $this->assertEquals($originUrl, $click->original_url);
    }

    /**
     * Test generating link with non-Shopee URL returns 422 Unprocessable Entity.
     */
    public function test_generate_link_rejects_invalid_url_with_422(): void
    {
        $response = $this->postJson('/hoantien/generate-link', [
            'url' => 'https://lazada.vn/item-xyz',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('không hợp lệ', $response->json('message'));
    }

    /**
     * Test generating link works on subdomain route as well.
     */
    public function test_generate_link_works_on_subdomain(): void
    {
        $originUrl = 'https://s.shopee.vn/7Km9pQrSt';

        $response = $this->postJson('http://hoantien.localhost:8000/generate-link', [
            'url' => $originUrl,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
    }

    /**
     * Adversarial Test: Pasting Shopee app share message with surrounding promo text extracts clean URL.
     */
    public function test_generate_link_extracts_shopee_url_from_app_share_text(): void
    {
        $rawShareText = 'Mua Bàn Phím Cơ Không Dây trên Shopee ngay! https://s.shopee.vn/7f8a9b (Áp mã giảm 20k)';

        $response = $this->postJson('/hoantien/generate-link', [
            'url' => $rawShareText,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertEquals('https://s.shopee.vn/7f8a9b', $response->json('original_url'));
    }

    /**
     * Adversarial Test: extractShopeeUrl handles multiple variations and rejects pure invalid text.
     */
    public function test_extract_shopee_url_helper_variations(): void
    {
        $this->assertEquals('https://s.shopee.vn/xyz123', ShopeeAffiliateService::extractShopeeUrl('Check this out https://s.shopee.vn/xyz123!'));
        $this->assertEquals('https://shopee.vn/product/12/34', ShopeeAffiliateService::extractShopeeUrl('Mua ngay https://shopee.vn/product/12/34.'));
        $this->assertEquals('https://shope.ee/short1', ShopeeAffiliateService::extractShopeeUrl('https://shope.ee/short1'));
        $this->assertNull(ShopeeAffiliateService::extractShopeeUrl('Không có link nào ở đây'));
        $this->assertNull(ShopeeAffiliateService::extractShopeeUrl('https://tiki.vn/san-pham-123'));
    }
}

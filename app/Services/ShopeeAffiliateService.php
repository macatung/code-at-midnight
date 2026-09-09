<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShopeeAffiliateService
{
    protected string $appId;
    protected string $secret;
    protected string $endpoint;
    protected bool $mockEnabled;

    public function __construct()
    {
        $this->appId = (string) config('cashback.shopee.app_id', '');
        $this->secret = (string) config('cashback.shopee.secret', '');
        $this->endpoint = (string) config('cashback.shopee.endpoint', 'https://open-api.affiliate.shopee.vn/graphql');
        $this->mockEnabled = (bool) config('cashback.shopee.mock_enabled', true) || empty($this->appId) || empty($this->secret);
    }

    /**
     * Compute SHA256 signature according to Shopee Open Platform specification:
     * SHA256(appId + timestamp + payload + secret)
     */
    public static function generateSignature(string $appId, string $secret, int|string $timestamp, string $payload): string
    {
        return hash('sha256', $appId . $timestamp . $payload . $secret);
    }

    /**
     * Verify whether an incoming signature matches expected SHA256 computation.
     */
    public static function verifySignature(string $appId, string $secret, int|string $timestamp, string $payload, string $signature): bool
    {
        return hash_equals(self::generateSignature($appId, $secret, $timestamp, $payload), $signature);
    }

    /**
     * Generate the Authorization header value:
     * Authorization: SHA256 Credential={appId}, Timestamp={timestamp}, Signature={signature}
     */
    public static function generateAuthHeader(string $appId, string $secret, int|string $timestamp, string $payload): string
    {
        $signature = self::generateSignature($appId, $secret, $timestamp, $payload);
        return sprintf('SHA256 Credential=%s, Timestamp=%s, Signature=%s', $appId, $timestamp, $signature);
    }

    /**
     * Validate whether a given URL belongs to Shopee.
     */
    public static function isValidShopeeUrl(string $url): bool
    {
        $clean = trim($url);
        if (empty($clean)) {
            return false;
        }

        // Support formats: https://shopee.vn/..., https://s.shopee.vn/..., https://shope.ee/..., vn.shp.ee/...
        $pattern = '/^(https?:\/\/)?([a-zA-Z0-9_-]+\.)?(shopee\.vn|s\.shopee\.vn|shope\.ee|vn\.shp\.ee)(\/.*)?$/i';
        return (bool) preg_match($pattern, $clean);
    }

    /**
     * Generate affiliate tracking short link with custom sub_id.
     */
    public function generateShortLink(string $originUrl, string $subId): string
    {
        if ($this->mockEnabled) {
            return $this->generateMockShortLink($originUrl, $subId);
        }

        try {
            $timestamp = time();
            $query = <<<'GRAPHQL'
mutation generateShortLink($input: GenerateShortLinkInput!) {
    generateShortLink(input: $input) {
        shortLink
    }
}
GRAPHQL;

            $payloadArray = [
                'query' => $query,
                'variables' => [
                    'input' => [
                        'originUrl' => $originUrl,
                        'subIds' => [$subId],
                    ],
                ],
            ];
            $payloadJson = json_encode($payloadArray, JSON_UNESCAPED_SLASHES);
            $authHeader = self::generateAuthHeader($this->appId, $this->secret, $timestamp, $payloadJson);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => $authHeader,
            ])->timeout(5)->post($this->endpoint, $payloadArray);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['data']['generateShortLink']['shortLink'])) {
                    return $data['data']['generateShortLink']['shortLink'];
                }
            }

            Log::warning('Shopee API generateShortLink returned unexpected response, fallback to mock', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Shopee API call exception', ['error' => $e->getMessage()]);
        }

        return $this->generateMockShortLink($originUrl, $subId);
    }

    /**
     * Fallback mock affiliate short link generator.
     */
    public function generateMockShortLink(string $originUrl, string $subId): string
    {
        $hash = substr(md5($originUrl . $subId), 0, 8);
        return sprintf('https://s.shopee.vn/aff_%s?sub_id=%s', $hash, urlencode($subId));
    }

    /**
     * Query Conversion Report from Shopee Open Platform Affiliate API.
     */
    public function getConversionReport(?int $startTime = null, ?int $endTime = null, int $page = 1, int $limit = 50): array
    {
        $startTime = $startTime ?? (time() - 86400 * 7);
        $endTime = $endTime ?? time();

        if ($this->mockEnabled) {
            return $this->getMockConversionReport($startTime, $endTime);
        }

        try {
            $timestamp = time();
            $query = <<<'GRAPHQL'
query conversionReport($purchaseTimeStart: Int, $purchaseTimeEnd: Int, $page: Int, $limit: Int) {
    conversionReport(purchaseTimeStart: $purchaseTimeStart, purchaseTimeEnd: $purchaseTimeEnd, page: $page, limit: $limit) {
        nodes {
            orderId
            purchaseTime
            subIds
            orderStatus
            totalCommission
            items {
                itemId
                itemName
                itemPrice
                itemCommission
                itemStatus
            }
        }
        pageInfo {
            hasNextPage
        }
    }
}
GRAPHQL;

            $payloadArray = [
                'query' => $query,
                'variables' => [
                    'purchaseTimeStart' => $startTime,
                    'purchaseTimeEnd' => $endTime,
                    'page' => $page,
                    'limit' => $limit,
                ],
            ];
            $payloadJson = json_encode($payloadArray, JSON_UNESCAPED_SLASHES);
            $authHeader = self::generateAuthHeader($this->appId, $this->secret, $timestamp, $payloadJson);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => $authHeader,
            ])->timeout(10)->post($this->endpoint, $payloadArray);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data']['conversionReport']['nodes'] ?? [];
            }
        } catch (\Throwable $e) {
            Log::error('Shopee API conversion report exception', ['error' => $e->getMessage()]);
        }

        return $this->getMockConversionReport($startTime, $endTime);
    }

    /**
     * Generate mock conversion report for testing and local development.
     */
    public function getMockConversionReport(int $startTime, int $endTime): array
    {
        return [
            [
                'orderId' => '240909SHP' . rand(100000, 999999),
                'purchaseTime' => time() - 3600,
                'subIds' => ['mt_demo'],
                'orderStatus' => 'COMPLETED',
                'totalCommission' => 45000,
                'items' => [
                    [
                        'itemId' => '987654321',
                        'itemName' => 'Bàn Phím Cơ Không Dây Macatung Edition',
                        'itemPrice' => 850000,
                        'itemCommission' => 45000,
                        'itemStatus' => 'COMPLETED',
                    ],
                ],
            ],
        ];
    }
}

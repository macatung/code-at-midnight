<?php

namespace App\Services;

use App\Models\CashbackClick;
use App\Models\CashbackOrder;
use App\Models\CashbackWallet;
use Illuminate\Support\Facades\Log;

class CashbackOrderSyncService
{
    protected ShopeeAffiliateService $shopeeService;
    protected CashbackWalletService $walletService;

    public function __construct(ShopeeAffiliateService $shopeeService, CashbackWalletService $walletService)
    {
        $this->shopeeService = $shopeeService;
        $this->walletService = $walletService;
    }

    /**
     * Sync orders from Shopee Open Platform conversion report API.
     */
    public function syncFromShopee(?int $startTime = null, ?int $endTime = null): array
    {
        $nodes = $this->shopeeService->getConversionReport($startTime, $endTime);
        return $this->processReportNodes($nodes);
    }

    /**
     * Alias for syncFromShopee.
     */
    public function syncOrders(?int $startTime = null, ?int $endTime = null): array
    {
        return $this->syncFromShopee($startTime, $endTime);
    }

    /**
     * Extract and normalize order nodes from diverse API/Webhook payload formats.
     */
    public static function extractOrderNodes(mixed $payload): array
    {
        if (!is_array($payload) || empty($payload)) {
            return [];
        }

        // 1. GraphQL structure: payload.data.conversionReport.nodes
        if (isset($payload['data']['conversionReport']['nodes']) && is_array($payload['data']['conversionReport']['nodes'])) {
            return $payload['data']['conversionReport']['nodes'];
        }

        // 2. GraphQL / API nodes: payload.data.nodes or payload.nodes
        if (isset($payload['data']['nodes']) && is_array($payload['data']['nodes'])) {
            return $payload['data']['nodes'];
        }
        if (isset($payload['nodes']) && is_array($payload['nodes'])) {
            return $payload['nodes'];
        }

        // 3. payload.data could be a single order object or an array of orders
        if (isset($payload['data']) && is_array($payload['data'])) {
            if (isset($payload['data']['orderId']) || isset($payload['data']['order_id'])) {
                return [$payload['data']];
            }
            if (array_is_list($payload['data']) || isset($payload['data'][0])) {
                return $payload['data'];
            }
        }

        // 4. payload.orders could be an array of orders or a single order object
        if (isset($payload['orders']) && is_array($payload['orders'])) {
            if (isset($payload['orders']['orderId']) || isset($payload['orders']['order_id'])) {
                return [$payload['orders']];
            }
            if (array_is_list($payload['orders']) || isset($payload['orders'][0])) {
                return $payload['orders'];
            }
        }

        // 5. Direct single order object: payload.orderId or payload.order_id
        if (isset($payload['orderId']) || isset($payload['order_id'])) {
            return [$payload];
        }

        // 6. Plain list of order objects: [ {orderId: ...}, {orderId: ...} ]
        if (array_is_list($payload)) {
            return $payload;
        }

        return [$payload];
    }

    /**
     * Process list of order nodes from API or Webhook payload.
     */
    public function processReportNodes(array $nodes): array
    {
        $nodes = self::extractOrderNodes($nodes);
        $processed = [];

        foreach ($nodes as $node) {
            if (!is_array($node)) {
                continue;
            }

            $orderId = (string) ($node['orderId'] ?? $node['order_id'] ?? '');
            if (!$orderId) {
                continue;
            }

            // Extract sub_id from all Shopee API formats
            $subId = null;
            if (!empty($node['subIds']) && is_array($node['subIds'])) {
                $subId = $node['subIds'][0] ?? null;
            } elseif (!empty($node['sub_ids']) && is_array($node['sub_ids'])) {
                $subId = $node['sub_ids'][0] ?? null;
            } elseif (!empty($node['sub_id'])) {
                $subId = (string) $node['sub_id'];
            } elseif (!empty($node['subId'])) {
                $subId = (string) $node['subId'];
            } elseif (!empty($node['subId1'])) {
                $subId = (string) $node['subId1'];
            } elseif (!empty($node['sub_id1'])) {
                $subId = (string) $node['sub_id1'];
            }

            // If sub_id omitted in subsequent status callbacks, match existing order sub_id
            if (!$subId) {
                $subId = CashbackOrder::where('shopee_order_id', $orderId)->value('sub_id');
            }

            if (!$subId) {
                continue;
            }

            // Extract product name, image, gmv, commission
            $productName = $node['product_name'] ?? null;
            $productImage = $node['product_image'] ?? $node['imageUrl'] ?? $node['image'] ?? null;
            $gmv = (float) ($node['gmv'] ?? 0);
            $commission = (float) ($node['totalCommission'] ?? $node['total_commission'] ?? $node['commission'] ?? 0);

            if (!empty($node['items']) && is_array($node['items'])) {
                $firstItem = $node['items'][0] ?? [];
                if (is_array($firstItem)) {
                    $firstItemName = $firstItem['itemName'] ?? $firstItem['item_name'] ?? $firstItem['name'] ?? null;
                    if (!$productName && !empty($firstItemName)) {
                        $productName = (string) $firstItemName;
                        if (count($node['items']) > 1) {
                            $productName .= ' (+' . (count($node['items']) - 1) . ' sp khác)';
                        }
                    }
                    if (!$productImage) {
                        $productImage = $firstItem['imageUrl'] ?? $firstItem['itemImage'] ?? $firstItem['item_image'] ?? $firstItem['image'] ?? null;
                    }
                }
                if ($gmv <= 0) {
                    foreach ($node['items'] as $item) {
                        if (is_array($item)) {
                            $gmv += (float) ($item['itemPrice'] ?? $item['item_price'] ?? $item['price'] ?? 0);
                        }
                    }
                }
                if ($commission <= 0) {
                    foreach ($node['items'] as $item) {
                        if (is_array($item)) {
                            $commission += (float) ($item['itemCommission'] ?? $item['item_commission'] ?? $item['commission'] ?? 0);
                        }
                    }
                }
            }

            // Parse purchase timestamp (support seconds or 13-digit milliseconds)
            $rawTime = $node['purchaseTime'] ?? $node['purchase_time'] ?? null;
            $orderTime = now();
            if ($rawTime) {
                $ts = is_numeric($rawTime) ? (int) $rawTime : strtotime((string) $rawTime);
                if ($ts > 9999999999) { // 13-digit millisecond timestamp
                    $ts = (int) round($ts / 1000);
                }
                if ($ts > 0) {
                    $orderTime = date('Y-m-d H:i:s', $ts);
                }
            }

            // Match click if exists
            $click = CashbackClick::where('sub_id', $subId)->latest()->first();

            $orderData = [
                'shopee_order_id' => $orderId,
                'sub_id' => $subId,
                'click_id' => $click?->id,
                'product_name' => $productName ?: ('Sản phẩm Shopee #' . $orderId),
                'product_image' => $productImage,
                'gmv' => $gmv,
                'commission_shopee' => $commission,
                'status' => $node['orderStatus'] ?? $node['status'] ?? 'pending',
                'raw_data' => $node,
                'order_time' => $orderTime,
            ];

            $order = $this->walletService->processOrder($orderData);
            if ($order) {
                $processed[] = $order;
            }
        }

        return $processed;
    }
}

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
     * Process list of order nodes from API or Webhook payload.
     */
    public function processReportNodes(array $nodes): array
    {
        $processed = [];

        foreach ($nodes as $node) {
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

            if (!$subId) {
                continue;
            }

            // Extract product name, gmv, commission
            $productName = $node['product_name'] ?? null;
            $gmv = (float) ($node['gmv'] ?? 0);
            $commission = (float) ($node['totalCommission'] ?? $node['commission'] ?? 0);

            if (!empty($node['items']) && is_array($node['items'])) {
                $firstItem = $node['items'][0] ?? [];
                if (!$productName && !empty($firstItem['itemName'])) {
                    $productName = $firstItem['itemName'];
                }
                if ($gmv <= 0) {
                    foreach ($node['items'] as $item) {
                        $gmv += (float) ($item['itemPrice'] ?? 0);
                    }
                }
                if ($commission <= 0) {
                    foreach ($node['items'] as $item) {
                        $commission += (float) ($item['itemCommission'] ?? 0);
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

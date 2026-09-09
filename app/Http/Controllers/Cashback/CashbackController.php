<?php

namespace App\Http\Controllers\Cashback;

use App\Http\Controllers\Controller;
use App\Models\CashbackOrder;
use App\Models\CashbackWithdrawal;
use App\Services\CashbackOrderSyncService;
use App\Services\CashbackWalletService;
use App\Services\ShopeeAffiliateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CashbackController extends Controller
{
    protected CashbackWalletService $walletService;
    protected ShopeeAffiliateService $shopeeService;
    protected CashbackOrderSyncService $syncService;

    public function __construct(
        CashbackWalletService $walletService,
        ShopeeAffiliateService $shopeeService,
        CashbackOrderSyncService $syncService
    ) {
        $this->walletService = $walletService;
        $this->shopeeService = $shopeeService;
        $this->syncService = $syncService;
    }

    /**
     * Display Cashback Portal Dashboard (R1 & R3).
     */
    public function index(Request $request): InertiaResponse
    {
        $wallet = $this->walletService->getOrCreateWallet($request);

        $clicks = $wallet->clicks()->latest()->limit(15)->get();
        $orders = $wallet->orders()->latest()->limit(20)->get();
        $withdrawals = $wallet->withdrawals()->latest()->limit(15)->get();

        $stats = [
            'total_orders' => $wallet->orders()->count(),
            'pending_orders' => $wallet->orders()->where('status', 'pending')->count(),
            'confirmed_orders' => $wallet->orders()->where('status', 'confirmed')->count(),
            'cancelled_orders' => $wallet->orders()->whereIn('status', ['cancelled', 'refunded'])->count(),
            'cashback_rate_percent' => round(config('cashback.rate', 0.80) * 100),
            'min_withdrawal' => (float) config('cashback.min_withdrawal', 50000),
        ];

        return Inertia::render('Cashback/Index', [
            'wallet' => [
                'id' => $wallet->id,
                'sub_id' => $wallet->sub_id,
                'pending_balance' => (float) $wallet->pending_balance,
                'available_balance' => (float) $wallet->available_balance,
                'withdrawn_balance' => (float) $wallet->withdrawn_balance,
                'status' => $wallet->status,
            ],
            'clicks' => $clicks,
            'orders' => $orders,
            'withdrawals' => $withdrawals,
            'stats' => $stats,
        ]);
    }

    /**
     * Generate custom affiliate link with sub_id (R1 & R2).
     */
    public function generateLink(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'string'],
        ], [
            'url.required' => 'Vui lòng nhập đường dẫn sản phẩm Shopee.',
        ]);

        $url = trim($validated['url']);

        if (!ShopeeAffiliateService::isValidShopeeUrl($url)) {
            return response()->json([
                'success' => false,
                'message' => 'Đường dẫn không hợp lệ. Vui lòng dán link từ Shopee (shopee.vn, s.shopee.vn, shope.ee hoặc vn.shp.ee).',
            ], 422);
        }

        $wallet = $this->walletService->getOrCreateWallet($request);
        $shortLink = $this->shopeeService->generateShortLink($url, $wallet->sub_id);
        $click = $this->walletService->recordClick($wallet, $url, $shortLink, $request);

        return response()->json([
            'success' => true,
            'short_link' => $shortLink,
            'sub_id' => $wallet->sub_id,
            'original_url' => $url,
            'click_id' => $click->id,
            'message' => 'Tạo link hoàn tiền thành công! Hãy bấm mở Shopee và mua hàng để nhận hoàn tiền vào ví.',
        ]);
    }

    /**
     * Submit a withdrawal request (R3).
     */
    public function withdraw(Request $request): JsonResponse|RedirectResponse
    {
        $wallet = $this->walletService->getOrCreateWallet($request);
        $minWithdrawal = config('cashback.min_withdrawal', 50000);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:' . $minWithdrawal, 'max:' . $wallet->available_balance],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'bank_account_name' => ['required', 'string', 'max:100'],
        ], [
            'amount.min' => sprintf('Số tiền rút tối thiểu là %s đ.', number_format($minWithdrawal, 0, ',', '.')),
            'amount.max' => 'Số tiền rút không được vượt quá số dư khả dụng (' . number_format($wallet->available_balance, 0, ',', '.') . ' đ).',
            'bank_name.required' => 'Vui lòng chọn hoặc nhập tên ngân hàng.',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản ngân hàng.',
            'bank_account_name.required' => 'Vui lòng nhập tên chủ tài khoản.',
        ]);

        try {
            $withdrawal = $this->walletService->requestWithdrawal(
                $wallet,
                (float) $validated['amount'],
                [
                    'bank_name' => $validated['bank_name'],
                    'bank_account_number' => $validated['bank_account_number'],
                    'bank_account_name' => $validated['bank_account_name'],
                ]
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Yêu cầu rút tiền đã được gửi thành công và đang được xử lý.',
                    'withdrawal' => $withdrawal,
                ]);
            }

            return back()->with('success', 'Yêu cầu rút tiền thành công!');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withErrors(['amount' => $e->getMessage()]);
        }
    }

    /**
     * Webhook endpoint to receive order notifications or batch reports (R2).
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        // If order list provided
        $orders = $payload['orders'] ?? $payload['data'] ?? [$payload];
        if (!is_array($orders)) {
            $orders = [$orders];
        }

        $processed = $this->syncService->processReportNodes($orders);

        return response()->json([
            'success' => true,
            'processed_count' => count($processed),
        ]);
    }

    /**
     * Trigger manual or test order sync.
     */
    public function sync(Request $request): JsonResponse
    {
        $orders = $this->syncService->syncFromShopee();

        return response()->json([
            'success' => true,
            'synced_count' => count($orders),
        ]);
    }
}

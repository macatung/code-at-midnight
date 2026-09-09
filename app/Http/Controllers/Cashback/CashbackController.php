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
            'auth_user' => \Illuminate\Support\Facades\Auth::check() ? [
                'id' => \Illuminate\Support\Facades\Auth::id(),
                'name' => \Illuminate\Support\Facades\Auth::user()->name,
                'email' => \Illuminate\Support\Facades\Auth::user()->email,
            ] : null,
            'wallet' => [
                'id' => $wallet->id,
                'sub_id' => $wallet->sub_id,
                'pending_balance' => (float) $wallet->pending_balance,
                'available_balance' => (float) $wallet->available_balance,
                'withdrawn_balance' => (float) $wallet->withdrawn_balance,
                'default_bank_name' => $wallet->default_bank_name,
                'default_bank_account_number' => $wallet->default_bank_account_number,
                'default_bank_account_name' => $wallet->default_bank_account_name,
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

        $rawUrl = trim($validated['url']);
        $cleanUrl = ShopeeAffiliateService::extractShopeeUrl($rawUrl);

        if (!$cleanUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Đường dẫn không hợp lệ. Vui lòng dán link từ Shopee (shopee.vn, s.shopee.vn, shope.ee hoặc vn.shp.ee).',
            ], 422);
        }

        $wallet = $this->walletService->getOrCreateWallet($request);
        $shortLink = $this->shopeeService->generateShortLink($cleanUrl, $wallet->sub_id);
        $click = $this->walletService->recordClick($wallet, $cleanUrl, $shortLink, $request);

        return response()->json([
            'success' => true,
            'short_link' => $shortLink,
            'sub_id' => $wallet->sub_id,
            'original_url' => $cleanUrl,
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

        // If user is authenticated, verify their password for withdrawal safety
        if (\Illuminate\Support\Facades\Auth::check()) {
            $request->validate([
                'password' => ['required', 'string'],
            ], [
                'password.required' => 'Vui lòng nhập mật khẩu tài khoản để xác nhận rút tiền.',
            ]);

            if (!\Illuminate\Support\Facades\Hash::check((string) $request->input('password'), \Illuminate\Support\Facades\Auth::user()->password)) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mật khẩu xác thực tài khoản không chính xác. Vui lòng thử lại.',
                    ], 422);
                }
                return back()->withErrors(['password' => 'Mật khẩu xác thực không chính xác.']);
            }
        }

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

            // Optionally remember default bank details
            if ($request->boolean('save_default_bank')) {
                $this->walletService->saveDefaultBank(
                    $wallet,
                    $validated['bank_name'],
                    $validated['bank_account_number'],
                    $validated['bank_account_name']
                );
            }

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
        $appId = (string) config('cashback.shopee.app_id', '');
        $secret = (string) config('cashback.shopee.secret', '');
        $mockEnabled = (bool) config('cashback.shopee.mock_enabled', true);

        // Verify signature if live credentials configured
        if (!$mockEnabled && !empty($appId) && !empty($secret)) {
            $authHeader = (string) $request->header('Authorization', '');
            $timestamp = (string) $request->header('Timestamp', $request->header('X-Shopee-Timestamp', ''));
            $signature = (string) $request->header('Signature', $request->header('X-Shopee-Signature', ''));

            if (preg_match('/Credential=([^,]+),\s*Timestamp=([^,]+),\s*Signature=([^,\s]+)/i', $authHeader, $matches)) {
                $timestamp = $matches[2];
                $signature = $matches[3];
            }

            if (empty($timestamp) || empty($signature)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thiếu thông tin xác thực chữ ký Shopee.',
                ], 401);
            }

            $isValid = ShopeeAffiliateService::verifySignature($appId, $secret, $timestamp, $request->getContent(), $signature);
            if (!$isValid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chữ ký xác thực Shopee không hợp lệ.',
                ], 401);
            }
        }

        $orders = CashbackOrderSyncService::extractOrderNodes($request->all());
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

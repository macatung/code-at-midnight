<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashbackOrder;
use App\Models\CashbackWallet;
use App\Models\CashbackWithdrawal;
use App\Services\CashbackOrderSyncService;
use App\Services\CashbackWalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AdminCashbackController extends Controller
{
    protected CashbackWalletService $walletService;
    protected CashbackOrderSyncService $syncService;

    public function __construct(
        CashbackWalletService $walletService,
        CashbackOrderSyncService $syncService
    ) {
        $this->walletService = $walletService;
        $this->syncService = $syncService;
    }

    /**
     * Display Cashback and Withdrawal Management CMS.
     */
    public function index(Request $request): InertiaResponse
    {
        $statusFilter = $request->query('status', 'all');

        $withdrawalsQuery = CashbackWithdrawal::with(['user', 'wallet'])->latest();
        if ($statusFilter !== 'all') {
            $withdrawalsQuery->where('status', $statusFilter);
        }
        $withdrawals = $withdrawalsQuery->paginate(15)->withQueryString();

        $orders = CashbackOrder::with('user')->latest()->limit(20)->get();

        $stats = [
            'total_gmv' => (float) CashbackOrder::where('status', 'confirmed')->sum('gmv'),
            'total_commission' => (float) CashbackOrder::where('status', 'confirmed')->sum('commission_shopee'),
            'total_cashback_paid' => (float) CashbackWithdrawal::whereIn('status', ['completed', 'paid'])->sum('amount'),
            'pending_withdrawals_count' => (int) CashbackWithdrawal::where('status', 'pending')->count(),
            'pending_withdrawals_amount' => (float) CashbackWithdrawal::where('status', 'pending')->sum('amount'),
            'total_orders_count' => (int) CashbackOrder::count(),
            'confirmed_orders_count' => (int) CashbackOrder::where('status', 'confirmed')->count(),
            'total_wallets_count' => (int) CashbackWallet::count(),
            'registered_users_count' => (int) CashbackWallet::whereNotNull('user_id')->count(),
        ];

        return Inertia::render('Admin/Cashback/Index', [
            'withdrawals' => $withdrawals,
            'orders' => $orders,
            'stats' => $stats,
            'currentFilter' => $statusFilter,
        ]);
    }

    /**
     * Approve and mark withdrawal as paid (with VietQR reference or note).
     */
    public function approve(Request $request, CashbackWithdrawal $withdrawal): JsonResponse|RedirectResponse
    {
        $bankRefCode = $request->input('bank_ref_code');
        $adminNote = $request->input('admin_note');

        $this->walletService->approveWithdrawal($withdrawal, $bankRefCode, $adminNote);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã duyệt và đánh dấu hoàn tất chi trả yêu cầu rút tiền #' . $withdrawal->id,
            ]);
        }

        return back()->with('success', 'Đã duyệt yêu cầu rút tiền thành công!');
    }

    /**
     * Reject withdrawal and refund available balance.
     */
    public function reject(Request $request, CashbackWithdrawal $withdrawal): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ], [
            'reason.required' => 'Vui lòng cung cấp lý do từ chối yêu cầu rút tiền.',
        ]);

        $this->walletService->rejectWithdrawal($withdrawal, $validated['reason']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã từ chối yêu cầu rút tiền #' . $withdrawal->id . ' và hoàn trả lại số dư cho người dùng.',
            ]);
        }

        return back()->with('success', 'Đã từ chối yêu cầu rút tiền và hoàn trả số dư.');
    }

    /**
     * Trigger manual Shopee order conversion sync.
     */
    public function sync(Request $request): JsonResponse|RedirectResponse
    {
        $report = $this->syncService->syncOrders();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => sprintf('Đã đồng bộ đơn hàng thành công! Đã xử lý %d đơn hàng mới/cập nhật.', $report['processed_count'] ?? 0),
                'report' => $report,
            ]);
        }

        return back()->with('success', 'Đã đồng bộ đơn hàng từ Shopee!');
    }
}

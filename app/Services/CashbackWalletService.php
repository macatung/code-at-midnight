<?php

namespace App\Services;

use App\Models\CashbackClick;
use App\Models\CashbackLedger;
use App\Models\CashbackOrder;
use App\Models\CashbackWallet;
use App\Models\CashbackWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CashbackWalletService
{
    /**
     * Get or create wallet for current request (User or Session/Cookie).
     */
    public function getOrCreateWallet(Request $request): CashbackWallet
    {
        $user = Auth::user();
        if ($user) {
            $wallet = CashbackWallet::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'sub_id' => 'mt_u' . $user->id . '_' . Str::lower(Str::random(6)),
                    'pending_balance' => 0.00,
                    'available_balance' => 0.00,
                    'withdrawn_balance' => 0.00,
                    'status' => 'active',
                ]
            );
            return $wallet;
        }

        // Guest session / cookie handling
        $subId = $request->session()->get('cashback_sub_id') ?? $request->cookie('cashback_sub_id');

        if ($subId) {
            $wallet = CashbackWallet::where('sub_id', $subId)->first();
            if ($wallet) {
                return $wallet;
            }
        }

        // Create new guest wallet
        $newSubId = 'mt_s_' . Str::lower(Str::random(8));
        $wallet = CashbackWallet::create([
            'session_id' => $request->session()->getId(),
            'sub_id' => $newSubId,
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'withdrawn_balance' => 0.00,
            'status' => 'active',
        ]);

        $request->session()->put('cashback_sub_id', $newSubId);
        cookie()->queue('cashback_sub_id', $newSubId, 60 * 24 * 365);
        return $wallet;
    }

    /**
     * Record a click and affiliate link generation.
     */
    public function recordClick(CashbackWallet $wallet, string $originalUrl, string $shortLink, Request $request): CashbackClick
    {
        return CashbackClick::create([
            'wallet_id' => $wallet->id,
            'user_id' => $wallet->user_id,
            'sub_id' => $wallet->sub_id,
            'original_url' => $originalUrl,
            'affiliate_url' => $shortLink,
            'short_link' => $shortLink,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Process order change with strict transaction locking and ledger balance checks.
     * Guardrail: available balance NEVER goes below 0.
     * Guardrail: cancelled / refunded orders do NOT add to available balance.
     */
    public function processOrder(array $orderData): ?CashbackOrder
    {
        $subId = $orderData['sub_id'] ?? null;
        if (!$subId) {
            return null;
        }

        $wallet = CashbackWallet::where('sub_id', $subId)->first();
        if (!$wallet) {
            return null;
        }

        return DB::transaction(function () use ($wallet, $orderData) {
            // Lock wallet row
            $wallet = CashbackWallet::where('id', $wallet->id)->lockForUpdate()->firstOrFail();

            $shopeeOrderId = (string) $orderData['shopee_order_id'];
            $existingOrder = CashbackOrder::where('shopee_order_id', $shopeeOrderId)->lockForUpdate()->first();

            $commission = (float) ($orderData['commission_shopee'] ?? 0);
            $gmv = (float) ($orderData['gmv'] ?? 0);
            $rate = (float) ($orderData['cashback_rate'] ?? config('cashback.rate', 0.80));
            $cashbackAmount = round($commission * $rate, 2);
            $rawStatus = strtolower(trim((string) ($orderData['status'] ?? 'pending')));

            // Normalize status to: pending, confirmed, cancelled, refunded
            if (in_array($rawStatus, ['completed', 'settled', 'paid_out', 'confirmed', 'valid', 'success', 'successful'])) {
                $status = 'confirmed';
            } elseif (in_array($rawStatus, ['cancelled', 'canceled', 'failed', 'rejected', 'expired', 'unpaid', 'fraud', 'void'])) {
                $status = 'cancelled';
            } elseif (in_array($rawStatus, ['refunded', 'returned', 'invalid', 'dispute'])) {
                $status = 'refunded';
            } else {
                $status = 'pending';
            }

            $productName = \Illuminate\Support\Str::limit(
                (string) ($orderData['product_name'] ?? ('Đơn hàng Shopee #' . $shopeeOrderId)),
                250,
                '...'
            );

            if (!$existingOrder) {
                // New Order
                $order = CashbackOrder::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $wallet->user_id,
                    'click_id' => $orderData['click_id'] ?? null,
                    'shopee_order_id' => $shopeeOrderId,
                    'sub_id' => $wallet->sub_id,
                    'product_name' => $productName,
                    'product_image' => $orderData['product_image'] ?? null,
                    'gmv' => $gmv,
                    'commission_shopee' => $commission,
                    'cashback_rate' => $rate,
                    'cashback_amount' => $cashbackAmount,
                    'status' => $status,
                    'raw_data' => $orderData['raw_data'] ?? null,
                    'order_time' => $orderData['order_time'] ?? now(),
                ]);

                if ($status === 'pending') {
                    $wallet->pending_balance = round($wallet->pending_balance + $cashbackAmount, 2);
                    $wallet->save();

                    CashbackLedger::create([
                        'wallet_id' => $wallet->id,
                        'order_id' => $order->id,
                        'type' => 'order_pending',
                        'amount' => $cashbackAmount,
                        'balance_before' => $wallet->available_balance,
                        'balance_after' => $wallet->available_balance,
                        'description' => 'Ghi nhận đơn hàng Shopee #' . $shopeeOrderId . ' (Chờ đối soát)',
                    ]);
                } elseif ($status === 'confirmed') {
                    $wallet->available_balance = round($wallet->available_balance + $cashbackAmount, 2);
                    $wallet->save();

                    CashbackLedger::create([
                        'wallet_id' => $wallet->id,
                        'order_id' => $order->id,
                        'type' => 'order_confirmed',
                        'amount' => $cashbackAmount,
                        'balance_before' => round($wallet->available_balance - $cashbackAmount, 2),
                        'balance_after' => $wallet->available_balance,
                        'description' => 'Cộng tiền hoàn đơn Shopee #' . $shopeeOrderId,
                    ]);
                } else {
                    // Cancelled / refunded initial order: do not credit wallet
                    CashbackLedger::create([
                        'wallet_id' => $wallet->id,
                        'order_id' => $order->id,
                        'type' => 'order_cancelled',
                        'amount' => 0.00,
                        'balance_before' => $wallet->available_balance,
                        'balance_after' => $wallet->available_balance,
                        'description' => 'Đơn hàng Shopee #' . $shopeeOrderId . ' bị hủy/hoàn trả ngay từ đầu',
                    ]);
                }

                return $order;
            }

            // Existing Order update
            $oldStatus = $existingOrder->status;
            $oldAmount = (float) $existingOrder->cashback_amount;

            // Handle wallet balances transitions and adjustments
            if ($oldStatus === 'pending') {
                if ($status === 'pending') {
                    // Still pending, but amount may have changed
                    $delta = round($cashbackAmount - $oldAmount, 2);
                    if ($delta != 0.0) {
                        $wallet->pending_balance = max(0.00, round($wallet->pending_balance + $delta, 2));
                        CashbackLedger::create([
                            'wallet_id' => $wallet->id,
                            'order_id' => $existingOrder->id,
                            'type' => 'order_pending_adjusted',
                            'amount' => $delta,
                            'balance_before' => $wallet->available_balance,
                            'balance_after' => $wallet->available_balance,
                            'description' => 'Điều chỉnh hoa hồng chờ duyệt đơn Shopee #' . $shopeeOrderId . ' (' . ($delta > 0 ? '+' : '') . number_format($delta, 0, ',', '.') . ' đ)',
                        ]);
                    }
                } elseif ($status === 'confirmed') {
                    // Pending -> Confirmed
                    $wallet->pending_balance = max(0.00, round($wallet->pending_balance - $oldAmount, 2));
                    $before = $wallet->available_balance;
                    $wallet->available_balance = round($wallet->available_balance + $cashbackAmount, 2);

                    CashbackLedger::create([
                        'wallet_id' => $wallet->id,
                        'order_id' => $existingOrder->id,
                        'type' => 'order_confirmed',
                        'amount' => $cashbackAmount,
                        'balance_before' => $before,
                        'balance_after' => $wallet->available_balance,
                        'description' => 'Xác nhận đơn hàng Shopee #' . $shopeeOrderId . ', chuyển vào số dư khả dụng',
                    ]);
                } elseif (in_array($status, ['cancelled', 'refunded'])) {
                    // Pending -> Cancelled / Refunded
                    $wallet->pending_balance = max(0.00, round($wallet->pending_balance - $oldAmount, 2));

                    CashbackLedger::create([
                        'wallet_id' => $wallet->id,
                        'order_id' => $existingOrder->id,
                        'type' => 'order_cancelled',
                        'amount' => -$oldAmount,
                        'balance_before' => $wallet->available_balance,
                        'balance_after' => $wallet->available_balance,
                        'description' => 'Đơn hàng Shopee #' . $shopeeOrderId . ' bị hủy/hoàn trả, hủy tiền chờ duyệt',
                    ]);
                }
            } elseif ($oldStatus === 'confirmed') {
                if ($status === 'confirmed') {
                    // Still confirmed, but commission / cashback adjusted
                    $delta = round($cashbackAmount - $oldAmount, 2);
                    if ($delta != 0.0) {
                        $before = $wallet->available_balance;
                        $wallet->available_balance = max(0.00, round($wallet->available_balance + $delta, 2));
                        CashbackLedger::create([
                            'wallet_id' => $wallet->id,
                            'order_id' => $existingOrder->id,
                            'type' => 'order_adjusted',
                            'amount' => $delta,
                            'balance_before' => $before,
                            'balance_after' => $wallet->available_balance,
                            'description' => 'Điều chỉnh hoàn tiền đơn Shopee #' . $shopeeOrderId . ' (' . ($delta > 0 ? '+' : '') . number_format($delta, 0, ',', '.') . ' đ)',
                        ]);
                    }
                } elseif (in_array($status, ['cancelled', 'refunded'])) {
                    // Confirmed -> Cancelled / Refunded: revoke cashback (never negative)
                    $before = $wallet->available_balance;
                    $wallet->available_balance = max(0.00, round($wallet->available_balance - $oldAmount, 2));

                    CashbackLedger::create([
                        'wallet_id' => $wallet->id,
                        'order_id' => $existingOrder->id,
                        'type' => 'order_revoked',
                        'amount' => -$oldAmount,
                        'balance_before' => $before,
                        'balance_after' => $wallet->available_balance,
                        'description' => 'Khấu trừ hoàn tiền do đơn Shopee #' . $shopeeOrderId . ' bị hủy/hoàn trả',
                    ]);
                } elseif ($status === 'pending') {
                    // Confirmed -> Pending (re-examination)
                    $before = $wallet->available_balance;
                    $wallet->available_balance = max(0.00, round($wallet->available_balance - $oldAmount, 2));
                    $wallet->pending_balance = round($wallet->pending_balance + $cashbackAmount, 2);

                    CashbackLedger::create([
                        'wallet_id' => $wallet->id,
                        'order_id' => $existingOrder->id,
                        'type' => 'order_reopened_pending',
                        'amount' => -$oldAmount,
                        'balance_before' => $before,
                        'balance_after' => $wallet->available_balance,
                        'description' => 'Đơn hàng Shopee #' . $shopeeOrderId . ' chuyển lại trạng thái chờ đối soát',
                    ]);
                }
            } elseif (in_array($oldStatus, ['cancelled', 'refunded'])) {
                if ($status === 'confirmed') {
                    // Cancelled/Refunded -> Confirmed: re-instate cashback
                    $before = $wallet->available_balance;
                    $wallet->available_balance = round($wallet->available_balance + $cashbackAmount, 2);

                    CashbackLedger::create([
                        'wallet_id' => $wallet->id,
                        'order_id' => $existingOrder->id,
                        'type' => 'order_confirmed',
                        'amount' => $cashbackAmount,
                        'balance_before' => $before,
                        'balance_after' => $wallet->available_balance,
                        'description' => 'Khôi phục và cộng tiền hoàn đơn Shopee #' . $shopeeOrderId,
                    ]);
                } elseif ($status === 'pending') {
                    // Cancelled/Refunded -> Pending
                    $wallet->pending_balance = round($wallet->pending_balance + $cashbackAmount, 2);

                    CashbackLedger::create([
                        'wallet_id' => $wallet->id,
                        'order_id' => $existingOrder->id,
                        'type' => 'order_pending',
                        'amount' => $cashbackAmount,
                        'balance_before' => $wallet->available_balance,
                        'balance_after' => $wallet->available_balance,
                        'description' => 'Ghi nhận lại đơn hàng Shopee #' . $shopeeOrderId . ' (Chờ đối soát)',
                    ]);
                }
            }

            // Always update order details
            $existingOrder->status = $status;
            $existingOrder->cashback_amount = $cashbackAmount;
            $existingOrder->commission_shopee = $commission;
            $existingOrder->gmv = $gmv;
            $existingOrder->cashback_rate = $rate;
            $existingOrder->product_name = $productName;
            if (!empty($orderData['raw_data'])) {
                $existingOrder->raw_data = $orderData['raw_data'];
            }
            $existingOrder->save();
            $wallet->save();

            return $existingOrder;
        });
    }

    /**
     * Request withdrawal with strict balance guardrails (no negative balance).
     */
    public function requestWithdrawal(CashbackWallet $wallet, float $amount, array $bankDetails): CashbackWithdrawal
    {
        if ($wallet->status !== 'active') {
            throw new \InvalidArgumentException('Tài khoản ví đang bị khóa hoặc tạm ngưng. Vui lòng liên hệ hỗ trợ.');
        }

        $minWithdrawal = config('cashback.min_withdrawal', 50000);
        if ($amount < $minWithdrawal) {
            throw new \InvalidArgumentException(sprintf('Số tiền rút tối thiểu là %s đ.', number_format($minWithdrawal, 0, ',', '.')));
        }

        return DB::transaction(function () use ($wallet, $amount, $bankDetails) {
            $wallet = CashbackWallet::where('id', $wallet->id)->lockForUpdate()->firstOrFail();

            if ($wallet->status !== 'active') {
                throw new \InvalidArgumentException('Tài khoản ví đang bị khóa hoặc tạm ngưng. Vui lòng liên hệ hỗ trợ.');
            }

            if ($wallet->available_balance < $amount) {
                throw new \InvalidArgumentException('Số dư khả dụng không đủ để thực hiện yêu cầu rút tiền.');
            }

            $before = $wallet->available_balance;
            $wallet->available_balance = round($wallet->available_balance - $amount, 2);
            $wallet->save();

            $withdrawal = CashbackWithdrawal::create([
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'amount' => $amount,
                'bank_name' => $bankDetails['bank_name'],
                'bank_account_number' => $bankDetails['bank_account_number'],
                'bank_account_name' => $bankDetails['bank_account_name'],
                'status' => 'pending',
            ]);

            CashbackLedger::create([
                'wallet_id' => $wallet->id,
                'withdrawal_id' => $withdrawal->id,
                'type' => 'withdrawal_requested',
                'amount' => -$amount,
                'balance_before' => $before,
                'balance_after' => $wallet->available_balance,
                'description' => sprintf('Yêu cầu rút tiền về %s (%s - %s)', $bankDetails['bank_name'], $bankDetails['bank_account_number'], $bankDetails['bank_account_name']),
            ]);

            return $withdrawal;
        });
    }

    /**
     * Approve and mark withdrawal as completed.
     */
    public function approveWithdrawal(CashbackWithdrawal $withdrawal): void
    {
        DB::transaction(function () use ($withdrawal) {
            $withdrawal = CashbackWithdrawal::where('id', $withdrawal->id)->lockForUpdate()->firstOrFail();
            if ($withdrawal->status !== 'pending') {
                return;
            }

            $wallet = CashbackWallet::where('id', $withdrawal->wallet_id)->lockForUpdate()->firstOrFail();
            $wallet->withdrawn_balance = round($wallet->withdrawn_balance + $withdrawal->amount, 2);
            $wallet->save();

            $withdrawal->status = 'completed';
            $withdrawal->processed_at = now();
            $withdrawal->save();

            CashbackLedger::create([
                'wallet_id' => $wallet->id,
                'withdrawal_id' => $withdrawal->id,
                'type' => 'withdrawal_completed',
                'amount' => $withdrawal->amount,
                'balance_before' => $wallet->available_balance,
                'balance_after' => $wallet->available_balance,
                'description' => 'Hoàn tất chi trả yêu cầu rút tiền #' . $withdrawal->id,
            ]);
        });
    }

    /**
     * Reject withdrawal and refund available balance.
     */
    public function rejectWithdrawal(CashbackWithdrawal $withdrawal, string $reason = ''): void
    {
        DB::transaction(function () use ($withdrawal, $reason) {
            $withdrawal = CashbackWithdrawal::where('id', $withdrawal->id)->lockForUpdate()->firstOrFail();
            if ($withdrawal->status !== 'pending') {
                return;
            }

            $wallet = CashbackWallet::where('id', $withdrawal->wallet_id)->lockForUpdate()->firstOrFail();
            $before = $wallet->available_balance;
            $wallet->available_balance = round($wallet->available_balance + $withdrawal->amount, 2);
            $wallet->save();

            $withdrawal->status = 'rejected';
            $withdrawal->note = $reason;
            $withdrawal->processed_at = now();
            $withdrawal->save();

            CashbackLedger::create([
                'wallet_id' => $wallet->id,
                'withdrawal_id' => $withdrawal->id,
                'type' => 'withdrawal_rejected_refund',
                'amount' => $withdrawal->amount,
                'balance_before' => $before,
                'balance_after' => $wallet->available_balance,
                'description' => 'Hoàn trả số dư do từ chối yêu cầu rút tiền #' . $withdrawal->id . ($reason ? ': ' . $reason : ''),
            ]);
        });
    }
}

<?php

namespace Tests\Feature;

use App\Models\CashbackLedger;
use App\Models\CashbackOrder;
use App\Models\CashbackWallet;
use App\Models\CashbackWithdrawal;
use App\Services\CashbackWalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CashbackWalletAndFraudSafetyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test all 4 required cashback tables and ledger table exist in database.
     */
    public function test_cashback_database_tables_exist_with_proper_columns(): void
    {
        $tables = [
            'cashback_wallets',
            'cashback_clicks',
            'cashback_orders',
            'cashback_withdrawals',
            'cashback_ledgers',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table {$table} should exist.");
        }

        $this->assertTrue(Schema::hasColumns('cashback_wallets', [
            'id', 'user_id', 'session_id', 'sub_id', 'pending_balance', 'available_balance', 'withdrawn_balance', 'status',
        ]));

        $this->assertTrue(Schema::hasColumns('cashback_orders', [
            'id', 'wallet_id', 'shopee_order_id', 'sub_id', 'gmv', 'commission_shopee', 'cashback_rate', 'cashback_amount', 'status',
        ]));

        $this->assertTrue(Schema::hasColumns('cashback_withdrawals', [
            'id', 'wallet_id', 'amount', 'bank_name', 'bank_account_number', 'bank_account_name', 'status',
        ]));
    }

    /**
     * Test order lifecycle: Pending -> Confirmed updates balances and records ledger entries.
     */
    public function test_order_pending_then_confirmed_lifecycle(): void
    {
        $walletService = app(CashbackWalletService::class);

        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_lifecycle_user',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'withdrawn_balance' => 0.00,
            'status' => 'active',
        ]);

        // 1. Order arrives as Pending
        $order = $walletService->processOrder([
            'shopee_order_id' => 'ORDER_001',
            'sub_id' => 'mt_lifecycle_user',
            'gmv' => 1000000,
            'commission_shopee' => 100000,
            'cashback_rate' => 0.80,
            'status' => 'pending',
            'product_name' => 'Ghế Gaming Cao Cấp',
        ]);

        $wallet->refresh();
        $this->assertEquals(80000.00, $wallet->pending_balance);
        $this->assertEquals(0.00, $wallet->available_balance);

        $this->assertDatabaseHas('cashback_ledgers', [
            'wallet_id' => $wallet->id,
            'order_id' => $order->id,
            'type' => 'order_pending',
            'amount' => 80000.00,
        ]);

        // 2. Order confirmed by Shopee
        $walletService->processOrder([
            'shopee_order_id' => 'ORDER_001',
            'sub_id' => 'mt_lifecycle_user',
            'commission_shopee' => 100000,
            'cashback_rate' => 0.80,
            'status' => 'confirmed',
        ]);

        $wallet->refresh();
        $this->assertEquals(0.00, $wallet->pending_balance);
        $this->assertEquals(80000.00, $wallet->available_balance);

        $this->assertDatabaseHas('cashback_ledgers', [
            'wallet_id' => $wallet->id,
            'order_id' => $order->id,
            'type' => 'order_confirmed',
            'amount' => 80000.00,
        ]);
    }

    /**
     * Test order cancellation: Pending order cancelled revokes pending balance and never credits available balance.
     */
    public function test_pending_order_cancellation_revokes_pending_balance(): void
    {
        $walletService = app(CashbackWalletService::class);

        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_cancel_user',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'withdrawn_balance' => 0.00,
            'status' => 'active',
        ]);

        // Order arrives as Pending
        $order = $walletService->processOrder([
            'shopee_order_id' => 'ORDER_CANCEL_01',
            'sub_id' => 'mt_cancel_user',
            'commission_shopee' => 50000,
            'cashback_rate' => 0.80,
            'status' => 'pending',
            'product_name' => 'Sạc Nhanh Anker 65W',
        ]);

        $wallet->refresh();
        $this->assertEquals(40000.00, $wallet->pending_balance);

        // Shopee informs order is Cancelled
        $walletService->processOrder([
            'shopee_order_id' => 'ORDER_CANCEL_01',
            'sub_id' => 'mt_cancel_user',
            'commission_shopee' => 50000,
            'cashback_rate' => 0.80,
            'status' => 'cancelled',
        ]);

        $wallet->refresh();
        $this->assertEquals(0.00, $wallet->pending_balance);
        $this->assertEquals(0.00, $wallet->available_balance);

        $this->assertDatabaseHas('cashback_ledgers', [
            'wallet_id' => $wallet->id,
            'order_id' => $order->id,
            'type' => 'order_cancelled',
            'amount' => -40000.00,
        ]);
    }

    /**
     * Test order arriving initially as Cancelled / Refunded does NOT credit available balance.
     */
    public function test_initial_cancelled_order_never_credits_balance(): void
    {
        $walletService = app(CashbackWalletService::class);

        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_instant_cancel',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'withdrawn_balance' => 0.00,
            'status' => 'active',
        ]);

        $walletService->processOrder([
            'shopee_order_id' => 'ORDER_INSTANT_CANCEL',
            'sub_id' => 'mt_instant_cancel',
            'commission_shopee' => 60000,
            'status' => 'cancelled',
            'product_name' => 'Đơn hàng hủy',
        ]);

        $wallet->refresh();
        $this->assertEquals(0.00, $wallet->pending_balance);
        $this->assertEquals(0.00, $wallet->available_balance);
    }

    /**
     * Test financial guardrail: available balance NEVER goes negative if confirmed order is revoked.
     */
    public function test_revoking_confirmed_order_never_leaves_negative_available_balance(): void
    {
        $walletService = app(CashbackWalletService::class);

        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_floor_guard',
            'pending_balance' => 0.00,
            'available_balance' => 30000.00, // Less than order cashback amount
            'status' => 'active',
        ]);

        $order = CashbackOrder::create([
            'wallet_id' => $wallet->id,
            'shopee_order_id' => 'ORDER_REVOKE_01',
            'sub_id' => 'mt_floor_guard',
            'product_name' => 'Bàn phím cơ',
            'gmv' => 500000,
            'commission_shopee' => 50000,
            'cashback_rate' => 0.80,
            'cashback_amount' => 40000.00, // 40k > 30k current balance
            'status' => 'confirmed',
        ]);

        // Revoke order
        $walletService->processOrder([
            'shopee_order_id' => 'ORDER_REVOKE_01',
            'sub_id' => 'mt_floor_guard',
            'commission_shopee' => 50000,
            'cashback_rate' => 0.80,
            'status' => 'refunded',
        ]);

        $wallet->refresh();
        // Guardrail: must be 0, never negative!
        $this->assertEquals(0.00, $wallet->available_balance);
        $this->assertGreaterThanOrEqual(0.00, $wallet->available_balance);
    }

    /**
     * Test withdrawal: valid request reduces available balance immediately.
     */
    public function test_valid_withdrawal_request_reduces_available_balance(): void
    {
        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_withdraw_user',
            'pending_balance' => 0.00,
            'available_balance' => 150000.00,
            'withdrawn_balance' => 0.00,
            'status' => 'active',
        ]);

        $response = $this->withSession(['cashback_sub_id' => 'mt_withdraw_user'])
            ->postJson('/hoantien/withdraw', [
                'amount' => 100000,
                'bank_name' => 'MB Bank',
                'bank_account_number' => '0987654321',
                'bank_account_name' => 'NGUYEN VAN B',
            ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        $wallet->refresh();
        $this->assertEquals(50000.00, $wallet->available_balance);

        $this->assertDatabaseHas('cashback_withdrawals', [
            'wallet_id' => $wallet->id,
            'amount' => 100000.00,
            'bank_name' => 'MB Bank',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('cashback_ledgers', [
            'wallet_id' => $wallet->id,
            'type' => 'withdrawal_requested',
            'amount' => -100000.00,
            'balance_after' => 50000.00,
        ]);
    }

    /**
     * Test withdrawal guardrail: cannot withdraw more than available balance.
     */
    public function test_withdrawal_fails_when_amount_exceeds_available_balance(): void
    {
        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_insufficient_user',
            'pending_balance' => 200000.00,
            'available_balance' => 60000.00, // Only 60k available
            'withdrawn_balance' => 0.00,
            'status' => 'active',
        ]);

        $response = $this->withSession(['cashback_sub_id' => 'mt_insufficient_user'])
            ->postJson('/hoantien/withdraw', [
                'amount' => 100000, // 100k > 60k
                'bank_name' => 'Techcombank',
                'bank_account_number' => '190312345678',
                'bank_account_name' => 'LE VAN C',
            ]);

        $response->assertStatus(422);

        $wallet->refresh();
        $this->assertEquals(60000.00, $wallet->available_balance);
        $this->assertDatabaseMissing('cashback_withdrawals', [
            'wallet_id' => $wallet->id,
        ]);
    }

    /**
     * Test withdrawal guardrail: cannot withdraw below minimum threshold (50,000 VND).
     */
    public function test_withdrawal_fails_when_below_minimum_threshold(): void
    {
        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_min_user',
            'available_balance' => 100000.00,
            'status' => 'active',
        ]);

        $response = $this->withSession(['cashback_sub_id' => 'mt_min_user'])
            ->postJson('/hoantien/withdraw', [
                'amount' => 20000, // Below 50k
                'bank_name' => 'ACB',
                'bank_account_number' => '123456789',
                'bank_account_name' => 'TRAN THI D',
            ]);

        $response->assertStatus(422);
    }

    /**
     * Test admin approval and rejection workflows.
     */
    public function test_admin_approval_and_rejection_balance_flow(): void
    {
        $walletService = app(CashbackWalletService::class);

        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_admin_flow',
            'available_balance' => 200000.00,
            'withdrawn_balance' => 0.00,
            'status' => 'active',
        ]);

        // User requests 70,000
        $withdrawal1 = $walletService->requestWithdrawal($wallet, 70000.00, [
            'bank_name' => 'Vietcombank',
            'bank_account_number' => '0011223344',
            'bank_account_name' => 'HOANG VAN E',
        ]);

        $wallet->refresh();
        $this->assertEquals(130000.00, $wallet->available_balance);

        // Approve withdrawal 1
        $walletService->approveWithdrawal($withdrawal1);
        $wallet->refresh();
        $withdrawal1->refresh();

        $this->assertEquals('completed', $withdrawal1->status);
        $this->assertEquals(70000.00, $wallet->withdrawn_balance);
        $this->assertEquals(130000.00, $wallet->available_balance);

        // User requests another 50,000
        $withdrawal2 = $walletService->requestWithdrawal($wallet, 50000.00, [
            'bank_name' => 'Vietcombank',
            'bank_account_number' => '0011223344',
            'bank_account_name' => 'HOANG VAN E',
        ]);

        $wallet->refresh();
        $this->assertEquals(80000.00, $wallet->available_balance);

        // Reject withdrawal 2 (e.g. invalid bank account)
        $walletService->rejectWithdrawal($withdrawal2, 'STK không chính xác');
        $wallet->refresh();
        $withdrawal2->refresh();

        $this->assertEquals('rejected', $withdrawal2->status);
        // Refunded back to available balance: 80,000 + 50,000 = 130,000
        $this->assertEquals(130000.00, $wallet->available_balance);
        $this->assertEquals(70000.00, $wallet->withdrawn_balance);
    }

    /**
     * Adversarial Test: Order status stays 'pending', but commission is updated.
     * Expected: commission, cashback and wallet pending balance must be updated.
     */
    public function test_pending_order_with_same_status_updates_commission_and_pending_balance(): void
    {
        $walletService = app(CashbackWalletService::class);

        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_same_status_user',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        // 1. Initial pending order with 10,000 commission (8,000 cashback)
        $order = $walletService->processOrder([
            'shopee_order_id' => 'ORDER_SAME_01',
            'sub_id' => 'mt_same_status_user',
            'commission_shopee' => 10000,
            'cashback_rate' => 0.80,
            'status' => 'pending',
            'product_name' => 'Sản phẩm thử nghiệm',
        ]);

        $wallet->refresh();
        $this->assertEquals(8000.00, $wallet->pending_balance);
        $this->assertEquals(8000.00, $order->cashback_amount);

        // 2. Shopee sends update: still pending, but final commission is 50,000 (40,000 cashback)
        $updatedOrder = $walletService->processOrder([
            'shopee_order_id' => 'ORDER_SAME_01',
            'sub_id' => 'mt_same_status_user',
            'commission_shopee' => 50000,
            'cashback_rate' => 0.80,
            'status' => 'pending',
            'product_name' => 'Sản phẩm thử nghiệm (cập nhật)',
        ]);

        $wallet->refresh();
        $this->assertEquals(40000.00, $wallet->pending_balance);
        $this->assertEquals(50000.00, $updatedOrder->commission_shopee);
        $this->assertEquals(40000.00, $updatedOrder->cashback_amount);
    }

    /**
     * Adversarial Test: Previously cancelled order transitioned to confirmed.
     * Expected: order confirmed, available balance credited.
     */
    public function test_cancelled_order_transitioning_to_confirmed_credits_available_balance(): void
    {
        $walletService = app(CashbackWalletService::class);

        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_cancel_to_confirm_user',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        // 1. Arrives as cancelled
        $order = $walletService->processOrder([
            'shopee_order_id' => 'ORDER_REINSTATE_01',
            'sub_id' => 'mt_cancel_to_confirm_user',
            'commission_shopee' => 50000,
            'cashback_rate' => 0.80,
            'status' => 'cancelled',
            'product_name' => 'Đơn tạm hủy',
        ]);

        $wallet->refresh();
        $this->assertEquals(0.00, $wallet->pending_balance);
        $this->assertEquals(0.00, $wallet->available_balance);

        // 2. Shopee re-confirms order
        $updatedOrder = $walletService->processOrder([
            'shopee_order_id' => 'ORDER_REINSTATE_01',
            'sub_id' => 'mt_cancel_to_confirm_user',
            'commission_shopee' => 50000,
            'cashback_rate' => 0.80,
            'status' => 'confirmed',
            'product_name' => 'Đơn tạm hủy',
        ]);

        $wallet->refresh();
        $this->assertEquals('confirmed', $updatedOrder->status);
        $this->assertEquals(40000.00, $wallet->available_balance);
        $this->assertEquals(0.00, $wallet->pending_balance);
    }

    /**
     * Adversarial Test: Failed, rejected, or expired orders must NEVER be treated as pending.
     */
    public function test_failed_or_rejected_order_never_credits_pending_balance(): void
    {
        $walletService = app(CashbackWalletService::class);

        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_failed_order_user',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        $order = $walletService->processOrder([
            'shopee_order_id' => 'ORDER_FAILED_99',
            'sub_id' => 'mt_failed_order_user',
            'commission_shopee' => 100000,
            'cashback_rate' => 0.80,
            'status' => 'FAILED',
            'product_name' => 'Đơn hàng thất bại',
        ]);

        $wallet->refresh();
        $this->assertEquals(0.00, $wallet->pending_balance);
        $this->assertEquals(0.00, $wallet->available_balance);
        $this->assertContains($order->status, ['cancelled', 'refunded']);
    }

    /**
     * Adversarial Test: Frozen or suspended wallet cannot request withdrawal.
     */
    public function test_frozen_wallet_cannot_request_withdrawal(): void
    {
        $walletService = app(CashbackWalletService::class);

        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_frozen_user',
            'available_balance' => 100000.00,
            'status' => 'frozen',
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('khóa hoặc tạm ngưng');

        $walletService->requestWithdrawal($wallet, 50000.00, [
            'bank_name' => 'Vietcombank',
            'bank_account_number' => '12345678',
            'bank_account_name' => 'FROZEN USER',
        ]);
    }

    /**
     * Adversarial Test: Shopee products with long names (>255 chars) do not crash DB.
     */
    public function test_long_product_name_is_safely_truncated(): void
    {
        $walletService = app(CashbackWalletService::class);

        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_long_name_user',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        $longName = str_repeat('Bàn phím cơ Shopee Siêu Bền ', 20); // ~560 chars

        $order = $walletService->processOrder([
            'shopee_order_id' => 'ORDER_LONG_NAME_01',
            'sub_id' => 'mt_long_name_user',
            'commission_shopee' => 50000,
            'status' => 'pending',
            'product_name' => $longName,
        ]);

        $this->assertNotNull($order);
        $this->assertLessThanOrEqual(255, mb_strlen($order->product_name));
    }

    /**
     * Adversarial Test: If an update payload for an existing order carries a different sub_id,
     * the system must lock and credit the original owner wallet, preventing cross-user wallet hijacking.
     */
    public function test_cross_wallet_order_hijacking_prevented_and_owner_wallet_preserved(): void
    {
        $walletService = app(CashbackWalletService::class);

        $walletA = CashbackWallet::create([
            'sub_id' => 'mt_victim_user_a',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        $walletB = CashbackWallet::create([
            'sub_id' => 'mt_attacker_user_b',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        // Order created by User A
        $order = $walletService->processOrder([
            'shopee_order_id' => 'ORDER_HIJACK_01',
            'sub_id' => 'mt_victim_user_a',
            'commission_shopee' => 50000,
            'status' => 'pending',
            'product_name' => 'Sản phẩm của User A',
        ]);

        $walletA->refresh();
        $this->assertEquals(40000.00, $walletA->pending_balance);

        // Attacker sends update attempting to claim ORDER_HIJACK_01 to User B
        $walletService->processOrder([
            'shopee_order_id' => 'ORDER_HIJACK_01',
            'sub_id' => 'mt_attacker_user_b', // Mismatched sub_id!
            'commission_shopee' => 50000,
            'status' => 'confirmed',
        ]);

        $walletA->refresh();
        $walletB->refresh();

        // Victim User A must receive confirmed balance
        $this->assertEquals(40000.00, $walletA->available_balance);
        $this->assertEquals(0.00, $walletA->pending_balance);

        // Attacker User B gets NOTHING
        $this->assertEquals(0.00, $walletB->available_balance);
        $this->assertEquals(0.00, $walletB->pending_balance);
    }

    /**
     * Adversarial Test: Malicious or erroneous negative commission values are clamped to 0.00.
     */
    public function test_negative_or_zero_commission_is_safely_clamped_and_never_drops_balance_below_zero(): void
    {
        $walletService = app(CashbackWalletService::class);

        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_negative_guard',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        $order = $walletService->processOrder([
            'shopee_order_id' => 'ORDER_NEGATIVE_01',
            'sub_id' => 'mt_negative_guard',
            'commission_shopee' => -100000,
            'gmv' => -500000,
            'status' => 'confirmed',
            'product_name' => 'Đơn âm hoa hồng',
        ]);

        $wallet->refresh();
        $this->assertEquals(0.00, $wallet->available_balance);
        $this->assertEquals(0.00, $wallet->pending_balance);
        $this->assertEquals(0.00, $order->commission_shopee);
        $this->assertEquals(0.00, $order->cashback_amount);
    }

    /**
     * Adversarial Test: cashback:sync-orders is scheduled in routes/console.php.
     */
    public function test_cashback_sync_orders_is_scheduled_hourly(): void
    {
        $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
        $events = collect($schedule->events());

        $hasCashbackSync = $events->contains(function ($event) {
            return str_contains($event->command, 'cashback:sync-orders');
        });

        $this->assertTrue($hasCashbackSync, 'Expected cashback:sync-orders to be scheduled');
    }

    /**
     * Adversarial Test: Directly calling processOrder on an existing order without providing sub_id
     * still updates the order and credits the owner wallet.
     */
    public function test_existing_order_update_without_sub_id_updates_order_and_credits_owner_wallet(): void
    {
        $walletService = app(CashbackWalletService::class);

        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_no_sub_direct_user',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        // 1. Initial pending order
        $order = $walletService->processOrder([
            'shopee_order_id' => 'ORDER_NO_SUB_DIRECT_01',
            'sub_id' => 'mt_no_sub_direct_user',
            'commission_shopee' => 50000,
            'status' => 'pending',
            'product_name' => 'Sản phẩm thử nghiệm',
        ]);

        $this->assertNotNull($order);
        $wallet->refresh();
        $this->assertEquals(40000.00, $wallet->pending_balance);

        // 2. Shopee callback updates order to confirmed, but omits sub_id completely
        $updatedOrder = $walletService->processOrder([
            'shopee_order_id' => 'ORDER_NO_SUB_DIRECT_01',
            // No sub_id provided!
            'commission_shopee' => 50000,
            'status' => 'confirmed',
        ]);

        $this->assertNotNull($updatedOrder);
        $this->assertEquals('confirmed', $updatedOrder->status);

        $wallet->refresh();
        $this->assertEquals(0.00, $wallet->pending_balance);
        $this->assertEquals(40000.00, $wallet->available_balance);
    }

    /**
     * Adversarial Test: Shopee products with extremely long product_image (>500 chars) do not fail DB insertion.
     */
    public function test_extremely_long_product_image_url_is_safely_truncated(): void
    {
        $walletService = app(CashbackWalletService::class);

        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_long_img_user',
            'pending_balance' => 0.00,
            'available_balance' => 0.00,
            'status' => 'active',
        ]);

        $longImage = 'https://cf.shopee.vn/file/' . str_repeat('image_token_query_param_', 30) . '.jpg'; // ~750 chars

        $order = $walletService->processOrder([
            'shopee_order_id' => 'ORDER_LONG_IMG_01',
            'sub_id' => 'mt_long_img_user',
            'commission_shopee' => 50000,
            'status' => 'pending',
            'product_image' => $longImage,
        ]);

        $this->assertNotNull($order);
        $this->assertLessThanOrEqual(500, mb_strlen($order->product_image));

        // Also test update with long image
        $updatedOrder = $walletService->processOrder([
            'shopee_order_id' => 'ORDER_LONG_IMG_01',
            'status' => 'confirmed',
            'commission_shopee' => 50000,
            'product_image' => $longImage . '_updated',
        ]);

        $this->assertNotNull($updatedOrder);
        $this->assertLessThanOrEqual(500, mb_strlen($updatedOrder->product_image));
    }
}


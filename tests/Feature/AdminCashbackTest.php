<?php

namespace Tests\Feature;

use App\Models\CashbackOrder;
use App\Models\CashbackWallet;
use App\Models\CashbackWithdrawal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCashbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cashback_dashboard_requires_auth(): void
    {
        $response = $this->get('/admin/cashback');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_cashback_dashboard(): void
    {
        $response = $this->withSession(['admin_authenticated' => true])
            ->get('/admin/cashback');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Cashback/Index')
            ->has('withdrawals')
            ->has('orders')
            ->has('stats')
        );
    }

    public function test_admin_can_approve_withdrawal_with_ref_code(): void
    {
        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_u1_test',
            'available_balance' => 40000.00,
            'withdrawn_balance' => 0.00,
            'pending_balance' => 0.00,
            'status' => 'active',
        ]);

        $withdrawal = CashbackWithdrawal::create([
            'wallet_id' => $wallet->id,
            'amount' => 60000.00,
            'bank_name' => 'Techcombank',
            'bank_account_number' => '1903652881',
            'bank_account_name' => 'NGUYEN VAN TEST',
            'status' => 'pending',
        ]);

        $response = $this->withSession(['admin_authenticated' => true])
            ->postJson("/admin/cashback/withdrawals/{$withdrawal->id}/approve", [
                'bank_ref_code' => 'FT262529981293',
                'admin_note' => 'Đã quét VietQR chuyển khoản thành công',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $withdrawal->refresh();
        $this->assertEquals('completed', $withdrawal->status);
        $this->assertEquals('FT262529981293', $withdrawal->bank_ref_code);
        $this->assertEquals('Đã quét VietQR chuyển khoản thành công', $withdrawal->admin_note);
        $this->assertNotNull($withdrawal->processed_at);

        $wallet->refresh();
        $this->assertEquals(60000.00, (float) $wallet->withdrawn_balance);
    }

    public function test_admin_can_reject_withdrawal_and_refunds_balance(): void
    {
        $wallet = CashbackWallet::create([
            'sub_id' => 'mt_u2_test',
            'available_balance' => 20000.00,
            'withdrawn_balance' => 0.00,
            'pending_balance' => 0.00,
            'status' => 'active',
        ]);

        $withdrawal = CashbackWithdrawal::create([
            'wallet_id' => $wallet->id,
            'amount' => 80000.00,
            'bank_name' => 'MB Bank',
            'bank_account_number' => '0987654321',
            'bank_account_name' => 'LE VAN REJECT',
            'status' => 'pending',
        ]);

        $response = $this->withSession(['admin_authenticated' => true])
            ->postJson("/admin/cashback/withdrawals/{$withdrawal->id}/reject", [
                'reason' => 'Số tài khoản ngân hàng không tồn tại trên hệ thống Napas.',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $withdrawal->refresh();
        $this->assertEquals('rejected', $withdrawal->status);
        $this->assertEquals('Số tài khoản ngân hàng không tồn tại trên hệ thống Napas.', $withdrawal->note);

        $wallet->refresh();
        // 20,000 + 80,000 = 100,000 refunded
        $this->assertEquals(100000.00, (float) $wallet->available_balance);
    }

    public function test_admin_can_trigger_shopee_order_sync(): void
    {
        $response = $this->withSession(['admin_authenticated' => true])
            ->postJson('/admin/cashback/sync');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
    }
}

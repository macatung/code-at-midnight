<?php

namespace Tests\Feature;

use App\Models\CashbackClick;
use App\Models\CashbackOrder;
use App\Models\CashbackWallet;
use App\Models\CashbackWithdrawal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CashbackAuthAndProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_from_cashback_portal(): void
    {
        $response = $this->postJson('/hoantien/auth/register', [
            'name' => 'Nguyen Van A',
            'email' => 'user_test@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('user.email', 'user_test@example.com');

        $this->assertDatabaseHas('users', [
            'email' => 'user_test@example.com',
            'name' => 'Nguyen Van A',
        ]);

        $user = User::where('email', 'user_test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertDatabaseHas('cashback_wallets', [
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_login_and_auto_merge_guest_wallet(): void
    {
        // 1. Create a guest wallet with orders and balance
        $guestSubId = 'mt_s_guest12345';
        $guestWallet = CashbackWallet::create([
            'sub_id' => $guestSubId,
            'pending_balance' => 30000.00,
            'available_balance' => 70000.00,
            'withdrawn_balance' => 0.00,
            'status' => 'active',
        ]);

        CashbackClick::create([
            'wallet_id' => $guestWallet->id,
            'sub_id' => $guestSubId,
            'original_url' => 'https://shopee.vn/product/123',
            'affiliate_url' => 'https://s.shopee.vn/mock123',
            'short_link' => 'https://s.shopee.vn/mock123',
        ]);

        // 2. Existing user
        $user = User::create([
            'name' => 'Tran Thi B',
            'email' => 'tranthib@example.com',
            'password' => Hash::make('mypassword123'),
        ]);

        // 3. Login with guest cookie attached
        $response = $this->withCredentials()
            ->withUnencryptedCookie('cashback_sub_id', $guestSubId)
            ->postJson('/hoantien/auth/login', [
                'email' => 'tranthib@example.com',
                'password' => 'mypassword123',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        // 4. Verify user wallet inherited guest balance
        $userWallet = CashbackWallet::where('user_id', $user->id)->first();
        $this->assertNotNull($userWallet);
        $this->assertEquals(70000.00, (float) $userWallet->available_balance);
        $this->assertEquals(30000.00, (float) $userWallet->pending_balance);

        // Verify click transferred
        $this->assertDatabaseHas('cashback_clicks', [
            'wallet_id' => $userWallet->id,
            'user_id' => $user->id,
        ]);

        // Guest wallet zeroed and merged
        $guestWallet->refresh();
        $this->assertEquals(0.00, (float) $guestWallet->available_balance);
        $this->assertEquals('merged', $guestWallet->status);
    }

    public function test_withdrawal_requires_password_for_authenticated_users(): void
    {
        $user = User::create([
            'name' => 'Le Van C',
            'email' => 'levanc@example.com',
            'password' => Hash::make('strongpass123'),
        ]);

        $wallet = CashbackWallet::create([
            'user_id' => $user->id,
            'sub_id' => 'mt_u' . $user->id . '_sub123',
            'available_balance' => 100000.00,
            'pending_balance' => 0.00,
            'withdrawn_balance' => 0.00,
            'status' => 'active',
        ]);

        // Test with incorrect password
        $failResponse = $this->actingAs($user)
            ->postJson('/hoantien/withdraw', [
                'amount' => 60000,
                'bank_name' => 'Techcombank',
                'bank_account_number' => '1903652881',
                'bank_account_name' => 'LE VAN C',
                'password' => 'wrongpassword',
            ]);

        $failResponse->assertStatus(422);
        $failResponse->assertJsonPath('success', false);

        // Wallet balance untouched
        $wallet->refresh();
        $this->assertEquals(100000.00, (float) $wallet->available_balance);

        // Test with correct password
        $successResponse = $this->actingAs($user)
            ->postJson('/hoantien/withdraw', [
                'amount' => 60000,
                'bank_name' => 'Techcombank',
                'bank_account_number' => '1903652881',
                'bank_account_name' => 'LE VAN C',
                'save_default_bank' => true,
                'password' => 'strongpass123',
            ]);

        $successResponse->assertStatus(200);
        $successResponse->assertJsonPath('success', true);

        // Balance deducted
        $wallet->refresh();
        $this->assertEquals(40000.00, (float) $wallet->available_balance);
        $this->assertEquals('Techcombank', $wallet->default_bank_name);
        $this->assertEquals('1903652881', $wallet->default_bank_account_number);
        $this->assertEquals('LE VAN C', $wallet->default_bank_account_name);

        $this->assertDatabaseHas('cashback_withdrawals', [
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'amount' => 60000.00,
            'status' => 'pending',
        ]);
    }

    public function test_user_can_save_default_bank_profile(): void
    {
        $user = User::create([
            'name' => 'Hoang D',
            'email' => 'hoangd@example.com',
            'password' => Hash::make('password123'),
        ]);

        $wallet = CashbackWallet::create([
            'user_id' => $user->id,
            'sub_id' => 'mt_u' . $user->id . '_sub456',
            'available_balance' => 0.00,
            'pending_balance' => 0.00,
            'withdrawn_balance' => 0.00,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)
            ->postJson('/hoantien/profile/bank', [
                'bank_name' => 'Vietcombank',
                'bank_account_number' => '0071001234567',
                'bank_account_name' => 'HOANG D',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $wallet->refresh();
        $this->assertEquals('Vietcombank', $wallet->default_bank_name);
        $this->assertEquals('0071001234567', $wallet->default_bank_account_number);
        $this->assertEquals('HOANG D', $wallet->default_bank_account_name);
    }
}

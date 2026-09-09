<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Cashback Wallets (Ví hoàn tiền của người dùng / session)
        Schema::create('cashback_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('session_id', 100)->nullable()->index();
            $table->string('sub_id', 64)->unique()->index();
            $table->decimal('pending_balance', 14, 2)->default(0.00);
            $table->decimal('available_balance', 14, 2)->default(0.00);
            $table->decimal('withdrawn_balance', 14, 2)->default(0.00);
            $table->string('status', 30)->default('active');
            $table->timestamps();
        });

        // 2. Cashback Clicks (Lịch sử tạo link tracking & click)
        Schema::create('cashback_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('cashback_wallets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sub_id', 64)->index();
            $table->text('original_url');
            $table->text('affiliate_url');
            $table->string('short_link', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        // 3. Cashback Orders (Đơn hàng ghi nhận từ Shopee Open Platform)
        Schema::create('cashback_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('cashback_wallets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('click_id')->nullable()->constrained('cashback_clicks')->nullOnDelete();
            $table->string('shopee_order_id', 100)->unique()->index();
            $table->string('sub_id', 64)->index();
            $table->string('product_name', 255);
            $table->string('product_image', 500)->nullable();
            $table->decimal('gmv', 14, 2)->default(0.00);
            $table->decimal('commission_shopee', 14, 2)->default(0.00);
            $table->decimal('cashback_rate', 5, 2)->default(0.80);
            $table->decimal('cashback_amount', 14, 2)->default(0.00);
            $table->string('status', 30)->default('pending')->index();
            $table->json('raw_data')->nullable();
            $table->timestamp('order_time')->nullable();
            $table->timestamps();
        });

        // 4. Cashback Withdrawals (Yêu cầu rút tiền)
        Schema::create('cashback_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('cashback_wallets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('bank_name', 100);
            $table->string('bank_account_number', 50);
            $table->string('bank_account_name', 100);
            $table->string('status', 30)->default('pending')->index();
            $table->text('note')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // 5. Cashback Ledger (Sổ cái giao dịch biến động số dư)
        Schema::create('cashback_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('cashback_wallets')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('cashback_orders')->nullOnDelete();
            $table->foreignId('withdrawal_id')->nullable()->constrained('cashback_withdrawals')->nullOnDelete();
            $table->string('type', 50)->index();
            $table->decimal('amount', 14, 2);
            $table->decimal('balance_before', 14, 2)->default(0.00);
            $table->decimal('balance_after', 14, 2)->default(0.00);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashback_ledgers');
        Schema::dropIfExists('cashback_withdrawals');
        Schema::dropIfExists('cashback_orders');
        Schema::dropIfExists('cashback_clicks');
        Schema::dropIfExists('cashback_wallets');
    }
};
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
        Schema::table('cashback_wallets', function (Blueprint $table) {
            $table->string('default_bank_name', 100)->nullable()->after('status');
            $table->string('default_bank_account_number', 50)->nullable()->after('default_bank_name');
            $table->string('default_bank_account_name', 100)->nullable()->after('default_bank_account_number');
        });

        Schema::table('cashback_withdrawals', function (Blueprint $table) {
            $table->string('bank_ref_code', 100)->nullable()->after('status');
            $table->text('admin_note')->nullable()->after('bank_ref_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashback_withdrawals', function (Blueprint $table) {
            $table->dropColumn(['bank_ref_code', 'admin_note']);
        });

        Schema::table('cashback_wallets', function (Blueprint $table) {
            $table->dropColumn([
                'default_bank_name',
                'default_bank_account_number',
                'default_bank_account_name',
            ]);
        });
    }
};

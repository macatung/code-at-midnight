<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashbackLedger extends Model
{
    use HasFactory;

    protected $table = 'cashback_ledgers';

    protected $fillable = [
        'wallet_id',
        'order_id',
        'withdrawal_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
    ];

    protected $casts = [
        'amount' => 'float',
        'balance_before' => 'float',
        'balance_after' => 'float',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(CashbackWallet::class, 'wallet_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(CashbackOrder::class, 'order_id');
    }

    public function withdrawal(): BelongsTo
    {
        return $this->belongsTo(CashbackWithdrawal::class, 'withdrawal_id');
    }
}

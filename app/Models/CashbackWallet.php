<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashbackWallet extends Model
{
    use HasFactory;

    protected $table = 'cashback_wallets';

    protected $fillable = [
        'user_id',
        'session_id',
        'sub_id',
        'pending_balance',
        'available_balance',
        'withdrawn_balance',
        'status',
    ];

    protected $casts = [
        'pending_balance' => 'float',
        'available_balance' => 'float',
        'withdrawn_balance' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(CashbackClick::class, 'wallet_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(CashbackOrder::class, 'wallet_id');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(CashbackWithdrawal::class, 'wallet_id');
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(CashbackLedger::class, 'wallet_id');
    }
}

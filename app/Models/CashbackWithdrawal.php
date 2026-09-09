<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashbackWithdrawal extends Model
{
    use HasFactory;

    protected $table = 'cashback_withdrawals';

    protected $fillable = [
        'wallet_id',
        'user_id',
        'amount',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'status',
        'bank_ref_code',
        'admin_note',
        'note',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'processed_at' => 'datetime',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(CashbackWallet::class, 'wallet_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(CashbackLedger::class, 'withdrawal_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashbackOrder extends Model
{
    use HasFactory;

    protected $table = 'cashback_orders';

    protected $fillable = [
        'wallet_id',
        'user_id',
        'click_id',
        'shopee_order_id',
        'sub_id',
        'product_name',
        'product_image',
        'gmv',
        'commission_shopee',
        'cashback_rate',
        'cashback_amount',
        'status',
        'raw_data',
        'order_time',
    ];

    protected $casts = [
        'gmv' => 'float',
        'commission_shopee' => 'float',
        'cashback_rate' => 'float',
        'cashback_amount' => 'float',
        'raw_data' => 'array',
        'order_time' => 'datetime',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(CashbackWallet::class, 'wallet_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function click(): BelongsTo
    {
        return $this->belongsTo(CashbackClick::class, 'click_id');
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(CashbackLedger::class, 'order_id');
    }
}

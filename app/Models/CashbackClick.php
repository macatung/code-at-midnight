<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashbackClick extends Model
{
    use HasFactory;

    protected $table = 'cashback_clicks';

    protected $fillable = [
        'wallet_id',
        'user_id',
        'sub_id',
        'original_url',
        'affiliate_url',
        'short_link',
        'ip_address',
        'user_agent',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(CashbackWallet::class, 'wallet_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(CashbackOrder::class, 'click_id');
    }
}

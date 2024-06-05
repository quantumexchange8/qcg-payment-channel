<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradingAccount extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'balance' => 'decimal:2',
        'credit' => 'decimal:2',
        'margin' => 'decimal:2',
        'margin_free' => 'decimal:2',
        'margin_level' => 'decimal:2',
        'profit' => 'decimal:2',
        'storage' => 'decimal:2',
        'commission' => 'decimal:2',
        'floating' => 'decimal:2',
        'equity' => 'decimal:2',
        'so_level' => 'decimal:2',
        'so_equity' => 'decimal:2',
        'so_margin' => 'decimal:2',
        'assets' => 'decimal:2',
        'liabilities' => 'decimal:2',
        'blocked_commission' => 'decimal:2',
        'blocked_profit' => 'decimal:2',
        'margin_initial' => 'decimal:2',
        'margin_maintenance' => 'decimal:2',
        'created_at' => 'datetime:Y-m-d',
    ];

    public function ofUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tradingUser(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TradingUser::class, 'meta_login', 'meta_login');
    }
}

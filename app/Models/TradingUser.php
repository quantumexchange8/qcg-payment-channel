<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradingUser extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'balance' => 'decimal:2',
        'credit' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'commission_daily' => 'decimal:2',
        'commission_montly' => 'decimal:2',
        'balance_prev_day' => 'decimal:2',
        'balance_prev_month' => 'decimal:2',
        'equity_prev_day' => 'decimal:2',
        'equity_prev_month' => 'decimal:2',
        'created_at' => 'datetime:Y-m-d',
    ];

    public function ofUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

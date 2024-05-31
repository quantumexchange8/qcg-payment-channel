<?php

namespace App\Services\Data;

use App\Models\TradingUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateTradingUser
{
    public function execute($meta_login, $data): TradingUser
    {
        return $this->updateTradingUser($meta_login, $data);
    }

    public function updateTradingUser($meta_login, $data): TradingUser
    {
        $tradingUser = TradingUser::query()->where('meta_login', $meta_login)->first();

        $tradingUser->meta_group = $data['groupName'];
        $tradingUser->leverage = $data['leverageInCents'] / 100;

        $tradingUser->registration = $data['registrationTimestamp'];
        $tradingUser->last_access = $data['lastUpdateTimestamp'];
        $tradingUser->balance = $data['balance'] / 100;
        $tradingUser->credit = $data['nonWithdrawableBonus'] / 100;

        DB::transaction(function () use ($tradingUser) {
            $tradingUser->save();
        });


        return $tradingUser;
    }
}

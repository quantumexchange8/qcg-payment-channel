<?php

namespace App\Services\Data;

use App\Models\AccountType;
use App\Models\TradingAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateTradingAccount
{
    public function execute($meta_login, $data): TradingAccount
    {
        return $this->updateTradingAccount($meta_login, $data);
    }

    public function updateTradingAccount($meta_login, $data): TradingAccount
    {
        $tradingAccount = TradingAccount::query()->where('meta_login', $meta_login)->first();
        $tradingUser = $tradingAccount->tradingUser;
        $accountType = AccountType::where('name', $tradingUser->meta_group)->first();

        $tradingAccount->currency_digits = $data['moneyDigits'];
        $tradingAccount->balance = $data['balance'] / 100;
        $tradingAccount->credit = $data['nonWithdrawableBonus'] / 100;
        $tradingAccount->margin_leverage = $data['leverageInCents'] / 100;
        $tradingAccount->equity = $data['equity'] / 100;
        $tradingAccount->account_type = $accountType->id;
        DB::transaction(function () use ($tradingAccount) {
            $tradingAccount->save();
        });

        return $tradingAccount;
    }
}

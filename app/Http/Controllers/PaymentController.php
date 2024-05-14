<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Http\Requests\InternalTransferRequest;
use App\Http\Requests\WithdrawalRequest;
use App\Models\PaymentAccount;
use App\Models\SettingWalletAddress;
use App\Models\TradingAccount;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(): Response
    {
        $user_id = auth()->user()->id;
        $trading_accounts = TradingAccount::where('user_id', $user_id)->get()->map(function ($trading_account) {
            return [
                'value' => $trading_account->meta_login,
                'label' => $trading_account->meta_login,
                'balance' => number_format($trading_account->balance, 2),
            ];
        });
        $wallet_addresses = SettingWalletAddress::all()->pluck('wallet_address')->shuffle();
        $payment_accounts = PaymentAccount::where('user_id', $user_id)->get()->map(function ($payment_account) {
            return [
                'value' => $payment_account->id,
                'label' => $payment_account->payment_account_name,
                'address' => $payment_account->account_no
            ];
        });

        if(count($trading_accounts) === 0) {
            $trading_accounts = [['value' => 'Unavailable', 'label' => 'No account', 'balance' => 'Unavailable']];
        }
        if(count($payment_accounts) === 0) {
            $payment_accounts = [['value' => 'Unavailable', 'label' => 'No account']];
        }

        return Inertia::render('Dashboard', [
            'tradingAccounts' => $trading_accounts,
            'walletAddresses' => $wallet_addresses,
            'paymentAccounts' => $payment_accounts,
        ]);
    }

    public function deposit(DepositRequest $request)
    {
        dd($request->all());
    }

    public function internalTransfer(InternalTransferRequest $request)
    {
        dd($request->all());
    }
    
    public function withdrawal(WithdrawalRequest $request)
    {
        dd($request->all());
    }
}

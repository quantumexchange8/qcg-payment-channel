<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Models\SettingWalletAddress;
use App\Models\TradingAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(): Response
    {
        // $user_id = auth()->user()->id;
        $user_id = 1188;
        $trading_accounts = TradingAccount::where('user_id', $user_id)->get()->map(function ($trading_account) {
            return [
                'value' => $trading_account->meta_login,
                'label' => $trading_account->meta_login,
                'balance' => number_format($trading_account->balance, 2),
            ];
        });
        $wallet_addresses = SettingWalletAddress::all()->pluck('wallet_address')->shuffle();

        return Inertia::render('Dashboard', [
            'tradingAccounts' => $trading_accounts,
            'walletAddresses' => $wallet_addresses,
        ]);
    }

    public function deposit(DepositRequest $request)
    {
        dd($request->all());

        echo $request->trading_account;
        echo "<br><br>";
        echo $request->deposit_amount;
        echo "<br><br>";
        echo $request->txid;
        echo "<br><br>";
        echo $request->receipt_file;
        die();
    }

    public function internalTransfer(Request $request)
    {
        dd($request);

        echo $request->transferMode;
        echo "<br><br>";
        echo $request->trading_account;
        echo "<br><br>";
        echo $request->amount;
        echo "<br><br>";
        die();
    }
    
    public function withdrawal(Request $request)
    {
        dd($request);

        echo $request->amount;
        echo "<br><br>";
        echo $request->usdtAddress;
        echo "<br><br>";
        die();
    }
}

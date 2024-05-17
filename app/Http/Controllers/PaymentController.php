<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Http\Requests\InternalTransferRequest;
use App\Http\Requests\WithdrawalRequest;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\SettingWalletAddress;
use App\Models\TradingAccount;
use App\Models\TradingUser;
use App\Services\ChangeTraderBalanceType;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\CTraderService;
use App\Services\RunningNumberService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index(): Response
    {
        $user_id = Auth::user()->id;
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

        return Inertia::render('Dashboard', [
            'tradingAccounts' => $trading_accounts,
            'walletAddresses' => $wallet_addresses,
            'paymentAccounts' => $payment_accounts,
        ]);
    }

    public function deposit(DepositRequest $request): \Illuminate\Http\RedirectResponse
    {
        $meta_login = $request->meta_login;
        $amount = number_format($request->deposit_amount, 2, '.', '');

        $payment_id = RunningNumberService::getID('transaction');
        $payment_charges = null;
        $real_amount = $amount;
        $user = Auth::user();

        $payment = Payment::create([
            'to' => $meta_login,
            'user_id' => $user->id,
            'category' => 'payment',
            'payment_id' => $payment_id,
            'type' => 'Deposit',
            'channel' => 'crypto',
            'TxID' => $request->txid,
            'comment' => 'Deposit',
            'amount' => $amount,
            'currency' => 'TRC20',
            'description' => null,
            'real_amount' => $real_amount,
            'payment_charges' => $payment_charges,
        ]);

        if ($request->hasFile('payment_receipt')) {
            $payment->addMedia($request->payment_receipt)->toMediaCollection('payment_receipt');
        }

        // Notification::route('mail', 'payment@currenttech.pro')
        //     ->notify(new DepositRequestNotification($payment, $user));

        return redirect()->route('success_page')->with([
            'title' => 'Success!',
            'description' => 'Your deposit is now being processed.',
        ]);
    }


    /**
     * ==============================
     *       Wallet to Account
     * ==============================
     */
    public function wallet_to_account(InternalTransferRequest $request): Response
    {
        return Inertia::render('SuccessPage', [
            'title' => 'Success!',
            'description' => 'Your internal transfer has been successfully processed.',
        ]);
        dd($request->all());

        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] != 0) {
            if ($conn['code'] == 10) {
                return response()->json(['success' => false, 'message' => 'No connection with cTrader Server']);
            }
            return response()->json(['success' => false, 'message' => $conn['message']]);
        }

        $user = Auth::user();
        $amount = floatval($request->amount);
        if ($user->cash_wallet < $amount) {
            throw ValidationException::withMessages(['amount' => trans('Insufficient balance')]);
        }

        dd($request->all());

        $payment_id = RunningNumberService::getID('transaction');
        try {
            $trade = (new CTraderService)->createTrade($request->to_meta_login, $request->amount, "Wallet To Account", ChangeTraderBalanceType::DEPOSIT);
        } catch (\Throwable $e) {
            if ($e->getMessage() == "Not found") {
                TradingUser::firstWhere('meta_login', $request->to_meta_login)->update(['acc_status' => 'Inactive']);
            } else {
                Log::error($e->getMessage());
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        $ticket = $trade->getTicket();
        Payment::create([
            'user_id' => $user->id,
            'payment_id' => $payment_id,
            'category' => 'internal_transfer',
            'type' => 'WalletToAccount',
            'to' => $request->to_meta_login,
            'amount' => $amount,
            'ticket' => $ticket,
            'status' => 'Successful',
        ]);

        $user->cash_wallet -= $amount;
        $user->save();

        return Inertia::render('SuccessPage', [
            'title' => 'Success!',
            'description' => 'Your internal transfer has been successfully processed.',
        ]);
    }

    /**
     * ==============================
     *       Account to Wallet
     * ==============================
     */
    public function account_to_wallet(InternalTransferRequest $request)
    {
        return Inertia::render('SuccessPage', [
            'title' => 'Success!',
            'description' => 'Your internal transfer has been successfully processed.',
        ]);
        dd($request->all());

        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] != 0) {
            if ($conn['code'] == 10) {
                return response()->json(['success' => false, 'message' => 'No connection with cTrader Server']);
            }
            return response()->json(['success' => false, 'message' => $conn['message']]);
        }

        $user = Auth::user();

        $tradingUser = TradingUser::firstWhere('meta_login', $request->from_meta_login);
        (new CTraderService)->getUserInfo([$tradingUser]);
        $tradingUser = TradingUser::firstWhere('meta_login', $request->from_meta_login);

        if ($tradingUser->balance < $request->amount) {
            throw ValidationException::withMessages(['amount' => trans('Insufficient balance')]);
        }

        dd($request->all());

        $payment_id = RunningNumberService::getID('transaction');
        try {
            $trade = (new CTraderService)->createTrade($request->from_meta_login, $request->amount, "Account To Wallet", ChangeTraderBalanceType::WITHDRAW);
        } catch (\Throwable $e) {
            if ($e->getMessage() == "Not found") {
                TradingUser::firstWhere('meta_login', $request->from_meta_login)->update(['acc_status' => 'Inactive']);
            } else {
                Log::error($e->getMessage());
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        $ticket = $trade->getTicket();

        Payment::create([
            'user_id' => $user->id,
            'payment_id' => $payment_id,
            'category' => 'internal_transfer',
            'type' => 'AccountToWallet',
            'from' => $request->from_meta_login,
            'amount' => $request->amount,
            'ticket' => $ticket,
            'status' => 'Successful',

        ]);
        $user->cash_wallet += $request->amount;
        $user->save();

        return Inertia::render('SuccessPage', [
            'title' => 'Success!',
            'description' => 'Your internal transfer has been successfully processed.',
        ]);
    }


    /**
     * ==============================
     *      Account to Account
     * ==============================
     */
    public function account_to_account(InternalTransferRequest $request)
    {
        return Inertia::render('SuccessPage', [
            'title' => 'Success!',
            'description' => 'Your internal transfer has been successfully processed.',
        ]);
        dd($request->all());

        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] != 0) {
            if ($conn['code'] == 10) {
                return response()->json(['success' => false, 'message' => 'No connection with cTrader Server']);
            }
            return response()->json(['success' => false, 'message' => $conn['message']]);
        }

        $user = Auth::user();
        $tradingUser = TradingUser::firstWhere('meta_login', $request->from_meta_login);
        (new CTraderService)->getUserInfo([$tradingUser]);
        $tradingUser = TradingUser::firstWhere('meta_login', $request->from_meta_login);

        if ($tradingUser->balance < $request->amount) {
            throw ValidationException::withMessages(['amount' => trans('Insufficient balance')]);
        }

        dd($request->all());

        $payment_id = RunningNumberService::getID('transaction');
        try {
            $trade_1 = (new CTraderService)->createTrade($request->from_meta_login, $request->amount, "Account To Account", ChangeTraderBalanceType::WITHDRAW);
        } catch (\Throwable $e) {
            if ($e->getMessage() == "Not found") {
                TradingUser::firstWhere('meta_login', $request->from_meta_login)->update(['acc_status' => 'Inactive']);
            } else {
                Log::error($e->getMessage());
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        try {
            $trade_2 = (new CTraderService)->createTrade($request->to_meta_login, $request->amount, "Account To Account", ChangeTraderBalanceType::DEPOSIT);
        } catch (\Throwable $e) {
            if ($e->getMessage() == "Not found") {
                TradingUser::firstWhere('meta_login', $request->to_meta_login)->update(['acc_status' => 'Inactive']);
            } else {
                Log::error($e->getMessage());
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        $ticket = $trade_1->getTicket() . ', ' . $trade_2->getTicket();

        Payment::create([
            'user_id' => $user->id,
            'payment_id' => $payment_id,
            'category' => 'internal_transfer',
            'type' => 'AccountToAccount',
            'from' => $request->from_meta_login,
            'to' => $request->to_meta_login,
            'amount' => $request->amount,
            'ticket' => $ticket,
            'status' => 'Successful',

        ]);

        return Inertia::render('SuccessPage', [
            'title' => 'Success!',
            'description' => 'Your internal transfer has been successfully processed.',
        ]);
    }

    public function withdrawal(WithdrawalRequest $request)
    {
        return Inertia::render('SuccessPage', [
            'title' => 'Request Sent Successful!',
            'description' => 'Your withdrawal request will be processed within 24 hours. Please wait patiently.',
        ]);
        dd($request->all());

        $user = Auth::user();
        $amount = floatval($request->amount);
        if ($user->cash_wallet < $amount) {
            throw ValidationException::withMessages(['amount' => trans('Insufficient balance')]);
        }

        dd($request->all());

        $user->cash_wallet -= $amount;
        $user->save();
        $payment_id = RunningNumberService::getID('transaction');

        $payment_account = PaymentAccount::query()
            ->where('account_no', $request->account_no)
            ->first();

        Payment::create([
            'user_id' => $user->id,
            'payment_id' => $payment_id,
            'category' => 'payment',
            'type' => 'Withdrawal',
            'channel' => $payment_account->payment_platform,
            'amount' => $amount,
            'account_no' => $request->account_no,
            'account_type' => $payment_account->payment_platform_name,
            'currency' => $payment_account->currency,
        ]);

        return Inertia::render('SuccessPage', [
            'title' => 'Request Sent Successful!',
            'description' => 'Your withdrawal request will be processed within 24 hours. Please wait patiently.',
        ]);
    }
}

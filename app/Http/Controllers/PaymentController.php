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
use App\Models\User;
use App\Notifications\DepositApprovalNotification;
use App\Services\ChangeTraderBalanceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\CTraderService;
use App\Services\RunningNumberService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index(): Response
    {
        $user_id = Auth::user()->id;

        if (App::environment('production')) {
            $tradingUsers = TradingUser::where('user_id', $user_id)->get();
            (new CTraderService)->getUserInfo($tradingUsers);
        }

        $trading_accounts = TradingAccount::where('user_id', $user_id)->get()->map(function ($trading_account) {
            return [
                'value' => $trading_account->meta_login,
                'label' => $trading_account->meta_login,
                'balance' => number_format($trading_account->balance, 2),
            ];
        });

        // $wallet_addresses = SettingWalletAddress::all()->pluck('wallet_address')->shuffle();
        $payment_accounts = PaymentAccount::where('user_id', $user_id)->get()->map(function ($payment_account) {
            return [
                'value' => $payment_account->id,
                'label' => $payment_account->payment_account_name,
                'address' => $payment_account->account_no
            ];
        });

        return Inertia::render('Dashboard', [
            'tradingAccounts' => $trading_accounts,
            'paymentAccounts' => $payment_accounts,
        ]);
    }

    public function deposit(DepositRequest $request)
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
            'real_amount' => $real_amount,
            'payment_charges' => $payment_charges,
        ]);

        $token = Str::random(40);

        $payoutSetting = config('payment-gateway');
        $domain = $_SERVER['HTTP_HOST'];

        if ($domain ===  'deposit.qcgexchange.com') {
            $selectedPayout = $payoutSetting['live'];
        } else {
            $selectedPayout = $payoutSetting['staging'];
        }

        $vCode = md5($selectedPayout['appId'] . $payment->payment_id . $selectedPayout['merchantId'] . $selectedPayout['ttKey']);

        $params = [
            'userName' => $user->first_name,
            'userEmail' => $user->email,
            'orderNumber' => $payment->payment_id,
            'userId' => $user->id,
            'merchantId' => $selectedPayout['merchantId'],
            'vCode' => $vCode,
            'token' => $token,
            'locale' => app()->getLocale(),
        ];

        // Send response
        $url = $selectedPayout['paymentUrl'] . '/payment';
        $redirectUrl = $url . "?" . http_build_query($params);

        return Inertia::location($redirectUrl);
    }

    //payment gateway return function
    public function depositReturn(Request $request)
    {
        $data = $request->all();

        Log::debug('deposit return ', $data);

        if ($data['response_status'] == 'success') {

            $result = [
                "amount" => $data['transfer_amount'],
                "payment_id" => $data['transaction_number'],
                "txid" => $data['txID'],
            ];

            $payment = Payment::query()
            ->where('payment_id', $result['payment_id'])
            ->first();

            $result['date'] = $payment->approval_date;

            return to_route('success_page')->with([
                'title' => trans('public.success'),
                'description' => trans('public.success_deposit'),
                'payment' => $result
            ]);
        } else {
            return to_route('dashboard');
        }
    }

    public function depositCallback(Request $request)
    {
        $data = $request->all();

        $result = [
            "token" => $data['vCode'],
            "from_wallet_address" => $data['from_wallet'],
            "to_wallet_address" => $data['to_wallet'],
            "txn_hash" => $data['txID'],
            "transactionID" => $data['transaction_number'],
            "amount" => $data['transfer_amount'],
            "status" => $data["status"],
            "remarks" => 'System Approval',
        ];

        $payment = Payment::query()
            ->where('payment_id', $result['transactionID'])
            ->first();

        $payoutSetting = config('payment-gateway');
        $domain = $_SERVER['HTTP_HOST'];

        if ($domain === 'deposit.qcgexchange.com') {
            $selectedPayout = $payoutSetting['live'];
        } else {
            $selectedPayout = $payoutSetting['staging'];
        }

        $dataToHash = md5($payment->payment_id . $selectedPayout['appId'] . $selectedPayout['merchantId']);
        $status = $result['status'] == 'success' ? 'Successful' : 'Rejected';

        if ($result['token'] === $dataToHash) {
            //proceed approval
            $payment->update([
                'TxID' => $result['txn_hash'],
                'amount' => $result['amount'],
                'real_amount' => $result['amount'],
                'status' => $status,
                'comment' => $result['remarks'],
                'approval_date' => date('Y-m-d')
            ]);

            Notification::route('mail', 'payment@currenttech.pro')
            ->notify(new DepositApprovalNotification($payment));

            if ($payment->status =='Successful') {
                if ($payment->type == 'Deposit') {
                    try {
                        $trade = (new CTraderService)->createTrade($payment->to, $payment->amount, "Deposit", ChangeTraderBalanceType::DEPOSIT);
                    } catch (\Throwable $e) {
                        if ($e->getMessage() == "Not found") {
                            TradingUser::firstWhere('meta_login', $payment->to)->update(['acc_status' => 'Inactive']);
                        } else {
                            Log::error($e->getMessage());
                        }
                        return response()->json(['success' => false, 'message' => $e->getMessage()]);
                    }
                    $ticket = $trade->getTicket();
                    $payment->ticket = $ticket;
                    $payment->save();

                    return response()->json(['success' => true, 'message' => 'Deposit Success']);

                }
            }
        }

        return response()->json(['success' => false, 'message' => 'Deposit Failed']);
    }

    /**
     * ==============================
     *       Wallet to Account
     * ==============================
     */
    public function wallet_to_account(InternalTransferRequest $request)
    {
        $user = Auth::user();
        $amount = floatval($request->amount);
        if ($user->cash_wallet < $amount) {
            throw ValidationException::withMessages(['amount' => trans('public.insufficient_balance')]);
        }

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

        return redirect()->route('success_page')->with([
            'title' => trans('public.success'),
            'description' => trans('public.success_internal_transfer'),
        ]);
    }

    /**
     * ==============================
     *       Account to Wallet
     * ==============================
     */
    public function account_to_wallet(InternalTransferRequest $request)
    {
        $user = Auth::user();

        $tradingUser = TradingUser::firstWhere('meta_login', $request->from_meta_login);
        (new CTraderService)->getUserInfo([$tradingUser]);
        $tradingUser = TradingUser::firstWhere('meta_login', $request->from_meta_login);

        if ($tradingUser->balance < $request->amount) {
            throw ValidationException::withMessages(['amount' => trans('public.insufficient_balance')]);
        }

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

        return redirect()->route('success_page')->with([
            'title' => trans('public.success'),
            'description' => trans('public.success_internal_transfer'),
        ]);
    }

    /**
     * ==============================
     *      Account to Account
     * ==============================
     */
    public function account_to_account(Request $request)
    {
        $user = Auth::user();
        $tradingUser = TradingUser::firstWhere('meta_login', $request->from_meta_login);
        (new CTraderService)->getUserInfo([$tradingUser]);
        $tradingUser = TradingUser::firstWhere('meta_login', $request->from_meta_login);

        if ($tradingUser->balance < $request->amount) {
            throw ValidationException::withMessages(['amount' => trans('public.insufficient_balance')]);
        }

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

        return redirect()->route('success_page')->with([
            'title' => trans('public.success'),
            'description' => trans('public.success_internal_transfer'),
        ]);
    }

    public function withdrawal(WithdrawalRequest $request)
    {
        $user = Auth::user();
        $amount = floatval($request->amount);
        if ($user->cash_wallet < $amount) {
            throw ValidationException::withMessages(['amount' => trans('public.insufficient_balance')]);
        }

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

        return redirect()->route('success_page')->with([
            'title' => trans('public.success_withdrawal_title'),
            'description' => trans('public.success_withdrawal'),
        ]);
    }
}

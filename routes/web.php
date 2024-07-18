<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('locale/{locale}', function ($locale) {
    App::setLocale($locale);
    Session::put("locale", $locale);

    return redirect()->back();
});

Route::post('deposit_callback', [PaymentController::class, 'depositCallback'])->name('depositCallback');

Route::middleware('auth')->group(function () {
    Route::get('deposit_return', [PaymentController::class, 'depositReturn']);

    Route::get('/dashboard', [PaymentController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/deposit', [PaymentController::class, 'deposit'])->name('dashboard.deposit');
    Route::post('/dashboard/walletToAccount', [PaymentController::class, 'wallet_to_account'])->name('dashboard.walletToAccount');
    Route::post('/dashboard/accountToWallet', [PaymentController::class, 'account_to_wallet'])->name('dashboard.accountToWallet');
    Route::post('/dashboard/accountToAccount', [PaymentController::class, 'account_to_account'])->name('dashboard.accountToAccount');
    Route::post('/dashboard/withdrawal', [PaymentController::class, 'withdrawal'])->name('dashboard.withdrawal');

    //success page
    Route::get('/success', function () {
        return Inertia::render('SuccessPage', [
            'title' => session('title'),
            'description' => session('description'),
            'payment' => session('payment'),
        ]);
    })->name('success_page');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

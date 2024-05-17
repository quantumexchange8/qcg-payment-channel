<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function() {
    Route::get('/dashboard', [PaymentController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/deposit', [PaymentController::class, 'deposit'])->name('dashboard.deposit');
    Route::post('/dashboard/walletToAccount', [PaymentController::class, 'wallet_to_account'])->name('dashboard.walletToAccount');
    Route::post('/dashboard/accountToWallet', [PaymentController::class, 'account_to_wallet'])->name('dashboard.accountToWallet');
    Route::post('/dashboard/accountToAccount', [PaymentController::class, 'account_to_account'])->name('dashboard.accountToAccount');
    Route::post('/dashboard/withdrawal', [PaymentController::class, 'withdrawal'])->name('dashboard.withdrawal');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

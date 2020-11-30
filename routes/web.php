<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use Carbon\Carbon;

Route::get('/', function () {
    // return redirect()->route('login');

    dd(Carbon::createFromFormat('m \/ y', '12 / 20'));
});

Auth::routes();

Route::middleware('auth')->group(function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::prefix('/payments')->group(function() {
        Route::resource('/e-wallet-payment', Controllers\Payment\EWalletController::class);
        Route::get('/e-wallet-payment/{eWalletPayment}/success', [Controllers\Payment\EWalletCallbackController::class, 'success'])->name('e-wallet-payment.callback_success');
        Route::get('/e-wallet-payment/{eWalletPayment}/failed', [Controllers\Payment\EWalletCallbackController::class, 'failed'])->name('e-wallet-payment.callback_failed');

        Route::get('/card-payment/{cardPayment}/security-check', [Controllers\Payment\CardController::class, 'securityCheck'])->name('card-payment.security_check');
        Route::resource('/card-payment', Controllers\Payment\CardController::class);
    });
});

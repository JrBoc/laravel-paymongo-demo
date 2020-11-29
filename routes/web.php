<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::middleware('auth')->group(function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('/ewallet-payments', App\Http\Controllers\EWalletPaymentController::class)->except('show', 'update', 'destroy');
    Route::resource('/card-payments', App\Http\Controllers\CardPaymentController::class);

    Route::resource('/payments/ewallet', Controllers\Payment\EWalletController::class);
    Route::get('/payments/ewallet-callback/success/{eWalletPayment}', [Controllers\Payment\EWalletCallbackController::class, 'success'])->name('ewallet.callback_success');
    Route::get('/payments/ewallet-callback/failed/{eWalletPayment}', [Controllers\Payment\EWalletCallbackController::class, 'failed'])->name('ewallet.callback_failed');
});

Route::get('/ewallet-payments-callback/success', [App\Http\Controllers\EWalletPaymentCallbackController::class, 'success'])->name('ewallet_payments_callback.success');
Route::get('/ewallet-payments-callback/failed', [App\Http\Controllers\EWalletPaymentCallbackController::class, 'failed'])->name('ewallet_payments_callback.failed');

Route::view('testing', 'pages.ewallet.create');

<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::middleware('auth')->group(function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('/ewallet-payments', App\Http\Controllers\EWalletPaymentController::class)->except('show', 'update', 'destroy');
    Route::resource('/card-payments', App\Http\Controllers\CardPaymentController::class);
});

Route::get('/ewallet-payments-callback/success', [App\Http\Controllers\EWalletPaymentCallbackController::class, 'success'])->name('ewallet_payments_callback.success');
Route::get('/ewallet-payments-callback/failed', [App\Http\Controllers\EWalletPaymentCallbackController::class, 'failed'])->name('ewallet_payments_callback.failed');

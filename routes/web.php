<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::middleware('auth')->group(function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('/payments', App\Http\Controllers\PaymentController::class);
});

Route::get('/payments-callback/success', [App\Http\Controllers\PaymentCallbackController::class, 'success'])->name('payments_callback.success');
Route::get('/payments-callback/failed', [App\Http\Controllers\PaymentCallbackController::class, 'failed'])->name('payments_callback.failed');

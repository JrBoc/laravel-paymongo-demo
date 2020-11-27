<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CardPaymentController extends Controller
{
    public function index()
    {
        return view('card_payments.index', [
            'payments' => current_user()->cardPayments()->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('card_payments.create');
    }
}

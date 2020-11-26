<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'payments' => current_user()->payments()->latest()->paginate(10),
        ]);
    }
}

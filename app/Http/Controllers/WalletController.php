<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $wallet = Auth::user()->wallet;
        $transactions = $wallet->transactions()->latest()->get();

        return view('wallet.index', compact('wallet', 'transactions'));
    }
}


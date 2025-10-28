<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
    
        $wallet = $user->wallet;
        $company = $user->company;
        $aiProfile = $company?->aiProfile;

        return view('dashboard', compact('wallet','company' ));
    }

}

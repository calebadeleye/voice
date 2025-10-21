<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wallet = $user->wallet()->firstOrCreate([
            'holder_type' => get_class($user),
            'holder_id' => $user->id,
        ]);

        $company = $user->company;
        $aiProfile = $company?->aiProfile;
        $companyFiles = $company?->files ?? [];

        return view('dashboard', compact('wallet', 'company', 'aiProfile', 'companyFiles'));
    }

}

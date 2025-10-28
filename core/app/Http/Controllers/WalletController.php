<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $wallet = Auth::user()->wallet;
        $transactions = $wallet->transactions()->latest()->paginate(10);

        return view('wallet.index', compact('wallet', 'transactions'));
    }

    public function fund()
    {
        return view('wallet.fund');
    }

    public function flutterwavePay(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        $user = Auth::user();
        $tx_ref = 'NAI_' . Str::random(10);

        // Prepare payload for Flutterwave API
        $payload = [
            'tx_ref' => $tx_ref,
            'amount' => $request->amount + 100,
            'currency' => 'NGN',
            'redirect_url' => route('flutterwave.callback'),
            'customer' => [
                'email' => $user->email,
                'name'  => $user->name,
            ],
            'customizations' => [
                'title' => 'Fund Wallet',
                'description' => 'Wallet funding on ' . config('app.name'),
            ],
            'subaccounts' => [
                [
                    'id' => env('FLW_SUBACCOUNT_ID'),
                ],
            ],
        ];

        // Send request to Flutterwave
        $response = Http::withToken(env('FLW_SECRET_KEY'))
            ->post(env('FLW_BASE_URL') . '/payments', $payload)
            ->json();

        if (isset($response['status']) && $response['status'] === 'success') {
            // Redirect user to payment page
            return redirect($response['data']['link']);
        }

        return back()->with('error', 'Unable to initialize payment. Please try again.');
    }


    public function flutterwaveCallback(Request $request)
    {
        $status = $request->status;
        $transaction_id = $request->transaction_id;

        // Verify payment
        $verifyResponse = Http::withToken(env('FLW_SECRET_KEY'))
            ->get(env('FLW_BASE_URL') . "/transactions/{$transaction_id}/verify")
            ->json();

        if (isset($verifyResponse['status']) && $verifyResponse['status'] === 'success') {
            $data = $verifyResponse['data'];

                // Credit user wallet
                $user = Auth::user();
                $wallet = $user->wallet;

                $wallet->deposit($data['amount'] - 100, [
                    'description' => 'Wallet funding via Flutterwave',
                    'reference' => $data['tx_ref'],
                ]);

            return redirect()->route('wallet.index')->with('success', 'Wallet funded successfully!');
        }

        return redirect()->route('wallet.index')->with('error', 'Payment verification failed.');
    }

}


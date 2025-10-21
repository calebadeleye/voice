<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    
    // Fund wallet page
    public function fundWallet()
    {
        return view('wallet.fund');
    }

    // Redirect to Flutterwave payment page
    public function initialize(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        $user = Auth::user();

        $data = [
            'payment_options' => 'card,banktransfer,ussd',
            'amount' => $request->amount,
            'email' => $user->email,
            'tx_ref' => Flutterwave::generateReference(),
            'currency' => 'NGN',
            'redirect_url' => route('flutterwave.callback'),
            'customer' => [
                'email' => $user->email,
                'name' => $user->name,
            ],
            'meta' => [
                'user_id' => $user->id,
            ],
        ];

        $payment = Flutterwave::initializePayment($data);

        if ($payment['status'] !== 'success') {
            return redirect()->back()->with('error', 'Unable to initiate payment.');
        }

        return redirect($payment['data']['link']);
    }

    // Handle payment callback
    public function callback()
    {
        $status = request()->status;

        if ($status === 'successful') {
            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $data = Flutterwave::verifyTransaction($transactionID);

            $amount = $data['data']['amount'];
            $user_id = $data['data']['meta']['user_id'];

            $user = \App\Models\User::find($user_id);

            if ($user) {
                $user->deposit($amount, ['tx_ref' => $data['data']['tx_ref']]);
            }

            return redirect()->route('wallet.index')->with('success', 'Wallet funded successfully!');
        }

        elseif ($status === 'cancelled') {
            return redirect()->route('wallet.index')->with('info', 'Payment cancelled.');
        }

        else {
            return redirect()->route('wallet.index')->with('error', 'Payment failed or invalid.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\CallRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use AfricasTalking\SDK\AfricasTalking;


class VoiceController extends Controller
{
    protected $atClient;
    protected $voiceService;

    public function __construct()
    {
        $username = config('services.africastalking.username');
        $apiKey = config('services.africastalking.api_key');
        $this->atClient = new AfricasTalking($username, $apiKey);
        $this->voiceService = $this->atClient->voice();
    }

    /**
     * Handle incoming voice call events from Africa’s Talking.
     */
    public function callback(Request $request)
    {

        // Generate internal session ID and store
        $internalSessionId = uniqid('session_', true);
        Cache::put("call_session", $internalSessionId, now()->addMinute());

        // Log the incoming request for debugging
        //Log::info("Voice callback received", $request->all());

        // Africa's Talking sends these parameters in POST
        $callerNumber      = $request->input('callerNumber'); // the caller
        $isActive          = $request->input('isActive');     // call status
        $africaNumber = $request->input('destinationNumber');     // call status
        $company = Company::where('africastalking_number',$africaNumber)->first();

        // The number you want the caller to be connected to
        $vapiNumber = $company->vapi_number; // <-- change to your target number
        $verifiedCallerId  = $company->africastalking_number; // <-- must be a number verified on AT

        // Make sure the call is active
        if ($isActive === 'true' || $isActive == 1) {

            // Build the XML response
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<Response>';
            $xml .= '<Say voice="woman" language="en-GB">Please hold while we connect you.</Say>';

            // Dial the destination number
            $xml .= '<Dial record="true" sequential="true" ';
            $xml .= 'phoneNumbers="' . $vapiNumber . '" ';
            $xml .= 'callerId="' . $verifiedCallerId . '">';
            $xml .= '</Dial>';

            $xml .= '</Response>';

            // Return the XML response
            return response($xml, 200)
                ->header('Content-Type', 'application/xml');
        }

         // === CALL ENDED ===
          if ($isActive === 'false' || $isActive === '0' || $isActive === 0 || $isActive === false){
            $this->handleCallEnd($request);
            // Return 200 OK to AT immediately after handling call end
            return response('Call ended processed successfully', 200);
        }

        // Optional: handle inactive calls or errors
        Log::warning("Call not active or failed", $request->all());
        return response('Unhandled call state', 200);
    }

     /**
     * Handle call end: get AT cost, Vapi cost (USD), convert to Naira, update wallet.
     */
    protected function handleCallEnd(Request $request)
    {

        $callerNumber  = $request->input('callerNumber');
        $calledNumber  = $request->input('destinationNumber');
        $duration      = $request->input('durationInSeconds');
        $atAmount      = $request->input('amount'); // e.g. "55.57850448"
        $sessionId = Cache::get("call_session");

        try {
            // === 1. Clean up Africa's Talking cost ===
            $atCost = floatval(preg_replace('/[^0-9.]/', '', $atAmount));

            // === 2. Get company using AT number ===
            $company = Company::where('africastalking_number', $calledNumber)->first();

            if (!$company) {
                Log::warning("No company found for AT number: $calledNumber");
                return;
            }

            // === 3. Get the associated user (wallet holder)
            $user = $company->user; // make sure Company has user() relationship
            if (!$user) {
                Log::error("No user found for company id={$company->id}");
                return;
            }

            // === 5. Deduct cost from company's wallet ===
            if ($atCost > 0) {
                $user->withdraw($atCost);
            }

            // === 6. Log call record ===
            CallRecord::create([
                'user_id'    => $user->id,
                'caller'     => $callerNumber,
                'session_id' => $sessionId,
                'duration'   => $duration,
                'at_cost'    => $atCost,
                'vapi_cost'  => 0,
                'total_cost' => 0,
                'status'     => 'active',
                'recording_url'=> $request->input('recordingUrl'),
            ]);

        } catch (\Exception $e) {
            Log::error("Error handling call end: " . $e->getMessage());
        }
    }

       /**
     * Fetch call details from Vapi and update wallet & call record.
     */
    public function handleWebhook(Request $request)
    {
        sleep(5);
       Log::info('Vapi Webhook Received', $request->all());

        $data = $request->input('message');
        $sessionId = Cache::get("call_session");

        $cost = $data['cost'] * env('USD_TO_NGN_RATE');
        $transcript = $data['transcript'] ?? null;

        $callRecord = CallRecord::where('session_id', $sessionId)->first();

        // Deduct cost from wallet
        if ($cost > 0) {
             // Deduct from wallet
            $user = User::where('id',$callRecord->user_id)->first();

            $user->withdraw($cost);
        }

        $callRecord = CallRecord::where('session_id', $sessionId)->first();

        if ($callRecord) {
            $callRecord->update([
                'vapi_cost'    => $cost,
                'total_cost' => $cost + $callRecord->at_cost,
                'status'     => 'completed',
            ]);

            // Clean up memory
            Cache::forget("call_session");
        }

        return response()->json(['message' => 'Webhook handled'], 200);
    }

}

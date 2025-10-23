<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\CallRecord;
use Illuminate\Http\Request;
use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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
        // Log the incoming request for debugging
        Log::info("Voice callback received", $request->all());

        // Africa's Talking sends these parameters in POST
        $callerNumber     = $request->input('callerNumber'); // the caller
        $sessionId        = $request->input('sessionId');    // session ID
        $isActive         = $request->input('isActive');     // call status

        // The number you want the caller to be connected to
        $destinationNumber = '+12343079240'; // <-- change to your target number
        $verifiedCallerId  = '+2342017001182'; // <-- must be a number verified on AT

        // Make sure the call is active
        if ($isActive === 'true' || $isActive == 1) {

            // Build the XML response
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<Response>';
            $xml .= '<Say voice="woman" language="en-GB">Please hold while we connect you.</Say>';

            // Dial the destination number
            $xml .= '<Dial record="true" sequential="true" ';
            $xml .= 'phoneNumbers="' . $destinationNumber . '" ';
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
        $sessionId     = $request->input('sessionId');
        $callerNumber  = $request->input('callerNumber');
        $calledNumber  = $request->input('destinationNumber');
        $duration      = $request->input('durationInSeconds');
        $atAmount      = $request->input('amount'); // e.g. "55.57850448"

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

            // === 4. Total combined cost in Naira ===
            $totalCost = $atCost ;

            // === 5. Deduct cost from company's wallet ===
            if ($totalCost > 0) {
                $user->withdraw($totalCost, [
                    'description' => 'Voice call charge (AT + Vapi)',
                    'session_id'  => $sessionId,
                    'duration'    => $duration,
                    'caller'      => $callerNumber,
                ]);
            }

            // === 6. Log call record ===
            CallRecord::create([
                'user_id'    => $user->id,
                'caller'     => $callerNumber,
                'session_id' => $sessionId,
                'duration'   => $duration,
                'at_cost'    => $atCost,
                'vapi_cost'  => 0,
                'total_cost' => $totalCost,
                'status'     => 'completed',
                'recording_url'=> $request->input('recordingUrl'),
            ]);

            $this->updateVapiCallDetails($user->id, $sessionId);


            Log::info("Call ended: Duration {$duration}s | Total ₦{$totalCost} | Company {$company->name}");

        } catch (\Exception $e) {
            Log::error("Error handling call end: " . $e->getMessage());
        }
    }

       /**
     * Fetch call details from Vapi and update wallet & call record.
     */
    private function handleWebhook(Request $request)
    {
        Log::info("Vapi callback received: " . json_encode($request->all()));
        return response()->json(['status' => 'ok']);
    }

}

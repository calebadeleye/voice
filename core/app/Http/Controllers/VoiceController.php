<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Company;

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

        // Optional: handle inactive calls or errors
        Log::warning("Call not active or failed", $request->all());
        return response('Call not active', 200);
    }


    /**
     * Handle AI interaction once user input is available.
     */
    public function respondToAi(Request $request)
    {
        $destinationNumber = $request->input('destinationNumber');
        $speechText = $request->input('speechText'); // Africa’s Talking will include speech result if you used <record> action

        $company = $this->identifyCompany($destinationNumber);

        if (!$company) {
            return $this->sayResponse("Sorry, I could not find your company profile.");
        }

        $user = $company->user;

        // Wallet check before AI call
        $cost = config('app.ai_message_cost', 10);
        if ($user->balance < $cost) {
            return $this->sayResponse("You have insufficient balance. Please top up your wallet.");
        }

        // Deduct charge
        $user->withdraw($cost, ['reason' => 'AI Call Interaction']);

        // Get AI reply (stubbed function)
        $aiReply = $this->getAiResponse($company, $speechText);

        return $this->sayResponse($aiReply);
    }

    /**
     * Africa’s Talking event logs
     */
    public function event(Request $request)
    {
        Log::info("Voice event received", $request->all());
        return response('OK', 200);
    }

    /**
     * Helper to identify the company from caller’s number.
     */
    private function identifyCompany($destinationNumber)
    {
        return Company::where('africastalking_number', $destinationNumber)->first();
    }

    /**
     * Stub AI function (replace with your GPT or internal AI logic)
     */
    private function getAiResponse(Company $company, string $query): string
    {
        // Example: call your AI API
        try {
            $response = Http::post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => "You are the AI assistant for {$company->name}."],
                    ['role' => 'user', 'content' => $query],
                ],
            ])->json();

            return $response['choices'][0]['message']['content'] ?? "Sorry, I couldn't process that.";
        } catch (\Exception $e) {
            Log::error('AI request failed', ['error' => $e->getMessage()]);
            return "There was an error processing your request.";
        }
    }

    /**
     * Builds JSON response to speak text using Google Chirp 3 HD
     */
    private function sayResponse(string $text)
    {
        return response()->json([
            'actions' => [
                [
                    'action' => 'say',
                    'text' => $text,
                    'voice' => 'en-US-Chirp3-HD-F'
                ]
            ]
        ]);
    }
}

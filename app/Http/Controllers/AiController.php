<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    /**
     * Handle user chat input and send it to the AI API.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userMessage = $request->input('message');

        try {
            // Example: Sending to OpenAI (or any AI API)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.key'),
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Failed to connect to AI service',
                    'details' => $response->body(),
                ], 500);
            }

            $aiResponse = $response->json()['choices'][0]['message']['content'] ?? 'No response from AI.';

            // Save chat to database (optional)
            // AIChat::create([
            //     'user_id' => auth()->id(),
            //     'message' => $userMessage,
            //     'response' => $aiResponse,
            // ]);

            return response()->json([
                'message' => $userMessage,
                'response' => $aiResponse,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while processing your request.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show previous chat history for the logged-in user.
     */
    public function history()
    {
        // If you store chat messages, uncomment this:
        // $chats = AIChat::where('user_id', auth()->id())->latest()->get();
        // return response()->json($chats);

        return response()->json([
            'message' => 'Chat history feature not yet implemented.'
        ]);
    }

    /**
     * Clear chat history for the logged-in user.
     */
    public function clearHistory()
    {
        // Uncomment if using database
        // AIChat::where('user_id', auth()->id())->delete();

        return response()->json([
            'message' => 'Chat history cleared successfully (placeholder).'
        ]);
    }

     public function sendReply(Request $request, AiBillingService $billingService)
    {
        $user = Auth::user();

        // Check wallet before generating AI response
        if ($user->balance < env('AI_MESSAGE_COST', 5)) {
            return response()->json([
                'error' => 'Insufficient wallet balance. Please fund your wallet to continue using the AI assistant.'
            ], 402);
        }

        // Deduct wallet balance
        $charged = $billingService->chargeForAiResponse($user);

        if (! $charged) {
            return response()->json([
                'error' => 'Failed to charge wallet. Try again later.'
            ], 500);
        }

        // Continue with AI reply logic
        $reply = $this->generateAiResponse($request->input('message'), $user);

        return response()->json([
            'reply' => $reply,
            'remaining_balance' => $user->balance,
        ]);
    }

}

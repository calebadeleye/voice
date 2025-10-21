<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class AiBillingService
{
    /**
     * Deduct cost per AI response.
     *
     * @param User $user
     * @return bool
     */
    public function chargeForAiResponse(User $user): bool
    {
        $cost = config('app.ai_message_cost', env('AI_MESSAGE_COST', 5));

        try {
            if ($user->balance < $cost) {
                return false; // insufficient funds
            }

            $user->withdraw($cost, ['reason' => 'AI Response Charge']);
            return true;

        } catch (\Throwable $th) {
            Log::error('AI Billing Error: ' . $th->getMessage());
            return false;
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Company;
use Illuminate\Support\Facades\Log;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch the company by Africastalking number
        $company = Company::where('africastalking_number', '+2342017001182')->first();

        if (!$company) {
            $this->command->error('Company with the specified Africastalking number not found.');
            return;
        }

        // Get the related user
        $user = $company->user;
        if (!$user) {
            $this->command->error("No user found for company ID {$company->id}");
            return;
        }

        // Ensure wallet exists (Bavix will auto-create if not)
        $wallet = $user->wallet; // triggers firstOrCreate internally

        // Log before deposit
        Log::info('Wallet before deposit', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id ?? 'none',
            'balance' => $wallet->balanceFloat ?? 0,
        ]);

        // Deposit ₦1000 (this automatically updates balance)
        $user->deposit(1000, [
            'description' => 'Initial funding for voice call billing',
        ]);

        $this->command->info("Wallet created/funded successfully for user ID {$user->id}.");
        Log::info("Wallet seeded for user ID {$user->id}, new balance = ₦" . $user->balanceFloat);
    }
}

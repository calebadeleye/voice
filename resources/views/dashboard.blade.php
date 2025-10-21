<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-100 py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome Banner -->
            <div class="bg-white/80 backdrop-blur-md shadow-sm sm:rounded-lg p-6 border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">
                    Welcome back, {{ Auth::user()->name }} 👋
                </h2>
                <p class="text-gray-600">Manage your AI Customer Assistant below.</p>
            </div>

            <!-- Company Settings -->
         <div class="bg-white/80 backdrop-blur-md p-4 rounded-xl shadow border border-gray-100">
            <h3 class="font-semibold mb-2 text-gray-800">Your Company Settings</h3>

            @if(auth()->user()->company)
                <a href="{{ route('company.edit') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                    Manage Company
                </a>
            @else
                <a href="{{ route('company.create') }}" 
                   class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition">
                    Create Company
                </a>
            @endif
        </div>


            <!-- Dashboard Summary Row -->
            <div class="flex flex-wrap gap-6 justify-between">
                <!-- Wallet -->
                <div class="flex-1 min-w-[250px] bg-green-600 text-white p-6 rounded-2xl shadow-md transition hover:scale-[1.02] hover:shadow-lg">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold">Wallet Balance</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2v-5zM5 7h14M16 12h2" />
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">
                        ₦{{ number_format($wallet->balance ?? 0, 2) }}
                    </p>
                    <a href="{{ route('wallet.index') }}" 
                        class="mt-4 inline-block px-4 py-2 bg-white text-green-700 font-semibold rounded-lg hover:bg-green-100 transition">
                        View Wallet
                    </a>
                </div>

                <!-- Virtual Number -->
                <div class="flex-1 min-w-[250px] bg-indigo-600 text-white p-6 rounded-2xl shadow-md transition hover:scale-[1.02] hover:shadow-lg">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold">Virtual Phone Number</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 8.5A2.5 2.5 0 014.5 6h15A2.5 2.5 0 0122 8.5v7a2.5 2.5 0 01-2.5 2.5h-15A2.5 2.5 0 012 15.5v-7zM16 10h.01M12 10h.01M8 10h.01" />
                        </svg>
                    </div>
                    <p class="text-2xl font-bold">
                        {{ $company->virtual_number ?? 'Not Assigned' }}
                    </p>
                    <a href="#" class="mt-4 inline-block px-4 py-2 bg-white text-indigo-700 font-semibold rounded-lg hover:bg-indigo-100 transition">
                        Manage Number
                    </a>
                </div>

               <!-- AI Assistant -->
                <div class="flex-1 min-w-[250px] bg-blue-600 text-white p-6 rounded-2xl shadow-md transition hover:scale-[1.02] hover:shadow-lg">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold">Your AI Assistant</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 11.25c1.242 0 2.25-1.008 2.25-2.25S13.242 6.75 12 6.75 9.75 7.758 9.75 9s1.008 2.25 2.25 2.25zM8.25 15a4.5 4.5 0 017.5 0M12 22.5C6.201 22.5 1.5 17.799 1.5 12S6.201 1.5 12 1.5 22.5 6.201 22.5 12 17.799 22.5 12 22.5z" />
                        </svg>
                    </div>
                    <p class="text-xl font-medium">
                        {{ $aiProfile->ai_name ?? 'Not Set' }}
                    </p>
                    <a href="#" 
                        class="mt-4 inline-block px-4 py-2 bg-white text-blue-700 font-semibold rounded-lg hover:bg-blue-100 transition">
                        Customize AI
                    </a>
                </div>

         </div> <!-- end flex summary row -->

        </div> <!-- end max-w container -->
    </div> <!-- end bg gradient -->
</x-app-layout>


<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Fund Wallet') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Fund Your Wallet</h2>

                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @elseif(session('error'))
                    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Fund Wallet Form --}}
                <form method="POST" action="{{ route('flutterwave.pay') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">
                            Amount (₦)
                        </label>
                        <input type="number" name="amount" id="amount" min="100" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Proceed to Flutterwave
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

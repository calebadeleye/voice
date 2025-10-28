<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('My Wallet') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Wallet Summary --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Wallet Balance</h3>
                        <p class="mt-1 text-3xl font-bold text-green-600">
                            ₦{{ number_format($wallet->balance, 2) }}
                        </p>
                    </div>

                    {{-- Fund Wallet Button --}}
                    <div>
                        <a href="{{ route('wallet.fund') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Fund Wallet
                        </a>
                    </div>
                </div>
            </div>

            {{-- Transactions Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Transactions</h3>

                @if($transactions->isEmpty())
                    <p class="text-gray-500">No transactions found.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="capitalize">{{ $transaction->type }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $transaction->amount > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            ₦{{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $transaction->meta['description'] ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->created_at->format('d M Y, h:i A') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

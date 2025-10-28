<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Virtual Numbers') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="mb-4 p-3 rounded bg-green-100 text-green-700">
                        {{ session('success') }}
                    </div>
                @elseif (session('info'))
                    <div class="mb-4 p-3 rounded bg-blue-100 text-blue-700">
                        {{ session('info') }}
                    </div>
                @elseif (session('error'))
                    <div class="mb-4 p-3 rounded bg-red-100 text-red-700">
                        {{ session('error') }}
                    </div>
                @endif


                <h2 class="text-xl font-semibold mb-4">Virtual Phone Numbers</h2>

                <p class="text-gray-600 mb-6 text-sm leading-relaxed">
                    Virtual numbers are assigned automatically by the provider once purchased.  
                    They cannot be changed or edited manually.
                </p>

                {{-- Display assigned numbers --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Local Number</label>
                    <input type="text"
                           value="{{ $company->africastalking_number ?? 'Not assigned yet' }}"
                           readonly
                           class="w-full border-gray-300 rounded-md shadow-sm mt-1 bg-gray-100 cursor-not-allowed">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">International Number</label>
                    <input type="text"
                           value="{{ $company->vapi_number ?? 'Not assigned yet' }}"
                           readonly
                           class="w-full border-gray-300 rounded-md shadow-sm mt-1 bg-gray-100 cursor-not-allowed">
                </div>

                {{-- Request buttons --}}
                <div class="flex items-center gap-4">
                    <form method="POST" action="{{ route('company.virtual-numbers.update') }}">
                        @csrf
                        <input type="hidden" name="type" value="local">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Request Local Number (₦6,500 + VAT)
                        </button>
                    </form>

                    <form method="POST" action="{{ route('company.virtual-numbers.update') }}">
                        @csrf
                        <input type="hidden" name="type" value="international">
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Request International Number (₦3,000 + VAT)
                        </button>
                    </form>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mt-6">
                    <p class="text-sm text-blue-700">
                        <strong>Note:</strong> If you want customers to call your <strong>local number</strong>,
                        you’ll also need to purchase an <strong>international number</strong> for proper routing.
                    </p>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

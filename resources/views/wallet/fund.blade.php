@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">Fund Your Wallet</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('flutterwave.pay') }}">
        @csrf
        <div class="mb-4">
            <label for="amount" class="block text-sm font-medium">Amount (₦)</label>
            <input type="number" name="amount" id="amount" min="100" required
                   class="w-full mt-1 border-gray-300 rounded-lg shadow-sm">
        </div>

        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
            Proceed to Flutterwave
        </button>
    </form>
</div>
@endsection

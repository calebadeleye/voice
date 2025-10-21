
<x-app-layout>
<div class="max-w-3xl mx-auto bg-white p-6 shadow rounded-lg">
    <h2 class="text-2xl font-semibold mb-4">Company Settings</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('company.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('POST')

        <div>
            <label class="block font-medium">Company Name</label>
            <input type="text" name="name" class="w-full border p-2 rounded">
        </div>

        <div>
            <label class="block font-medium">Industry</label>
            <input type="text" name="industry" class="w-full border p-2 rounded">
        </div>

        <div>
            <label class="block font-medium">Description</label>
            <textarea name="description" class="w-full border p-2 rounded"></textarea>
        </div>

        <div>
            <label class="block font-medium">Website</label>
            <input type="url" name="website" class="w-full border p-2 rounded">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">Phone</label>
                <input type="text" name="phone" class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block font-medium">Email</label>
                <input type="email" name="email" class="w-full border p-2 rounded">
            </div>
        </div>

        <hr class="my-4">

       
        <div class="mb-3">
            <label>AI Assistant Name</label>
            <input type="text" name="ai_name" class="w-full border p-2 rounded">
        </div>

        <div class="mb-3">
            <label>Africa’s Talking Number</label>
            <input type="text" name="africastalking_number" class="w-full border p-2 rounded">
        </div>

        <div class="mb-3">
            <label>OpenAI API Key</label>
            <input type="text" name="openai_api_key" class="w-full border p-2 rounded">
        </div>

        <div class="mb-3">
            <label>Assistant Description / Prompt</label>
            <textarea name="assistant_description" class="w-full border p-2 rounded" rows="3"></textarea>
        </div>

        <div>
                <label class="block text-gray-600 font-medium mb-2">Company Logo</label>
                <div class="flex items-center space-x-4">
                    @if(optional(auth()->user()->company)->logo)
                        <img src="{{ asset('storage/' . auth()->user()->company->logo) }}" 
                             alt="Logo" 
                             class="w-20 h-20 rounded-lg object-cover border">
                    @else
                        <div class="w-20 h-20 flex items-center justify-center bg-gray-200 rounded-lg text-gray-500">
                            No Logo
                        </div>
                    @endif

                    <input type="file" 
                           name="logo" 
                           class="border rounded-lg px-3 py-2 w-full focus:ring focus:ring-blue-200">
                </div>

        </div>
            <div class="flex justify-center mt-6">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-lg font-semibold transition">Submit</button>
            </div>

    </form>
</div>

</x-app-layout>

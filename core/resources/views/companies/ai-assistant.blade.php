<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Create AI Assistant') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="mb-4 p-3 rounded bg-green-100 text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('company.ai-assistant.update') }}">
                    @csrf

                    <!-- Assistant Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">AI Assistant Name</label>
                        <input type="text" name="ai_name"
                               value="{{ old('ai_name', $company->ai_name) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm mt-1">
                        @error('ai_name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Assistant Description / Prompt -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Assistant Description / Prompt
                        </label>
                        <textarea name="assistant_description"
                                  rows="4"
                                  class="w-full border-gray-300 rounded-md shadow-sm mt-1"
                                  placeholder="Describe what your AI assistant does (e.g. 'Handles customer inquiries for my spa, books appointments, and answers FAQs')">{{ old('assistant_description', $company->assistant_description) }}</textarea>
                        @error('assistant_description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Welcome Message -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Welcome Message (What the AI says first when someone calls)
                        </label>
                        <textarea name="welcome_message"
                                  rows="3"
                                  class="w-full border-gray-300 rounded-md shadow-sm mt-1"
                                  placeholder="e.g. Hi there! I'm Lily, your virtual assistant. How can I help you today?">{{ old('welcome_message',$company->welcome_message) }}</textarea>
                        @error('welcome_message')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- AI Voice or Persona -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Voice / Persona
                        </label>
                        <select name="voice" class="w-full border-gray-300 rounded-md shadow-sm mt-1">
                            <option value="">Select a voice style</option>
                            <option value="friendly">Friendly & Warm</option>
                            <option value="professional">Professional & Calm</option>
                            <option value="energetic">Energetic & Cheerful</option>
                            <option value="soft">Soft & Gentle</option>
                        </select>
                        @error('voice')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="mt-6">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none">
                            Create Assistant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

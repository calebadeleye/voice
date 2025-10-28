<x-app-layout>
<div class="max-w-3xl mx-auto bg-white p-6 shadow rounded-lg">
    <h2 class="text-2xl font-semibold mb-4">Company Settings</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('company.update', $company->id) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-medium">Company Name</label>
            <input type="text" name="name" value="{{ old('name', $company->name) }}" class="w-full border p-2 rounded">
        </div>

        <div>
            <label class="block font-medium">Industry</label>
            <input type="text" name="industry" value="{{ old('industry', $company->industry) }}" class="w-full border p-2 rounded">
        </div>

        <div>
            <label class="block font-medium">Description</label>
            <textarea name="description" class="w-full border p-2 rounded">{{ old('description', $company->description) }}</textarea>
        </div>

        <div>
            <label class="block font-medium">Website</label>
            <input type="url" name="website" value="{{ old('website', $company->website) }}" class="w-full border p-2 rounded">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', $company->email) }}" class="w-full border p-2 rounded">
            </div>
        </div>

        <hr class="my-4">

        <div>
                <label class="block text-gray-600 font-medium mb-2">Company Logo</label>
                <div class="flex items-center space-x-4">
                    @if(auth()->user()->company->logo)
                        <img src="{{ asset('storage/' . auth()->user()->company->logo) }}" alt="Logo" class="w-20 h-20 rounded-lg object-cover border">
                    @else
                        <div class="w-20 h-20 flex items-center justify-center bg-gray-200 rounded-lg text-gray-500">No Logo</div>
                    @endif
                    <input type="file" name="logo" class="border rounded-lg px-3 py-2 w-full focus:ring focus:ring-blue-200">
                </div>
        </div>

        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Save Changes</button>
    </form>

    
<hr class="my-6">

<div>
    <h3 class="text-xl font-semibold mb-4">Company Files</h3>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('company.uploadFile') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-3 mb-4">
        @csrf
        <input type="file" name="file" required class="border p-2 rounded">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Upload</button>
    </form>

    @if($company->files->count() > 0)
        <table class="w-full border-collapse border">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="border p-2">File Name</th>
                    <th class="border p-2">Type</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($company->files as $file)
                    <tr>
                        <td class="border p-2">{{ $file->file_name }}</td>
                        <td class="border p-2">{{ strtoupper($file->file_type) }}</td>
                        <td class="border p-2">
                            <a href="{{ asset('storage/'.$file->file_path) }}" target="_blank" class="text-blue-600 underline">View</a> |
                            <form action="{{ route('company.deleteFile', $file->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline" onclick="return confirm('Delete this file?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-gray-500">No files uploaded yet.</p>
    @endif
</div>
</div>

</x-app-layout>

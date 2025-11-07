@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">✏️ Edit Project Request</h2>

    {{-- FORM --}}
    <form action="{{ route('manager.project-request.update', $projectRequest->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- tiket --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Nomor Tiket</label>
            <input type="text" value="{{ $projectRequest->tiket }}" readonly
                   class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-600">
        </div>

        {{-- client --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Pilih Client</label>
            <select name="client_id" id="client_id"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                    onchange="if(this.value) window.location='{{ route('manager.project-request.edit', $projectRequest->id) }}?client_id=' + this.value;">
                <option value="">-- Pilih Client --</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" 
                        {{ (request('client_id') ?? $projectRequest->client_id) == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- @php
            $selectedClient = request('client_id') 
                ? \App\Models\Client::find(request('client_id')) 
                : $projectRequest->client;
        @endphp --}}

        @if($selectedClient)
        <div class="mb-6 p-4 bg-gray-50 border rounded-lg">
            <h3 class="font-semibold text-gray-800 mb-2">Detail Client:</h3>
            <p><span class="font-medium text-gray-700">Nama:</span> {{ $selectedClient->name }}</p>
            <p><span class="font-medium text-gray-700">NIK:</span> {{ $selectedClient->nik }}</p>
            <p><span class="font-medium text-gray-700">Kode Organisasi:</span> {{ $selectedClient->kode_organisasi }}</p>
            <p><span class="font-medium text-gray-700">Phone:</span> {{ $selectedClient->phone }}</p>
        </div>
        @endif

        {{-- project --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Nama Project</label>
            <input type="text" name="name_project" value="{{ old('name_project', $projectRequest->name_project) }}" required
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- kategori --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Kategori</label>
            <div class="flex items-center space-x-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="kategori" value="New Aplikasi"
                        {{ old('kategori', $projectRequest->kategori) == 'New Aplikasi' ? 'checked' : '' }}
                        class="text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-gray-700">New Aplikasi</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="kategori" value="Update Aplikasi"
                        {{ old('kategori', $projectRequest->kategori) == 'Update Aplikasi' ? 'checked' : '' }}
                        class="text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-gray-700">Update Aplikasi</span>
                </label>
            </div>
        </div>

        {{-- deskripsi --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Deskripsi</label>
            <textarea name="description" rows="4" required
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $projectRequest->description) }}</textarea>
        </div>

        {{-- dokumen --}}
        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-1">Upload Dokumen (opsional)</label>
            <input type="file" name="document"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            
            @if($projectRequest->document)
                <p class="text-sm text-gray-600 mt-2">
                    Dokumen saat ini: 
                    <a href="{{ asset('storage/' . $projectRequest->document) }}" target="_blank" class="text-indigo-600 hover:underline">
                        Lihat File
                    </a>
                </p>
            @endif
        </div>

        
        <div class="flex justify-end">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2 rounded-md shadow-md transition">
                Update Request
            </button>
        </div>
    </form>
</div>
@endsection

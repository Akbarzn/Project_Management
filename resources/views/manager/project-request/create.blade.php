@extends('layouts.manager')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">🧾 Buat Project Request (Manager)</h2>

    {{-- FORM --}}
    <form action="{{ route('manager.project-request.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Nomor Tiket --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Nomor Tiket</label>
            <input type="text" name="tiket" value="{{ $ticketNumber }}" readonly
                   class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-600">
        </div>

        {{-- PILIH CLIENT --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Pilih Client</label>
            <select name="client_id" required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- Pilih Client --</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->name }} ({{ $client->kode_organisasi }})
                    </option>
                @endforeach
            </select>
            @error('client_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nama Project --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Nama Project</label>
            <input type="text" name="name_project" value="{{ old('name_project') }}" required
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            @error('name_project')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Kategori --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Kategori</label>
            <div class="flex items-center space-x-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="kategori" value="New Aplikasi"
                        {{ old('kategori') == 'New Aplikasi' ? 'checked' : '' }}
                        class="text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-gray-700">New Aplikasi</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="kategori" value="Update Aplikasi"
                        {{ old('kategori') == 'Update Aplikasi' ? 'checked' : '' }}
                        class="text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-gray-700">Update Aplikasi</span>
                </label>
            </div>
            @error('kategori')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Deskripsi --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Deskripsi Project</label>
            <textarea name="description" rows="4" required
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Upload Dokumen --}}
        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-1">Upload Dokumen Pendukung (opsional)</label>
            <input type="file" name="document"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            <p class="text-sm text-gray-500 mt-1">Format: PDF, DOC, DOCX, PNG, JPG (maks. 2MB)</p>
            @error('document')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2 rounded-md shadow-md transition">
                Simpan Request
            </button>
        </div>
    </form>
</div>
@endsection

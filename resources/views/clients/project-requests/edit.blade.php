@extends('layouts.client')

@section('title', 'Edit Project Request')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h5 class="text-xl font-semibold">Edit Project Request</h5>
        <a href="{{ route('clients.project-requests.index') }}" 
           class="bg-gray-200 text-gray-700 px-3 py-1 rounded hover:bg-gray-300">
           ← Kembali
        </a>
    </div>

    <form action="{{ route('clients.project-requests.update', $projectRequest->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Kategori --}}
        <div class="mb-4">
            <span class="block text-gray-700 font-medium mb-2">Kategori</span>
            <label class="inline-flex items-center mr-4">
                <input type="radio" name="kategori" value="New Aplikasi" required
                    class="form-radio text-indigo-600"
                    {{ old('kategori', $projectRequest->kategori) == 'New Aplikasi' ? 'checked' : '' }}>
                <span class="ml-2">New Aplikasi</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="kategori" value="Update Aplikasi" required
                    class="form-radio text-indigo-600"
                    {{ old('kategori', $projectRequest->kategori) == 'Update Aplikasi' ? 'checked' : '' }}>
                <span class="ml-2">Update Aplikasi</span>
            </label>
            @error('kategori')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Deskripsi --}}
        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-medium mb-1">Deskripsi</label>
            <textarea name="description" id="description" rows="4" required
                class="block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $projectRequest->description) }}</textarea>
            @error('description')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Upload Dokumen --}}
        <div class="mb-4">
            <label for="upload_file" class="block text-gray-700 font-medium mb-1">Upload Dokumen (Opsional)</label>
            <input type="file" name="upload_file" id="upload_file"
                class="block w-full border border-gray-300 rounded-md px-3 py-2 @error('upload_file') border-red-500 @enderror" accept=".pdf,.doc,.docx,.zip,.rar">
            @error('upload_file')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
            @if($projectRequest->upload_file)
                <p class="mt-2 text-gray-700">📎 Dokumen saat ini:
                    <a href="{{ asset('storage/' . $projectRequest->upload_file) }}" target="_blank" class="text-indigo-600 hover:underline">Lihat File</a>
                </p>
            @endif
        </div>

       
        </div>

        {{-- Tombol Submit --}}
        <div class="flex justify-end">
            <button type="submit"
                class="bg-green-600 text-white px-5 py-2 rounded-md hover:bg-green-700 transition">
                💾 Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

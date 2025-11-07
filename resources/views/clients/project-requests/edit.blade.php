@extends('layouts.app')

@section('title', 'Edit Project Request')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-lg mt-8 border border-gray-200">
    
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">✏️ Edit Project Request</h2>
        <a href="{{ route('clients.project-requests.index') }}" 
           class="inline-flex items-center text-sm font-medium bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
           ← Kembali
        </a>
    </div>

    <form action="{{ route('clients.project-requests.update', $projectRequest->id) }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Nama Project --}}
        <div>
            <label for="name_project" class="block text-gray-700 font-medium mb-2">Nama Project</label>
            <input type="text" 
                   id="name_project"
                   name="name_project" 
                   value="{{ old('name_project', $projectRequest->name_project) }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2"
                   placeholder="Masukkan nama project">
            @error('name_project')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Kategori --}}
        <div>
            <span class="block text-gray-700 font-medium mb-2">Kategori</span>
            <div class="flex items-center space-x-6">
                <label class="inline-flex items-center">
                    <input type="radio" name="kategori" value="New Aplikasi" 
                        class="text-indigo-600 border-gray-300 focus:ring-indigo-500"
                        {{ old('kategori', $projectRequest->kategori) == 'New Aplikasi' ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-700">New Aplikasi</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="radio" name="kategori" value="Update Aplikasi" 
                        class="text-indigo-600 border-gray-300 focus:ring-indigo-500"
                        {{ old('kategori', $projectRequest->kategori) == 'Update Aplikasi' ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-700">Update Aplikasi</span>
                </label>
            </div>
            @error('kategori')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Deskripsi --}}
        <div>
            <label for="description" class="block text-gray-700 font-medium mb-2">Deskripsi</label>
            <textarea name="description" 
                      id="description" 
                      rows="5" 
                      required
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2"
                      placeholder="Tuliskan deskripsi project...">{{ old('description', $projectRequest->description) }}</textarea>
            @error('description')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Dokumen --}}
        <div>
            <label for="document" class="block text-gray-700 font-medium mb-2">Upload Dokumen (Opsional)</label>
            <input type="file" 
                   name="document" 
                   id="document" 
                   accept=".pdf,.doc,.docx,.zip,.rar"
                   class="w-full border-gray-300 rounded-lg shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            @error('document')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror

            @if($projectRequest->document)
                <div class="mt-3 text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">
                    📎 Dokumen saat ini:
                    <a href="{{ asset('storage/' . $projectRequest->document) }}" 
                       target="_blank" 
                       class="text-indigo-600 font-medium hover:underline ml-1">
                        Lihat File
                    </a>
                </div>
            @endif
        </div>

        {{-- Tombol Submit --}}
        <div class="flex justify-end pt-4">
            <button type="submit"
                class="inline-flex items-center bg-green-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-green-700 transition-all focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                💾 Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

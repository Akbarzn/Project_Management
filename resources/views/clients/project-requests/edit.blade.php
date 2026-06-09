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

        {{-- Parameter Auto Assignment Tim --}}
        <div class="mb-6 p-5 bg-indigo-50 border border-indigo-200 rounded-xl">
            <h3 class="text-base font-bold text-indigo-800 mb-4 flex items-center gap-2">
                <i class="fas fa-robot text-indigo-600"></i>
                Parameter Auto Assignment Tim
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Priority --}}
                <div>
                    <label for="priority" class="block text-sm font-semibold text-gray-700 mb-1">
                        Priority
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="priority" id="priority"
                        class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('priority') border-red-500 @enderror"
                        required>
                        <option value="">-- Pilih Priority --</option>
                        <option value="1" {{ old('priority', $projectRequest->priority) == '1' ? 'selected' : '' }}>Low</option>
                        <option value="2" {{ old('priority', $projectRequest->priority) == '2' ? 'selected' : '' }}>Medium</option>
                        <option value="3" {{ old('priority', $projectRequest->priority) == '3' ? 'selected' : '' }}>High</option>
                        <option value="4" {{ old('priority', $projectRequest->priority) == '4' ? 'selected' : '' }}>Critical</option>
                    </select>
                    @error('priority')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Difficulty --}}
                <div>
                    <label for="difficulty" class="block text-sm font-semibold text-gray-700 mb-1">
                        Difficulty
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="difficulty" id="difficulty"
                        class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('difficulty') border-red-500 @enderror"
                        required>
                        <option value="">-- Pilih Difficulty --</option>
                        <option value="1" {{ old('difficulty', $projectRequest->difficulty) == '1' ? 'selected' : '' }}>Sangat Mudah</option>
                        <option value="2" {{ old('difficulty', $projectRequest->difficulty) == '2' ? 'selected' : '' }}>Mudah</option>
                        <option value="3" {{ old('difficulty', $projectRequest->difficulty) == '3' ? 'selected' : '' }}>Sedang</option>
                        <option value="4" {{ old('difficulty', $projectRequest->difficulty) == '4' ? 'selected' : '' }}>Sulit</option>
                        <option value="5" {{ old('difficulty', $projectRequest->difficulty) == '5' ? 'selected' : '' }}>Sangat Sulit</option>
                    </select>
                    @error('difficulty')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Info Task Weight --}}
            <p class="text-xs text-indigo-600 mt-3">
                <i class="fas fa-info-circle mr-1"></i>
                <strong>Task Weight</strong> = Priority × Difficulty — digunakan oleh sistem untuk mengukur bobot penugasan pada pembagian tim otomatis (Least Load).
            </p>
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

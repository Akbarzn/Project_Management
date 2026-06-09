@extends('layouts.app')

@section('title', 'Edit Project Request')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    {{-- HEADER --}}
    <div class="bg-indigo-700 px-6 py-4 rounded-t-xl shadow-lg flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-white flex items-center gap-3">
            <i class="fas fa-edit"></i>
            Edit Project Request
        </h2>

        <span class="text-white font-medium text-lg">
            #{{ $projectRequest->tiket }}
        </span>
    </div>

    {{-- CARD --}}
    <div class="bg-white border border-gray-200 p-6 rounded-b-xl shadow-xl">

        {{-- VALIDATION ERRORS --}}
        @if ($errors->any())
            <div class="mb-5 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm">
                <p class="font-bold">⚠️ Gagal menyimpan. Silakan periksa:</p>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM --}}
        <form action="{{ route('manager.project-request.update', $projectRequest->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- NOMOR TIKET --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Tiket</label>
                <input type="text" value="{{ $projectRequest->tiket }}" readonly
                       class="w-full border border-gray-300 bg-gray-100 rounded-lg px-4 py-2.5 text-gray-600">
            </div>

            {{-- PILIH CLIENT --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Client</label>
                <select name="client_id" id="client_id"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500"
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

            {{-- DETAIL CLIENT --}}
            @if($selectedClient)
            <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 shadow-sm">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-user"></i>
                    Detail Client
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <p><span class="font-medium text-gray-700">Nama :</span> {{ $selectedClient->name }}</p>
                    <p><span class="font-medium text-gray-700">NIK :</span> {{ $selectedClient->nik }}</p>
                    <p><span class="font-medium text-gray-700">Kode Organisasi :</span> {{ $selectedClient->kode_organisasi }}</p>
                    <p><span class="font-medium text-gray-700">Telepon :</span> {{ $selectedClient->phone }}</p>
                </div>
            </div>
            @endif

            {{-- NAMA PROJECT --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Project</label>
                <input type="text" name="name_project" value="{{ old('name_project', $projectRequest->name_project) }}"
                       required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('name_project') border-red-500 @enderror">
                @error('name_project')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- KATEGORI --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori</label>
                <div class="flex items-center gap-6 mt-2">
                    <label class="flex items-center">
                        <input type="radio" name="kategori" value="New Aplikasi"
                            {{ old('kategori', $projectRequest->kategori) == 'New Aplikasi' ? 'checked' : '' }}
                            class="text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-gray-700">New Aplikasi</span>
                    </label>

                    <label class="flex items-center">
                        <input type="radio" name="kategori" value="Update Aplikasi"
                            {{ old('kategori', $projectRequest->kategori) == 'Update Aplikasi' ? 'checked' : '' }}
                            class="text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-gray-700">Update Aplikasi</span>
                    </label>
                </div>
            </div>

            {{-- DESKRIPSI --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="4" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $projectRequest->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ═══════════════════════════════════════════════════════════════ --}}
            {{-- SECTION: Auto Assignment Parameters                            --}}
            {{-- ═══════════════════════════════════════════════════════════════ --}}
            <div class="p-5 bg-indigo-50 border border-indigo-200 rounded-xl">
                <h3 class="text-base font-bold text-indigo-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-robot text-indigo-600"></i>
                    Parameter Auto Assignment Tim
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Priority --}}
                    <div>
                        <label for="priority" class="block text-sm font-semibold text-gray-700 mb-1">
                            Priority <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" id="priority"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('priority') border-red-500 @enderror"
                            required>
                            <option value="">-- Pilih Priority --</option>
                            <option value="1" {{ old('priority', $projectRequest->priority) == 1 ? 'selected' : '' }}>Low</option>
                            <option value="2" {{ old('priority', $projectRequest->priority) == 2 ? 'selected' : '' }}>Medium</option>
                            <option value="3" {{ old('priority', $projectRequest->priority) == 3 ? 'selected' : '' }}>High</option>
                            <option value="4" {{ old('priority', $projectRequest->priority) == 4 ? 'selected' : '' }}>Critical</option>
                        </select>
                        @error('priority')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Difficulty --}}
                    <div>
                        <label for="difficulty" class="block text-sm font-semibold text-gray-700 mb-1">
                            Difficulty <span class="text-red-500">*</span>
                        </label>
                        <select name="difficulty" id="difficulty"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('difficulty') border-red-500 @enderror"
                            required>
                            <option value="">-- Pilih Difficulty --</option>
                            <option value="1" {{ old('difficulty', $projectRequest->difficulty) == 1 ? 'selected' : '' }}>Sangat Mudah</option>
                            <option value="2" {{ old('difficulty', $projectRequest->difficulty) == 2 ? 'selected' : '' }}>Mudah</option>
                            <option value="3" {{ old('difficulty', $projectRequest->difficulty) == 3 ? 'selected' : '' }}>Sedang</option>
                            <option value="4" {{ old('difficulty', $projectRequest->difficulty) == 4 ? 'selected' : '' }}>Sulit</option>
                            <option value="5" {{ old('difficulty', $projectRequest->difficulty) == 5 ? 'selected' : '' }}>Sangat Sulit</option>
                        </select>
                        @error('difficulty')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Task Weight Preview --}}
                @if($projectRequest->priority && $projectRequest->difficulty)
                    <div class="mt-3 p-3 bg-white border border-indigo-200 rounded-lg">
                        <p class="text-xs text-indigo-700">
                            <i class="fas fa-calculator mr-1"></i>
                            <strong>Task Weight saat ini:</strong>
                            {{ $projectRequest->priority }} × {{ $projectRequest->difficulty }}
                            = <strong class="text-indigo-900">{{ $projectRequest->priority * $projectRequest->difficulty }}</strong>
                        </p>
                    </div>
                @endif
            </div>

            {{-- DOKUMEN --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Upload Dokumen (opsional)</label>
                <input type="file" name="document"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500">

                @if($projectRequest->document)
                    <p class="text-sm text-gray-600 mt-2">
                        Dokumen saat ini:
                        <a href="{{ asset('storage/' . $projectRequest->document) }}" target="_blank"
                           class="text-indigo-600 hover:underline font-medium">
                            Lihat File
                        </a>
                    </p>
                @endif
                @error('document')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- BUTTON --}}
            <div class="pt-4 border-t border-gray-200 flex justify-end">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-lg shadow-md shadow-indigo-300/50 transition">
                    <i class="fas fa-save mr-2"></i>
                    Update Request
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

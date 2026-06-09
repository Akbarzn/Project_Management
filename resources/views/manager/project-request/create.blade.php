@extends('layouts.app')

@section('title', 'Create Project Request')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Create Project Request</h2>

            <a href="{{ route('manager.project-request.index') }}"
                class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg shadow">
                Kembali
            </a>
        </div>

        {{-- Validation Errors --}}
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

        {{-- Form Create Project Request --}}
        <form action="{{ route('manager.project-request.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Nomor Tiket --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Nomor Tiket</label>
                <input type="text" name="tiket" value="{{ $ticketNumber }}" readonly
                    class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-600">
            </div>

            {{-- Pilih Client --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Pilih Client</label>
                <select name="client_id"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('client_id') border-red-500 @enderror"
                    onchange="if(this.value) window.location='{{ route('manager.project-request.create') }}?client_id=' + this.value;">

                    <option value="">-- Pilih Client --</option>

                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}"
                            {{ (request('client_id') ?? old('client_id')) == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>

                @error('client_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Detail Client --}}
            @if ($selectedClient)
                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 shadow-sm mb-4">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-user"></i>
                        Detail Client
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <p><span class="font-medium text-gray-700">Nama :</span> {{ $selectedClient->name }}</p>
                        <p><span class="font-medium text-gray-700">NIK :</span> {{ $selectedClient->nik }}</p>
                        <p><span class="font-medium text-gray-700">Kode Organisasi :</span>
                            {{ $selectedClient->kode_organisasi }}</p>
                        <p><span class="font-medium text-gray-700">Telepon :</span> {{ $selectedClient->phone }}</p>
                    </div>
                </div>
            @endif

            {{-- Nama Project --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Nama Project</label>
                <input type="text" name="name_project" value="{{ old('name_project') }}" required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name_project') border-red-500 @enderror">
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
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ═══════════════════════════════════════════════════════════════ --}}
            {{-- SECTION: Auto Assignment Parameters                            --}}
            {{-- ═══════════════════════════════════════════════════════════════ --}}
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
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('priority') border-red-500 @enderror"
                            required>
                            <option value="">-- Pilih Priority --</option>
                            <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>Low</option>
                            <option value="2" {{ old('priority') == '2' ? 'selected' : '' }}>Medium</option>
                            <option value="3" {{ old('priority') == '3' ? 'selected' : '' }}>High</option>
                            <option value="4" {{ old('priority') == '4' ? 'selected' : '' }}>Critical</option>
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
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('difficulty') border-red-500 @enderror"
                            required>
                            <option value="">-- Pilih Difficulty --</option>
                            <option value="1" {{ old('difficulty') == '1' ? 'selected' : '' }}>Sangat Mudah</option>
                            <option value="2" {{ old('difficulty') == '2' ? 'selected' : '' }}>Mudah</option>
                            <option value="3" {{ old('difficulty') == '3' ? 'selected' : '' }}>Sedang</option>
                            <option value="4" {{ old('difficulty') == '4' ? 'selected' : '' }}>Sulit</option>
                            <option value="5" {{ old('difficulty') == '5' ? 'selected' : '' }}>Sangat Sulit</option>
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
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-1">Upload Dokumen Pendukung (opsional)</label>
                <input type="file" name="document"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="text-sm text-gray-500 mt-1">Format: PDF, DOC, DOCX, PNG, JPG (maks. 2MB)</p>
                @error('document')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Simpan --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2 rounded-md shadow-md transition">
                    Simpan Request
                </button>
            </div>

        </form>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Create Project Request</h2>

            <a href="{{ route('manager.project-request.index') }}"
                class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg shadow">
                Kembali
            </a>
        </div>

        {{--  Form Create Project Request --}}
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
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
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

            {{-- Detail Client  --}}
            @if ($selectedClient)
                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 shadow-sm">
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

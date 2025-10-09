@extends('layouts.manager')

@section('content')

<div class="max-w-3xl mx-auto mt-10 bg-white rounded-2xl shadow-lg p-8">
<h2 class="text-2xl font-bold text-gray-800 mb-6">✏️ Edit Data Client</h2>

@if($errors->any())
    <div class="mb-4 bg-red-100 text-red-700 p-4 rounded-lg">
        <strong>Terjadi kesalahan:</strong>
        <ul class="mt-2 list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('clients.update', $client->id) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')
    
    <!-- Input Nama Client -->
    <div>
        <label for="name" class="block text-gray-700 font-medium mb-1">Nama Client</label>
        <input type="text" name="name" id="name"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
            value="{{ old('name', $client->name) }}" required>
    </div>

    <!-- Input NIK -->
    <div>
        <label for="nik" class="block text-gray-700 font-medium mb-1">NIK</label>
        <input type="text" name="nik" id="nik"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
            value="{{ old('nik', $client->nik) }}">
    </div>

    <!-- Input Nama Project (BARU DITAMBAHKAN) -->
    <div>
        <label for="project_name" class="block text-gray-700 font-medium mb-1">Nama Project</label>
        <input type="text" name="project_name" id="project_name"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
            value="{{ old('project_name', $client->project_name) }}">
    </div>

    <!-- Input Phone -->
    <div>
        <label for="phone" class="block text-gray-700 font-medium mb-1">Nomor Telepon</label>
        <input type="text" name="phone" id="phone"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
            value="{{ old('phone', $client->phone) }}">
    </div>

    <!-- Input Kode Organisasi -->
    <div>
        <label for="kode_organisasi" class="block text-gray-700 font-medium mb-1">Kode Organisasi</label>
        <input type="text" name="kode_organisasi" id="kode_organisasi"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
            value="{{ old('kode_organisasi', $client->kode_organisasi) }}">
    </div>

    <div class="flex justify-end space-x-3">
        <a href="{{ route('clients.index') }}"
           class="bg-gray-300 text-gray-800 px-5 py-2 rounded-lg hover:bg-gray-400 transition">
            Batal
        </a>
        <button type="submit"
            class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition">
            Update Data
        </button>
    </div>
</form>

</div>
@endsection
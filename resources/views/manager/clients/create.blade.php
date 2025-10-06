@extends('layouts.manager')

@section('content')
<div class="max-w-3xl mx-auto mt-10 bg-white rounded-2xl shadow-lg p-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Client Baru</h2>

    {{-- Pesan error validasi --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-100 text-red-700 p-4 rounded-lg">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form tambah client --}}
    <form action="{{ route('manager.clients.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="name" class="block text-gray-700 font-medium mb-1">Nama</label>
            <input type="text" name="name" id="name"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                value="{{ old('name') }}" required>
        </div>

        <div>
            <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
            <input type="email" name="email" id="email"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                value="{{ old('email') }}" required>
        </div>

        <div>
            <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
            <input type="password" name="password" id="password"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                required>
        </div>

        <div>
            <label for="nik" class="block text-gray-700 font-medium mb-1">NIK</label>
            <input type="text" name="nik" id="nik"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                value="{{ old('nik') }}" required>
        </div>

        <div>
            <label for="phone" class="block text-gray-700 font-medium mb-1">Nomor Telepon</label>
            <input type="text" name="phone" id="phone"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                value="{{ old('phone') }}">
        </div>

        <div>
            <label for="kode_organisasi" class="block text-gray-700 font-medium mb-1">Kode Organisasi</label>
            <input type="text" name="kode_organisasi" id="kode_organisasi"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                value="{{ old('kode_organisasi') }}">
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('manager.clients.index') }}"
               class="bg-gray-300 text-gray-800 px-5 py-2 rounded-lg hover:bg-gray-400 transition">
                Batal
            </a>
            <button type="submit"
                class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <div class="flex justify-between items-center ">
        <h2 class="text-2xl font-bold mb-6">Edit Profil</h2>
        <a href="{{ route(Auth::user()->hasRole('manager') ? 'manager.dashboard' : (Auth::user()->hasRole('karyawan') ? 'karyawan.tasks.index' :'clients.project-requests.index')) }}"
        class="bg-indigo-500 px-4 py-2 mb-2 rounded-lg text-white hover:bg-indigo-700">
        Kembali</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        
        <div class="grid grid-cols-2 gap-4 mb-4">
        {{-- Nama --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" 
                class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" 
                class="w-full border rounded-lg px-3 py-2 bg-gray-100" >
        </div>

        {{-- Password baru --}}

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Password Baru</label>
            <input type="password" name="password" 
                   placeholder="Kosongkan jika tidak ingin mengubah" 
                   class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" 
                   class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500" >
                   @error('password_confirmation')
                   <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                   @enderror
                </div>
            </div>

        {{-- karyawan --}}
        @include('profile.edit-karyawan')

        {{-- client --}}
        @if(Auth::user()->hasRole('client'))
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">nik</label>
            <input type="text" name="nik" 
                   value="{{ old('nik', Auth::user()->client->nik ?? '') }}" 
                   class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div class="mb-4">
            <label for="kode_organisasi" class="block text-gray-700 font-medium mb-1">Kode Organisasi</label>
            <input type="text" name="kode_organisasi" value="{{ old('kode_organisasi', Auth::user()->client->kode_organisasi) }}">
        </div>
        @endif

        {{-- Foto Profil --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Foto Profil</label>
            @if(Auth::user()->potho_profile)
                <img src="{{ asset('storage/' . Auth::user()->potho_profile) }}" 
                     alt="Profile" class="w-20 h-20 rounded-full object-cover mb-2 border">
            @else
                <img src="{{ asset('images/default.jpg') }}" 
                     alt="Profile" class="w-20 h-20 rounded-full object-cover mb-2 border">
            @endif

            <input type="file" name="profile_photo"
                   class="block w-full text-sm text-gray-600 border rounded-lg cursor-pointer focus:ring-indigo-500 focus:border-indigo-500">
            <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG (maks 2MB)</p>
        </div>

        {{-- Tombol Submit --}}
        <div class="flex justify-end">
            <button type="submit" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg shadow font-medium">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Edit Profil')

@section('content')

<div class="max-w-4xl mx-auto bg-white px-8 py-10 rounded-2xl shadow-xl border border-gray-200">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 flex items-center gap-3">
                <i class="fas fa-user-edit text-indigo-600"></i>
                Edit Profil
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Perbarui informasi akun dan identitas Anda.
            </p>
        </div>

        <a href="{{ route(Auth::user()->hasRole('manager') ? 'manager.dashboard' : (Auth::user()->hasRole('karyawan') ? 'karyawan.tasks.index' :'clients.project-requests.index')) }}"
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2.5 rounded-lg font-semibold shadow transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>


    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm mb-6">
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif


    {{-- FORM EDIT --}}
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        {{-- GRID UTAMA --}}
        <div class="grid md:grid-cols-2 gap-6">

            {{-- NAMA --}}
            <div>
                <label class="block text-gray-700 font-medium mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- EMAIL --}}
            <div>
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ Auth::user()->email }}"
                    class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed" readonly>
            </div>

            {{-- PASSWORD BARU --}}
            <div>
                <label class="block text-gray-700 font-medium mb-1">Password Baru</label>
                <input type="password" name="password"
                    placeholder="Biarkan kosong jika tidak ingin mengubah"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- KONFIRMASI PASSWORD --}}
            <div>
                <label class="block text-gray-700 font-medium mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('password_confirmation')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>


        {{-- FIELD KHUSUS KARYAWAN --}}
        @include('profile.edit-karyawan')


        {{-- FIELD KHUSUS CLIENT --}}
        @if(Auth::user()->hasRole('client'))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

            <div>
                <label class="block text-gray-700 font-medium mb-1">NIK</label>
                <input type="text" name="nik"
                       value="{{ old('nik', Auth::user()->client->nik ?? '') }}"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Kode Organisasi</label>
                <input type="text" name="kode_organisasi"
                       value="{{ old('kode_organisasi', Auth::user()->client->kode_organisasi) }}"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

        </div>
        @endif


        {{-- FOTO PROFIL --}}
        <div class="mt-8">
            <label class="block text-gray-700 font-medium mb-2">Foto Profil</label>

            <div class="flex items-center gap-5">
                <img src="{{ Auth::user()->potho_profile ? asset('storage/' . Auth::user()->potho_profile) : asset('images/default.jpg') }}"
                     class="w-20 h-20 rounded-full object-cover border shadow">

                <div class="flex-grow">
                    <input type="file" name="profile_photo"
                        class="block w-full text-sm text-gray-600 border rounded-lg cursor-pointer focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG — Maksimal 2MB</p>
                </div>
            </div>
        </div>


        {{-- SUBMIT --}}
        <div class="flex justify-end mt-10">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg shadow-md font-semibold transition">
                <i class="fas fa-save mr-2"></i>
                Simpan Perubahan
            </button>
        </div>

    </form>

</div>

@endsection

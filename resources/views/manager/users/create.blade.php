@extends('layouts.manager')

@section('title', 'Tambah User Baru')

@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
        
        <div class="bg-indigo-700 py-4 px-6 flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-white">
                <i class="fas fa-user-plus mr-3"></i> {{-- Ikon Font Awesome untuk tambah user --}}
                Tambah User Baru
            </h2>
        </div>

        <div class="p-6">
            {{--  ERROR VALIDASI --}}
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

            <form action="{{ route('manager.users.store') }}" method="POST" class="space-y-5">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror" 
                            required />
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror" 
                            required />
                    </div>
                </div>

                <hr class="border-gray-200 pt-4">

                {{-- Password & Konfirmasi Password Group (Grid 2 Kolom) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" id="password" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror" 
                            required />
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500" 
                            required />
                    </div>
                </div>

                {{-- Role --}}
                <hr class="border-gray-200 pt-4">
                
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="role" class="block text-sm font-semibold text-gray-700 mb-1">Role</label>
                        <select name="role" id="role" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 bg-white focus:ring-indigo-500 focus:border-indigo-500 @error('role') border-red-500 @enderror" 
                            required>
                            <option value="">Pilih Role</option>
                            <option value="karyawan" {{ old('role') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                            <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>Client</option>
                            <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                        </select>
                    </div>
                </div>

                <div class="pt-6 flex justify-between items-center">
                    <a href="{{ route('manager.users.index') }}"
                       class="px-5 py-2.5 text-gray-700 border border-gray-300 rounded-lg font-semibold hover:bg-gray-100 transition duration-300 ease-in-out">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-semibold shadow-md shadow-indigo-300/50 hover:bg-indigo-700 transition duration-300 ease-in-out">
                        <i class="fas fa-save mr-2"></i>
                        Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
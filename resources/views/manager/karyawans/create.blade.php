@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@section('content')

@php
    // daftar job title
    $requiredJobTitle = [
        'Analisis Proses Bisnis',
        'Database Functional',
        'Programmer',
        'Quality Test',
        'SysAdmin',
    ];
@endphp

<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
        
        <div class="bg-indigo-700 py-4 px-6 flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-white">
                <i class="fas fa-user-plus mr-3"></i> 
                Tambah Karyawan Baru
            </h2>
        </div>

        <div class="p-6"> 
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

            <form action="{{ route('manager.karyawans.store') }}" method="POST" class="space-y-5">
                @csrf
                
                {{-- Nama & Email --}}
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

                {{-- Password & Konfirmasi Password --}}
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
                    
                <hr class="border-gray-200 pt-4">

                {{-- NIK & Nomor Telepon --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- NIK --}}
                    <div>
                        <label for="nik" class="block text-sm font-semibold text-gray-700 mb-1">NIK</label>
                        <input type="text" name="nik" id="nik" value="{{ old('nik') }}" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('nik') border-red-500 @enderror" 
                            required />
                    </div>

                    {{-- Nomor Telepon --}}
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-500 @enderror" />
                    </div>
                </div>
                
                {{-- Job Title & Cost --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Job Title (Diubah menjadi SELECT) --}}
                    <div>
                        <label for="job_title" class="block text-sm font-semibold text-gray-700 mb-1">Job Title </label>
                        <select name="job_title" id="job_title" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('job_title') border-red-500 @enderror" 
                            required>
                            <option value="" disabled {{ old('job_title') == null ? 'selected' : '' }}>Pilih Job Title</option>
                            @foreach($requiredJobTitle as $jobTitle)
                                <option value="{{ $jobTitle }}" {{ old('job_title') == $jobTitle ? 'selected' : '' }}>
                                    {{ $jobTitle }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Biaya --}}
                    <div>
                        <label for="cost" class="block text-sm font-semibold text-gray-700 mb-1">Biaya / Jam (Rp)</label>
                        <input type="number" step="0.01" name="cost" id="cost" value="{{ old('cost') }}" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('cost') border-red-500 @enderror" 
                            required 
                            />
                    </div>
                </div>

                <div>
                    <label for="jabatan" class="block text-sm font-semibold text-gray-700 mb-1">Jabatan</label>
                    <input type="text" name="jabatan" id="jabatan" value="{{ old('jabatan') }}"
                        class="2-full border border-gray-300 rounded-lg px-8 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('jabatan') border-red-500 @enderror"
                        required
                    />
                </div>

                <div class="pt-6 flex justify-between items-center">
                    <a href="{{ route('manager.karyawans.index') }}"
                       class="px-5 py-2.5 text-gray-700 border border-gray-300 rounded-lg font-semibold hover:bg-gray-100 transition duration-300 ease-in-out">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-semibold shadow-md shadow-indigo-300/50 hover:bg-indigo-700 transition duration-300 ease-in-out">
                        <i class="fas fa-save mr-2"></i> 
                        Simpan Karyawan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

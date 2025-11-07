@extends('layouts.app')

@section('title', 'Edit Karyawan')

@section('content')

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-xl mx-auto bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">

            <div class="bg-indigo-700 py-4 px-6 flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-white">
                    <i class="fas fa-user-tie mr-3"></i>
                    Ubah Data Karyawan
                </h2>
                <span class="text-white text-lg font-medium">{{ $karyawan->name }}</span>
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

                <form action="{{ route('manager.karyawans.update', $karyawan->id) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $karyawan->name) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                            required />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- NIK --}}
                        <div>
                            <label for="nik" class="block text-sm font-semibold text-gray-700 mb-1">NIK</label>
                            <input type="text" name="nik" id="nik" value="{{ old('nik', $karyawan->nik) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('nik') border-red-500 @enderror"
                                required />
                        </div>

                        {{-- Telepon --}}
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1">Nomor
                                Telepon</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $karyawan->phone) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-500 @enderror" />
                        </div>
                    </div>

                    {{-- Jabatan & Biaya Group --}}
                    <hr class="border-gray-200 pt-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Job Title --}}
                        <select name="job_title" id="job_title"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('job_title') border-red-500 @enderror"
                            required>
                            <option value="">-- Pilih Jabatan --</option>
                            @php
                                $jobTitles = [
                                    'Analisis Proses Bisnis',
                                    'Database Functional',
                                    'Programmer',
                                    'Quality Test',
                                    'SysAdmin',
                                ];
                            @endphp
                            @foreach ($jobTitles as $title)
                                <option value="{{ $title }}"
                                    {{ old('job_title', $karyawan->job_title ?? $karyawan->jabatan) == $title ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>

                        {{-- jabatan --}}
                        <div class="mb-4">
                            <label for="jabatan" class="block text-sm font-semibold text-gray-700 mb-1">Jabatan</label>
                            <input type="text" name="jabatan" value="{{ old('jabatan', $karyawan->jabatan) }}"
                            class="w-full border px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg">
                        </div>


                        {{-- Biaya --}}
                        <div>
                            <label for="cost" class="block text-sm font-semibold text-gray-700 mb-1">Biaya / Hari
                                (Rp)</label>
                            <input type="number" step="0.01" name="cost" id="cost"
                                value="{{ old('cost', $karyawan->cost) }}"
                                class="w-full border  rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('cost') border-red-500 @enderror"
                                required />
                        </div>
                    </div>

                    <div class="pt-6 flex justify-between items-center">
                        <a href="{{ route('manager.karyawans.index') }}"
                            class="px-5 py-2.5 text-gray-700 border border-gray-300 rounded-lg font-semibold hover:bg-gray-100 transition duration-300 ease-in-out">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-semibold shadow-md shadow-indigo-300/50 hover:bg-indigo-700 transition duration-300 ease-in-out">
                            <i class="fas fa-check mr-2"></i>
                            Update Karyawan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

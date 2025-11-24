@extends('layouts.app')

@section('title', 'Approve Project Request')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-2xl overflow-hidden">

        {{-- Header Form --}}
        <div class="bg-indigo-600 p-6 text-white flex justify-between items-center">
            <h2 class="text-3xl font-extrabold tracking-tight">
                <i class="fas fa-check-circle mr-3"></i>
                Persetujuan Project Baru
            </h2>
        </div>

        <div class="p-8">
            <form action="{{ route('manager.projects.store') }}" method="POST" class="space-y-8">
                @csrf
                <input type="hidden" name="request_id" value="{{ $projectRequest->id }}">

                {{-- Informasi Permintaan Project --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 shadow-inner">
                    <h3 class="text-xl font-bold text-gray-700 mb-4 border-b pb-2">
                        Detail Permintaan Project
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-800">

                        {{-- Client --}}
                        <div>
                            <span class="block font-semibold text-blue-700 mb-1">Client</span>
                            <div class="bg-white border rounded-md px-4 py-2 shadow-sm">
                                {{ $projectRequest->client->name ?? 'N/A' }}
                            </div>
                        </div>

                        {{-- Nama Project --}}
                        <div>
                            <span class="block font-semibold text-blue-700 mb-1">Nama Project</span>
                            <div class="bg-white border rounded-md px-4 py-2 shadow-sm">
                                {{ $projectRequest->name_project ?? 'N/A' }}
                            </div>
                        </div>

                        {{-- Kategori Project --}}
                        <div>
                            <span class="block font-semibold text-blue-700 mb-1">Kategori Project</span>
                            <div class="bg-white border rounded-md px-4 py-2 shadow-sm">
                                {{ $projectRequest->category ?? 'Tidak ada kategori' }}
                            </div>
                        </div>

                        {{-- Dokumen  --}}
                        <div>
                            <span class="block font-semibold text-blue-700 mb-1">Dokumen Lampiran</span>

                            @php
                                $documentPath = $projectRequest->document_path ?? $projectRequest->document;
                            @endphp

                            @if($documentPath)
                                @php
                                    $documentUrl = asset('storage/' . $documentPath);
                                    $fileName = basename($documentPath);
                                @endphp

                                <a href="{{ $documentUrl }}" target="_blank"
                                    class="inline-flex items-center gap-2 text-indigo-600 border border-indigo-200 px-3 py-2 rounded-md bg-white shadow-sm hover:bg-indigo-50 hover:text-indigo-800 transition truncate">
                                    <i class="fas fa-file-alt"></i>
                                    <span class="truncate block max-w-[330px]">
                                        {{ $fileName }}
                                    </span>
                                </a>
                            @else
                                <div class="text-gray-500 italic">Tidak ada dokumen dilampirkan.</div>
                            @endif
                        </div>

                        {{-- Deskripsi --}}
                        <div class="md:col-span-2">
                            <span class="block font-semibold text-blue-700 mb-1">Deskripsi Project</span>
                            <div class="bg-white border rounded-md px-4 py-3 shadow-sm text-sm leading-relaxed">
                                {{ $projectRequest->description ?? 'Tidak ada deskripsi.' }}
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Pengaturan Project --}}
                <h3 class="text-xl font-bold text-gray-700 border-b pb-2">Tetapkan Detail Project</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Tanggal Mulai --}}
                    <div>
                        <label for="start_date_project" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mulai Project</label>
                        <input type="date" name="start_date_project" id="start_date_project"
                            value="{{ old('start_date_project') }}" required
                            class="block w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Tanggal Selesai --}}
                    <div>
                        <label for="finish_date_project" class="block text-sm font-semibold text-gray-700 mb-1">Target Tanggal Selesai</label>
                        <input type="date" name="finish_date_project" id="finish_date_project"
                            value="{{ old('finish_date_project') }}" required
                            class="block w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                </div>

                {{--  Alokasi Karyawan --}}
                <h3 class="text-xl font-bold text-gray-700 border-b pb-2 pt-4">Alokasi Tim Karyawan</h3>
                <p class="text-sm text-gray-500 mb-4">Pilih karyawan yang sesuai untuk setiap peran yang dibutuhkan.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                    @foreach($requiredRoles as $index => $role)
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">{{ $role }}</label>
                            <select name="karyawan_ids[]" required
                                class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Pilih {{ $role }}</option>

                                @foreach($karyawans->where('job_title', $role) as $karyawan)
                                    <option value="{{ $karyawan->id }}" 
                                        {{ old("karyawan_ids.$index") == $karyawan->id ? 'selected' : '' }}>
                                        {{ $karyawan->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    @endforeach
                </div>

                {{-- Submit --}}
                <div class="pt-6 border-t mt-8">
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-extrabold py-3.5 px-6 rounded-xl shadow-lg shadow-green-400/50 transition hover:scale-[1.01] flex items-center justify-center">
                        <i class="fas fa-rocket mr-3"></i>
                        Konfirmasi Persetujuan & Buat Project
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

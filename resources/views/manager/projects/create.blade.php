@extends('layouts.manager')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Approve & Konfigurasi Project</h2>

    <div class="bg-white p-8 shadow-xl rounded-lg">
        <form action="{{ route('manager.projects.store') }}" method="POST">
            @csrf
            <input type="hidden" name="request_id" value="{{ $request->id }}">

            {{-- Informasi Project Request --}}
            <div class="mb-6 border-b pb-4">
                <p class="text-xl font-semibold text-blue-600 mb-2">{{ $request->client->name }}</p>
                <p class="text-gray-700">Deskripsi: {{ $request->description }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                {{-- Nama Project --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Project</label>
                    <input type="text" name="project_name"
                        value="{{ old('project_name', $request->project_name ?? $request->kategori) }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
                    @error('project_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Mulai --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="date" name="start_date_project"
                        value="{{ old('start_date_project') }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
                    @error('start_date_project')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Selesai --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <input type="date" name="finish_date_project"
                        value="{{ old('finish_date_project') }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
                    @error('finish_date_project')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Penugasan Karyawan --}}
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Penugasan Karyawan (5 Peran Wajib)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
                @foreach($requiredRoles as $index => $role)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ $role }}</label>
                        <select name="karyawan_ids[]" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
                            <option value="">Pilih {{ $role }}</option>
                            @foreach($karyawans->where('job_title', $role) as $karyawan)
                                <option value="{{ $karyawan->id }}" {{ old("karyawan_ids.$index") == $karyawan->id ? 'selected' : '' }}>
                                    {{ $karyawan->name }}
                                </option>
                            @endforeach
                        </select>
                        @error("karyawan_ids.$index")
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="pt-5 border-t mt-4">
                <button type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                    Konfirmasi Persetujuan & Buat Project
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

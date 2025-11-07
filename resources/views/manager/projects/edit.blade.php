@extends('layouts.app')

@section('content')

<div class="container mx-auto p-4">
<h2 class="text-3xl font-bold mb-6 text-gray-800">Edit Project: {{ $project->name_project }}</h2>

<div class="bg-white p-8 shadow-xl rounded-lg">
    <form action="{{ route('manager.projects.update', $project) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-6 border-b pb-4">
            <p class="text-xl font-semibold text-blue-600 mb-2">Client: {{ $project->client->name }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {{-- nama project --}}
            <div>
                <label for="name_project" class="block text-sm font-medium text-gray-700">Nama Project</label>
                <input type="text" name="name_project" id="name_project"
                    value="{{ old('name_project', $project->projectRequest->name_project) }}"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
                @error('name_project')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- start project --}}
            <div>
                <label for="start_date_project" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                <input type="date" name="start_date_project" id="start_date_project"
                    value="{{ old('start_date_project', $project->start_date_project ? \Carbon\Carbon::parse($project->start_date_project)->format('Y-m-d') : '') }}"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
                @error('start_date_project')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- finish project --}}
            <div>
                <label for="finish_date_project" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                <input type="date" name="finish_date_project" id="finish_date_project"
                    value="{{ old('finish_date_project', $project->finish_date_project ? \Carbon\Carbon::parse($project->finish_date_project)->format('Y-m-d') : '') }}"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
                @error('finish_date_project')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- karyawan --}}
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Penugasan Karyawan (5 Peran Wajib)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
            @foreach($requiredRoles as $index => $role)
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ $role }}</label>
                    <select name="karyawan_ids[]" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
                        <option value="">Pilih {{ $role }}</option>
                        @foreach($karyawans->where('job_title', $role) as $karyawan)
                            @php
                                // cek apa sudh dpt task
                                $isSelected = in_array($karyawan->id, $selectedKaryawanIds);
                                // cek apakah ada data lam
                                $isOldSelected = old("karyawan_ids.$index") == $karyawan->id;
                            @endphp
                            <option value="{{ $karyawan->id }}" {{ $isOldSelected || $isSelected ? 'selected' : '' }}>
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

        <div class="pt-5 border-t mt-4 flex justify-between">
            <a href="{{ route('manager.projects.index') }}" 
                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition duration-300 ease-in-out">
                Batal
            </a>
            <button type="submit" 
                class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                Update Project
            </button>
        </div>
    </form>
</div>


</div>
@endsection
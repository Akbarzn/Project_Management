@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-edit text-indigo-600"></i>
            Edit Project
        </h2>

        <a href="{{ route('manager.projects.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow">
            Kembali
        </a>
    </div>

    {{-- FORM --}}
    <form action="{{ route('manager.projects.update', $project->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- INFO PROJECT REQUEST (READONLY) --}}
        <div class="mb-6 p-4 bg-gray-50 border rounded-lg">
            <h3 class="font-semibold text-gray-800 mb-3">Informasi Project</h3>

            <p class="mb-2">
                <span class="font-semibold text-gray-700">Nama Project:</span><br>
                <span class="bg-gray-100 px-3 py-2 rounded block mt-1">
                    {{ $project->projectRequest->name_project }}
                </span>
            </p>

            <p class="mb-2">
                <span class="font-semibold text-gray-700">Client:</span><br>
                <span class="bg-gray-100 px-3 py-2 rounded block mt-1">
                    {{ $project->client->name }}
                </span>
            </p>

            <p>
                <span class="font-semibold text-gray-700">Deskripsi:</span><br>
                <span class="bg-gray-100 px-3 py-2 rounded block mt-1">
                    {{ $project->projectRequest->description }}
                </span>
            </p>
        </div>

        {{-- TANGGAL PROJECT --}}
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tanggal Project</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            {{-- Start --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                <input type="date" name="start_date_project"
                       value="{{ old('start_date_project', $project->start_date_project) }}"
                       class="w-full border-gray-300 rounded-lg px-3 py-2 mt-1 focus:border-indigo-600 focus:ring-indigo-600">
                @error('start_date_project')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Finish --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                <input type="date" name="finish_date_project"
                       value="{{ old('finish_date_project', $project->finish_date_project) }}"
                       class="w-full border-gray-300 rounded-lg px-3 py-2 mt-1 focus:border-indigo-600 focus:ring-indigo-600">
                @error('finish_date_project')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- KARYAWAN PER ROLE --}}
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Penugasan Karyawan</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-10">
            @foreach($requiredRoles as $index => $role)
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ $role }}</label>

                    <select name="karyawan_ids[]" class="w-full border-gray-300 rounded-lg px-3 py-2 mt-1 focus:border-indigo-600 focus:ring-indigo-600" required>
                        <option value="">Pilih {{ $role }}</option>

                        @foreach($karyawans->where('job_title', $role) as $karyawan)
                            <option value="{{ $karyawan->id }}"
                                {{ in_array($karyawan->id, $selectedKaryawanIds) ? 'selected' : '' }}>
                                {{ $karyawan->name }}
                            </option>
                        @endforeach
                    </select>

                    @error("karyawan_ids.$index")
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="border-t pt-6 flex justify-between">
            <a href="{{ route('manager.projects.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow">
                Batal
            </a>

            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow font-semibold">
                Update Project
            </button>
        </div>
    </form>

</div>
@endsection

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

    <form action="{{ route('manager.projects.update', $project->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- INFO PROJECT --}}
        <div class="mb-6 p-4 bg-gray-50 border rounded-lg">
            <h3 class="font-semibold text-gray-800 mb-3">Informasi Project</h3>

            {{-- Nama Project --}}
            <div class="mb-4">
                <label class="font-semibold text-gray-700">Nama Project:</label>
                <input type="text" name="name_project"
                    value="{{ old('name_project', $project->projectRequest->name_project) }}"
                    class="w-full border-gray-300 rounded-lg px-3 py-2 mt-1 focus:ring-indigo-600 focus:border-indigo-600">
                @error('name_project')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Client --}}
            <div class="mb-4">
                <label class="font-semibold text-gray-700">Client:</label>
                <select name="client_id"
                    class="w-full border-gray-300 rounded-lg px-3 py-2 mt-1 focus:ring-indigo-600 focus:border-indigo-600">
                    <option value="">— Pilih Client —</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" {{ $client->id == $project->client_id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
                @error('client_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="mb-4">
                <label class="font-semibold text-gray-700">Deskripsi:</label>
                <textarea name="description" rows="4"
                    class="w-full border-gray-300 rounded-lg px-3 py-2 mt-1 focus:ring-indigo-600 focus:border-indigo-600">{{ old('description', $project->projectRequest->description) }}</textarea>

                @error('description')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
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

        {{-- KARYAWAN JOBTITLE --}}
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Penugasan Karyawan</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-10">

            @foreach ($requiredJobTitle as $jobTitle)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $jobTitle }}</label>

                    <select name="karyawan_ids[]" class="w-full border-gray-300 rounded-lg px-2 py-2 focus:ring-indigo-600 focus:border-indigo-600">
                        <option value="">-- Pilih {{ $jobTitle }} --</option>

                        @foreach ($allKaryawans as $karyawan)
                            <option value="{{ $karyawan->id }}"
                                {{ isset($assigned[$jobTitle]) && $assigned[$jobTitle] == $karyawan->id ? 'selected' : '' }}>
                                {{ $karyawan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endforeach

        </div>

        {{-- BUTTON --}}
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

@extends('layouts.app')

@section('title', 'Update Progress Task')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-200">

    {{-- HEADER --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-edit text-indigo-600 mr-2"></i>
            Update Progress Task
        </h2>
    </div>

    {{-- PROJECT INFORMATION --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">

        <div class="bg-gray-50 p-4 rounded-lg border">
            <h3 class="font-semibold text-gray-700 mb-2 flex items-center">
                <i class="fas fa-project-diagram mr-2 text-indigo-600"></i> Informasi Project
            </h3>

            <p class="text-sm">
                <strong>Nama Project:</strong> 
                {{ $task->project->projectRequest->name_project ?? '-' }}
            </p>

            <p class="text-sm">
                <strong>Client:</strong> {{ $task->project->client->name ?? '-' }}
            </p>

            <p class="text-sm">
                <strong>Start Project:</strong>
                {{ $task->project->start_date_project ?: '-' }}
            </p>

            <p class="text-sm">
                <strong>Finish Project:</strong>
                {{ $task->project->finish_date_project ?: '-' }}
            </p>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg border">
            <h3 class="font-semibold text-gray-700 mb-2 flex items-center">
                <i class="fas fa-tasks mr-2 text-indigo-600"></i> Informasi Task
            </h3>

            <p class="text-sm">
                <strong>Task:</strong> {{ $task->task_name }}
            </p>

            <p class="text-sm">
                <strong>Tanggal Mulai Task:</strong>
                {{ $task->start_date_task ?? '-' }}
            </p>

            <p class="text-sm">
                <strong>Tanggal Selesai Task:</strong>
                {{ $task->finish_date_task ?? '-' }}
            </p>

            <p class="text-sm">
                <strong>Terakhir Diperbarui:</strong>
                {{ $task->updated_at->format('d M Y, H:i') }}
            </p>
        </div>

    </div>

    {{-- FORM --}}
    <form action="{{ route('karyawan.tasks.update', $task->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- PROGRESS INPUT --}}
        <div class="mb-6">
            <label for="progress" class="block text-sm font-semibold text-gray-700 mb-2">
                Progress Pekerjaan
            </label>

            <div class="flex items-center gap-4">
                <input type="range" name="progress" min="0" max="100"
                       value="{{ old('progress', $task->progress) }}"
                       class="w-full accent-indigo-600 cursor-pointer"
                       oninput="document.getElementById('progressValue').value = this.value">

                <input id="progressValue" type="number" min="0" max="100"
                       value="{{ old('progress', $task->progress) }}"
                       name="progress"
                       class="w-20 border-gray-300 rounded-md p-2 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>

        {{-- CATATAN --}}
        <div class="mb-6">
            <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-2">
                Catatan / Kendala
            </label>
            <textarea name="catatan" id="catatan" rows="4"
                class="w-full border-gray-300 rounded-lg p-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('catatan', $task->catatan) }}</textarea>
        </div>

        {{-- INPUT JAM KERJA --}}
        <div class="mb-6">
            <label for="hours" class="block text-sm font-semibold text-gray-700 mb-2">
                Jam Kerja Hari Ini (jam)
            </label>
            <input type="number" name="hours" id="hours" step="0.5" min="0" max="7"
                   value="{{ old('hours') }}"
                   class="w-full border-gray-300 rounded-md p-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

            <p class="text-xs text-gray-500 mt-1">
                <i class="fas fa-info-circle mr-1"></i>
                Maksimal total 7 jam per hari di semua project.
            </p>
        </div>

        {{-- BUTTONS --}}
        <div class="flex justify-end gap-3 mt-8">
            <a href="{{ route('karyawan.tasks.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded-lg shadow">
                <i class="fas fa-arrow-left mr-1"></i> Batal
            </a>

            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg shadow font-semibold">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
        </div>

    </form>
</div>

@endsection

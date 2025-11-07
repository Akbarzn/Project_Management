@extends('layouts.karyawan')

@section('title', 'Update Progress Task')

@section('content')
<div class="max-w-5xl mx-auto bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-bold text-indigo-700 mb-6">Update Progress Task</h2>

    <div class="mb-6">
        <p><strong>Nama Project:</strong> {{ $task->project->project_name ?? '-' }}</p>
        <p><strong>Client:</strong> {{ $task->project->client->name ?? '-' }}</p>
        <p><strong>Task:</strong> {{ $task->task_name }}</p>
        <p><strong>Start Project:</strong> {{ $task->project->start_date_project }}</p>
        <p><strong>Finish Project:</strong> {{ $task->project->finish_date_project }}</p>
    </div>

    <form action="{{ route('karyawan.tasks.update', $task->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Progress --}}
        <div class="mb-4">
            <label for="progress" class="block text-sm font-medium text-gray-700 mb-1">
                Progress Pekerjaan (%)
            </label>
            <input type="number" name="progress" id="progress" min="0" max="100"
                   value="{{ old('progress', $task->progress) }}"
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
        </div>

        {{-- Catatan --}}
        <div class="mb-4">
            <label for="note" class="block text-sm font-medium text-gray-700 mb-1">
                Catatan / Kendala
            </label>
            <textarea name="desc" id="note" rows="4"
                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">{{ old('desc', $task->desc) }}</textarea>
        </div>

   {{-- Input Jam Kerja --}}
<div class="mb-4">
    <label for="work_hours" class="block text-sm font-medium text-gray-700 mb-1">
        Jam Kerja Hari Ini (jam)
    </label>
    <input type="number" name="work_hours" id="work_hours" step="0.5" min="0" max="7"
        value="{{ old('work_hours') }}"
        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
    <p class="text-xs text-gray-500 mt-1">*Maksimal total 7 jam per hari di semua project.</p>
</div>



        <div class="mb-6 text-sm text-gray-600 bg-gray-50 p-3 rounded">
            <p><strong>Tanggal Mulai Task:</strong> {{ $task->start_date_task ?? '-' }}</p>
            <p><strong>Tanggal Selesai Task:</strong> {{ $task->finish_date_task ?? '-' }}</p>
            <p><strong>Terakhir Diperbarui:</strong> {{ $task->updated_at->format('d M Y, H:i') }}</p>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('karyawan.tasks.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                Batal
            </a>
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

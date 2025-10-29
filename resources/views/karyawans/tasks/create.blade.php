@extends('layouts.karyawan')

@section('title', 'Buat Task Baru')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-8">
    <h2 class="text-2xl font-bold mb-6 text-indigo-700">Buat Task Baru</h2>

    <form action="{{ route('karyawan.tasks.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Pilih Project</label>
            <select name="project_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                <option value="">-- Pilih Project --</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                @endforeach
            </select>
            @error('project_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Progress Awal (%)</label>
            <input type="number" name="progress" min="0" max="100" value="{{ old('progress', 0) }}"
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Catatan</label>
            <textarea name="note" rows="3"
                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">{{ old('note') }}</textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2 rounded">
                Simpan Task
            </button>
        </div>
    </form>
</div>
@endsection

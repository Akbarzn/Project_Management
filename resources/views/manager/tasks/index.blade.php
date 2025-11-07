@extends('layouts.app')
@section('title', 'Daftar Task Semua Karyawan')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">📋 Semua Task</h2>

    <!-- filter -->
    <form method="GET" class="flex flex-wrap gap-2 mb-4">
        <input type="text" name="search" placeholder="Cari Project atau Karyawan..." value="{{ request('search') }}" class="border rounded px-3 py-1">
        <select name="project_id" class="border rounded px-3 py-1">
            <option value="">Semua Proyek</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }} class="px-2">
                    {{ $project->projectRequest->name_project }}
                </option>
            @endforeach
        </select>

        <select name="karyawan_id" class="border rounded px-3 py-1">
            <option value="" >Semua Karyawan</option>
            @foreach ($karyawans as $karyawan)
                <option value="{{ $karyawan->id }}" {{ request('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                    {{ $karyawan->user->name }}
                </option>
            @endforeach
        </select>

        <select name="status" class="border rounded px-3 py-1">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="progress" {{ request('status') == 'inwork' ? 'selected' : '' }}>Progress</option>
            <option value="done" {{ request('status') == 'complete' ? 'selected' : '' }}>Selesai</option>
        </select>

        <button type="submit" class="bg-indigo-600 text-white px-4 py-1 rounded">Filter</button>
    </form>

    {{-- table --}}
    <div class="overflow-x-auto">
        <table class="w-full border text-sm text-center">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">No</th>
                    <th class="p-2 border">Karyawan</th>
                    <th class="p-2 border">Proyek</th>
                    <th class="p-2 border">Status</th>
                    <th class="p-2 border">Start Project</th>
                    <th class="p-2 border">Finish Project</th>
                    {{-- <th class="p-2 border">Aksi</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $task)
                    <tr class="hover:bg-gray-50">
                        <td class="border p-2">{{ $loop->iteration }}</td>
                        <td class="border p-2">{{ $task->karyawan->user->name ?? '-' }}</td>
                        <td class="border p-2">{{ $task->project->projectRequest->name_project ?? '-' }}</td>
                        <td class="border p-2">{{ ucfirst($task->status) }}</td>
                        <td class="border p-2">{{ $task->created_at->format('d M Y') }}</td>
                        <td>{{ $task->project->finish_date_project }}</td>
                        {{-- <td class="border p-2 text-center">
                            <a href="{{ route('manager.tasks.show', $task->id) }}" class="text-indigo-600 hover:underline">Detail</a>
                        </td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tasks->links() }}</div>
</div>
@endsection

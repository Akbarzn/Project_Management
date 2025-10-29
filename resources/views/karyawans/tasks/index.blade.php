@extends('layouts.karyawan')

@section('title', 'Daftar Tugas')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-bold mb-6 text-indigo-700">Daftar Tugas Saya</h2>

    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Nama Project</th>
                <th class="px-4 py-2 text-left">Client/Requestor</th>
                <th class="px-4 py-2 text-left">Task</th>
                <th class="px-4 py-2 text-center">Progress</th>
                <th class="px-4 py-2 text-center">Status</th>
                <th class="px-4 py-2 text-center">Jam Kerja</th>
                <th class="px-4 py-2 text-left">Catatan</th>
                <th class="px-4 py-2 text-center">Start Task</th>
                <th class="px-4 py-2 text-center">Finish Task</th>
                <th class="px-4 py-2 text-center">Start Project</th>
                <th class="px-4 py-2 text-center">Finish Project</th>
                <th class="px-4 py-2 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse($tasks as $task)
                @php
                    // hitung total jam kerja dari tabel task_work_logs
                    $totalHours = $task->workLogs->sum('hours');
                @endphp
                <tr class="border-t hover:bg-gray-50">
                    {{-- Nama Project --}}
                    <td class="px-4 py-2">
                        {{ $task->project->project_name ?? optional($task->project->projectRequest)->name_project ?? '-' }}
                    </td>

                    {{-- Client & Requestor --}}
                    <td class="px-4 py-2">
                        <span class="font-medium">{{ $task->project->client->name ?? '-' }}</span>
                        @if ($task->project && $task->project->projectRequest)
                            <p class="text-sm text-gray-500">
                                Req: {{ $task->project->projectRequest->name ?? 'N/A' }}
                            </p>
                        @endif
                    </td>

                    {{-- Task (Jabatan) --}}
                    <td class="px-4 py-2">{{ $task->karyawan->job_title }}</td>

                    {{-- Progress Bar --}}
                    <td class="px-4 py-2 text-center">
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full
                                {{ $task->progress < 30 ? 'bg-red-500' : ($task->progress < 70 ? 'bg-yellow-400' : 'bg-green-500') }}"
                                style="width: {{ $task->progress }}%">
                            </div>
                        </div>
                        <span class="text-sm text-gray-600">{{ $task->progress }}%</span>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-2 text-center">
                        @if ($task->status == 'pending')
                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full font-semibold">Pending</span>
                        @elseif ($task->status == 'inwork')
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full font-semibold">In Work</span>
                        @elseif ($task->status == 'complete')
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full font-semibold">Selesai</span>
                        @else
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full font-semibold">-</span>
                        @endif
                    </td>

                    {{-- Jam Kerja --}}
                    <td class="px-4 py-2 text-center">
                        {{ $totalHours > 0 ? $totalHours . ' jam' : '-' }}
                    </td>

                    {{-- Catatan --}}
                    <td class="px-4 py-2 text-sm">{{ Str::limit($task->description_task, 50) ?? '-' }}</td>

                    {{-- Start Task --}}
                    <td class="px-4 py-2 text-center text-sm text-gray-600">
                        {{ $task->start_date_task ? \Carbon\Carbon::parse($task->start_date_task)->format('d M Y') : '-' }}
                    </td>

                    {{-- Finish Task --}}
                    <td class="px-4 py-2 text-center text-sm text-gray-600">
                        {{ $task->finish_date_task ? \Carbon\Carbon::parse($task->finish_date_task)->format('d M Y') : '-' }}
                    </td>

                    {{-- Start Project --}}
                    <td class="px-4 py-2 text-center text-sm text-gray-600">
                        {{ $task->project->start_date_project ? \Carbon\Carbon::parse($task->project->start_date_project)->format('d M Y') : '-' }}
                    </td>

                    {{-- Finish Project --}}
                    <td class="px-4 py-2 text-center text-sm text-gray-600">
                        {{ $task->project->finish_date_project ? \Carbon\Carbon::parse($task->project->finish_date_project)->format('d M Y') : '-' }}
                    </td>

                    {{-- Aksi --}}
                    <td class="px-4 py-2 text-center space-y-2">
                        <a href="{{ route('karyawan.tasks.edit', $task->id) }}"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm inline-block">
                            Update
                        </a>
                        <a href="{{ route('karyawan.tasks.logs', $task->id) }}"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm inline-block">
                            Riwayat
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center py-6 text-gray-500">
                        Belum ada tugas yang tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

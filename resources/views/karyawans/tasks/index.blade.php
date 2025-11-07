@extends('layouts.app')

@section('title', 'Daftar Tugas')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6 overflow-hidden">
    <h2 class="text-2xl font-bold mb-6 text-indigo-700">Daftar Tugas Saya</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full table-fixed border border-gray-200 rounded-lg">
            <thead class="bg-gray-100 text-sm">
                <tr>
                    <th class="px-2 py-2 text-left w-1/6">Nama Project</th>
                    <th class="px-4 py-2 text-left w-1/6">Client / Requestor</th>
                    <th class="px-4 py-2 text-left w-1/6">Task</th>
                    <th class="px-4 py-2 text-center w-1/12">Progress</th>
                    <th class="px-4 py-2 text-center w-1/12">Status</th>
                    <th class="px-4 py-2 text-center w-1/12">Jam Kerja</th>
                    <th class="px-4 py-2 text-left w-1/6">Catatan</th>
                    <th class="px-4 py-2 text-center w-1/12">Start Task</th>
                    <th class="px-4 py-2 text-center w-1/12">Finish Task</th>
                    <th class="px-4 py-2 text-center w-1/12">Start Project</th>
                    <th class="px-4 py-2 text-center w-1/12">Finish Project</th>
                    <th class="px-4 py-2 text-center w-1/12">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                @forelse($tasks as $task)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 break-words">{{ $task->project->project_name ?? optional($task->project->projectRequest)->name_project ?? '-' }}</td>
                        <td class="px-4 py-2 break-words">
                            <span class="font-medium">{{ $task->project->client->name ?? '-' }}</span>
                            @if ($task->project && $task->project->projectRequest)
                                <p class="text-sm text-gray-500">
                                    {{-- Req: {{ $task->project->projectRequest->name ?? 'N/A' }} --}}
                                </p>
                            @endif
                        </td>
                        <td class="px-4 py-2 break-words">{{ $task->karyawan->job_title }}</td>
                        <td class="px-4 py-2 text-center">
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="h-3 rounded-full
                                    {{ $task->progress < 30 ? 'bg-red-500' : ($task->progress < 70 ? 'bg-yellow-400' : 'bg-green-500') }}"
                                    style="width: {{ $task->progress }}%">
                                </div>
                            </div>
                            <span class="text-xs text-gray-600">{{ $task->progress }}%</span>
                        </td>
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
                        <td class="px-4 py-2 text-center">
                            {{ $task->workLogs->sum('hours') > 0 ? $task->workLogs->sum('hours') . ' jam' : '-' }}
                        </td>
                        <td class="px-4 py-2 text-sm break-words">{{ Str::limit($task->description_task, 50) ?? '-' }}</td>
                        <td class="px-4 py-2 text-center text-xs">{{ $task->start_date_task ? \Carbon\Carbon::parse($task->start_date_task)->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-2 text-center text-xs">{{ $task->finish_date_task ? \Carbon\Carbon::parse($task->finish_date_task)->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-2 text-center text-xs">{{ $task->project->start_date_project ? \Carbon\Carbon::parse($task->project->start_date_project)->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-2 text-center text-xs">{{ $task->project->finish_date_project ? \Carbon\Carbon::parse($task->project->finish_date_project)->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-2 text-center space-y-2">
                            <a href="{{ route('karyawan.tasks.edit', $task->id) }}"
                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm inline-block">Update</a>
                            <a href="{{ route('karyawan.tasks.logs', $task->id) }}"
                               class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm inline-block">Riwayat</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center py-6 text-gray-500">Belum ada tugas yang tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

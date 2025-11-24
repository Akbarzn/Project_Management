@extends('layouts.app')

@section('title', 'Daftar Tugas')

@section('content')

<div class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow-lg border border-gray-200">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-tasks mr-2 text-indigo-600"></i> Daftar Tugas Saya
        </h2>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-indigo-600">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Project</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Client</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Task Role</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-white">Progress</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-white">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-white">Jam Kerja</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Catatan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-white">Start Task</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-white">Finish Task</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-white">Start Project</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-white">Finish Project</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-white">Aksi</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-100 text-sm">

                @forelse ($tasks as $task)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- Project --}}
                        <td class="px-4 py-3 font-semibold text-gray-800">
                            {{ $task->project->projectRequest->name_project ?? '-' }}
                        </td>

                        {{-- Client --}}
                        <td class="px-4 py-3 text-gray-700">
                            {{ $task->project->client->name ?? '-' }}
                        </td>

                        {{-- Role --}}
                        <td class="px-4 py-3 text-gray-700">
                            <span class="font-medium">{{ $task->karyawan->job_title }}</span>
                        </td>

                        {{-- Progress --}}
                        <td class="px-4 py-3 text-center">
                            <div class="w-full max-w-[120px] mx-auto bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full
                                    @if($task->progress < 30) bg-red-500
                                    @elseif($task->progress < 70) bg-yellow-400
                                    @else bg-green-500 @endif"
                                    style="width: {{ $task->progress }}%"></div>
                            </div>
                            <span class="text-xs text-gray-600">{{ $task->progress }}%</span>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3 text-center">
                            @php
                                $badge = [
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'inwork' => 'bg-blue-100 text-blue-700',
                                    'complete' => 'bg-green-100 text-green-700',
                                ][$task->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp

                            <span class="px-2 py-1 text-xs rounded-full font-semibold {{ $badge }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </td>

                        {{-- Jam Kerja --}}
                        <td class="px-4 py-3 text-center text-gray-700">
                            {{ $task->workLogs->sum('hours') ?: 0 }} jam
                        </td>

                        {{-- Catatan + truncate --}}
                        <td class="px-4 py-3 max-w-xs truncate group relative">
                            {{ Str::limit($task->catatan, 40) ?? '-' }}

                            @if($task->catatan)
                                <div class="absolute hidden group-hover:block bg-gray-900 text-white text-xs rounded px-3 py-2 w-64 left-0 top-8 shadow-lg z-50">
                                    {{ $task->catatan }}
                                </div>
                            @endif
                        </td>

                        {{-- Dates --}}
                        <td class="px-4 py-3 text-center">
                            {{ $task->start_date_task ? \Carbon\Carbon::parse($task->start_date_task)->format('d M Y') : '-' }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            {{ $task->finish_date_task ? \Carbon\Carbon::parse($task->finish_date_task)->format('d M Y') : '-' }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            {{ $task->project->start_date_project ? \Carbon\Carbon::parse($task->project->start_date_project)->format('d M Y') : '-' }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            {{ $task->project->finish_date_project ? \Carbon\Carbon::parse($task->project->finish_date_project)->format('d M Y') : '-' }}
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-3 text-center">
                            <div class="flex flex-col gap-2 items-center">

                                {{-- Update --}}
                                <a href="{{ route('karyawan.tasks.edit', $task->id) }}"
                                   class="flex items-center bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-3 py-1 rounded shadow">
                                    <i class="fas fa-edit mr-1"></i> Update
                                </a>

                                {{-- Riwayat --}}
                                <a href="{{ route('karyawan.tasks.logs', $task->id) }}"
                                   class="flex items-center bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded shadow">
                                    <i class="fas fa-history mr-1"></i> Riwayat
                                </a>

                            </div>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="12" class="text-center py-8 text-gray-500 text-sm">
                            Tidak ada tugas ditemukan.
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

</div>

@endsection

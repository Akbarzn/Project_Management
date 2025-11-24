@extends('layouts.app')

@section('title', 'Detail Task')

@section('content')

<div class="max-w-4xl mx-auto mt-10">

    {{-- CARD WRAPPER --}}
    <div class="bg-white/90 backdrop-blur-lg shadow-2xl rounded-2xl overflow-hidden border border-gray-200">

        {{-- HEADER --}}
        <div class="bg-indigo-600 px-6 py-5 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-tasks"></i>
                Detail Task
            </h2>

        </div>

        {{-- CONTENT --}}
        <div class="p-8 space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Nama Task -->
                <div class="bg-gray-50 p-5 rounded-xl border">
                    <div class="text-sm text-gray-600 flex items-center gap-2">
                        <i class="fas fa-tag text-indigo-500"></i> Nama Task
                    </div>
                    <p class="mt-1 text-lg font-semibold text-gray-800">
                        {{ $task->karyawan->job_title }}
                    </p>
                </div>

                <!-- Karyawan -->
                <div class="bg-gray-50 p-5 rounded-xl border">
                    <div class="text-sm text-gray-600 flex items-center gap-2">
                        <i class="fas fa-user text-indigo-500"></i> Karyawan
                    </div>
                    <p class="mt-1 text-lg font-semibold text-gray-800">
                        {{ $task->karyawan->name ?? '-' }}
                    </p>
                </div>

                <!-- Project -->
                <div class="bg-gray-50 p-5 rounded-xl border md:col-span-2">
                    <div class="text-sm text-gray-600 flex items-center gap-2">
                        <i class="fas fa-folder-open text-indigo-500"></i> Project
                    </div>
                    <p class="mt-1 text-lg font-semibold text-gray-800">
                        {{ $task->project->projectRequest->name_project ?? '-' }}
                    </p>
                </div>

            </div>

            {{-- PROGRESS --}}
            <div class="p-5 bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-xl border">
                <div class="flex justify-between">
                    <span class="text-sm font-semibold text-gray-700">
                        Progress Task
                    </span>
                    <span class="text-sm font-bold text-indigo-700">{{ $task->progress }}%</span>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-3 mt-2">
                    <div
                        class="h-3 rounded-full transition-all duration-700 bg-indigo-600"
                        style="width: {{ $task->progress }}%">
                    </div>
                </div>
            </div>

            {{-- TASK + PROJECT DATES --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Task Dates -->
                <div class="bg-white p-5 shadow-sm border rounded-xl">
                    <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-indigo-500"></i>
                        Waktu Task
                    </h3>

                    <div class="space-y-2 text-sm">
                        <p>
                            <span class="text-gray-600">Mulai Task:</span><br>
                            <span class="font-semibold text-gray-800">{{ $task->start_date_task ?? '-' }}</span>
                        </p>

                        <p>
                            <span class="text-gray-600">Selesai Task:</span><br>
                            <span class="font-semibold text-gray-800">{{ $task->finish_date_task ?? '-' }}</span>
                        </p>
                    </div>
                </div>

                <!-- Project Dates -->
                <div class="bg-white p-5 shadow-sm border rounded-xl">
                    <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-calendar-check text-indigo-500"></i>
                        Waktu Project
                    </h3>

                    <div class="space-y-2 text-sm">
                        <p>
                            <span class="text-gray-600">Mulai Project:</span><br>
                            <span class="font-semibold text-gray-800">
                                {{ $task->project->start_date_project ?? '-' }}
                            </span>
                        </p>

                        <p>
                            <span class="text-gray-600">Target Selesai Project:</span><br>
                            <span class="font-semibold text-gray-800">
                                {{ $task->project->finish_date_project ?? '-' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- CATATAN --}}
            <div>
                <h3 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-indigo-500"></i>
                    Catatan / Deskripsi
                </h3>
                <p class="bg-gray-50 p-4 rounded-xl border text-sm text-gray-800">
                    {{ $task->catatan ?? 'Tidak ada catatan.' }}
                </p>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="p-6 bg-gray-50 border-t flex justify-end">
            <a href="{{ route('manager.tasks.index') }}"
               class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 shadow-md transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>

    </div>
</div>

@endsection

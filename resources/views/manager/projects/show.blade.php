@extends('layouts.app')

@section('title', 'Detail Project')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-10">

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- HEADER CARD                                                --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-200">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <i class="fas fa-folder-open text-indigo-600"></i>
                    {{ $project->projectRequest?->name_project ?? 'Tanpa Nama' }}
                </h2>
                <p class="text-gray-500 mt-1">Detail lengkap informasi proyek</p>
            </div>

            <a href="{{ route('manager.projects.index') }}"
                class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg shadow hover:bg-indigo-700 transition">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mt-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('warning'))
            <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                <p class="text-yellow-700 font-medium">{{ session('warning') }}</p>
            </div>
        @endif

        {{-- Informasi Project --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">

            {{-- Client --}}
            <div class="bg-gray-50 p-5 rounded-xl border flex flex-col">
                <span class="text-sm font-semibold text-gray-600">Client</span>
                <span class="mt-1 text-gray-800 font-bold text-lg">
                    {{ $project->client->name ?? '-' }}
                </span>
            </div>

            {{-- Start Date --}}
            <div class="bg-gray-50 p-5 rounded-xl border flex flex-col">
                <span class="text-sm font-semibold text-gray-600">Tanggal Mulai</span>
                <span class="mt-1 text-gray-800 font-bold text-lg">
                    {{ $project->start_date_project ?? '-' }}
                </span>
            </div>

            {{-- Finish Date --}}
            <div class="bg-gray-50 p-5 rounded-xl border flex flex-col">
                <span class="text-sm font-semibold text-gray-600">Tanggal Selesai</span>
                <span class="mt-1 text-gray-800 font-bold text-lg">
                    {{ $project->finish_date_project ?? '-' }}
                </span>
            </div>

            {{-- Status --}}
            <div class="bg-gray-50 p-5 rounded-xl border flex flex-col">
                <span class="text-sm font-semibold text-gray-600">Status</span>
                <span class="mt-1 text-white font-semibold px-3 py-1 rounded-full text-center
                    @if($project->status === 'complete') bg-green-600
                    @elseif($project->status === 'ongoing') bg-yellow-500
                    @else bg-red-500 @endif">
                    {{ ucfirst($project->status) }}
                </span>
            </div>

            {{-- Deskripsi --}}
            <div class="md:col-span-2 bg-gray-50 p-5 rounded-xl border">
                <span class="text-sm font-semibold text-gray-600">Deskripsi Project</span>
                <p class="text-gray-800 mt-2">{{ $project->projectRequest?->description ?? '-' }}</p>
            </div>

        </div>

        {{-- TOTAL COST --}}
        <div class="mt-8">
            <p class="text-xl font-bold text-green-600">
                Total Cost: Rp {{ number_format($project->total_cost, 0, ',', '.') }}
            </p>
            <p class="text-gray-500 text-sm">Dihitung otomatis berdasarkan aktivitas karyawan</p>
        </div>

        {{-- PROGRESS BAR --}}
        <div class="mt-6">
            <div class="flex justify-between items-center mb-1">
                <h3 class="font-semibold text-gray-700">Progress Proyek</h3>
                <span class="font-bold text-indigo-600">{{ $totalProgress }}%</span>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="h-4 bg-gradient-to-r from-indigo-500 to-indigo-700 rounded-full transition-all"
                    style="width: {{ $totalProgress }}%">
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- TIM OTOMATIS TERPILIH                                      --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($autoTeam->isNotEmpty())
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-indigo-200">

        <div class="bg-indigo-700 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-robot text-white text-xl"></i>
            <div>
                <h3 class="text-lg text-white font-bold">Tim Otomatis Terpilih</h3>
                <p class="text-indigo-200 text-xs">Dibentuk oleh sistem menggunakan algoritma Least Load</p>
            </div>
        </div>

        @php
            $projectRequest = $project->projectRequest;
        @endphp

        {{-- Parameter Summary --}}
        @if($projectRequest)
        <div class="px-6 py-3 bg-indigo-50 border-b border-indigo-100 flex flex-wrap gap-4 text-xs">
            <span class="text-indigo-700">
                <strong>Priority:</strong> {{ $projectRequest->priority_label }}
            </span>
            <span class="text-indigo-700">
                <strong>Difficulty:</strong> {{ $projectRequest->difficulty_label }}
            </span>
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-indigo-600 text-white uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-left">Nama Karyawan</th>
                        <th class="px-4 py-3 text-center">Level</th>
                        <th class="px-4 py-3 text-left">Skill</th>
                        <th class="px-4 py-3 text-center">Active Projects</th>
                        <th class="px-4 py-3 text-center">Workload Status</th>
                        <th class="px-4 py-3 text-center">Fallback</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach($autoTeam as $member)
                    <tr class="hover:bg-indigo-50 transition {{ $member['fallback_used'] ? 'bg-yellow-50' : '' }}">

                        {{-- Role --}}
                        <td class="px-4 py-3 font-semibold text-indigo-800">
                            {{ $member['role'] }}
                        </td>

                        {{-- Nama --}}
                        <td class="px-4 py-3 font-semibold text-gray-900">
                            {{ $member['name'] }}
                        </td>

                        {{-- Level --}}
                        <td class="px-4 py-3 text-center">
                            @php
                                $levelColor = match($member['level']) {
                                    'Junior'       => 'bg-gray-100 text-gray-700',
                                    'Intermediate' => 'bg-blue-100 text-blue-700',
                                    'Senior'       => 'bg-purple-100 text-purple-700',
                                    'Lead'         => 'bg-indigo-100 text-indigo-700',
                                    default        => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $levelColor }}">
                                {{ $member['level'] }}
                            </span>
                        </td>

                        {{-- Skill --}}
                        <td class="px-4 py-3 text-gray-600 text-xs">
                            {{ $member['skills_text'] ?: '-' }}
                        </td>

                        {{-- Active Projects --}}
                        <td class="px-4 py-3 text-center text-gray-700">
                            {{ $member['active_projects'] }}
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3 text-center">
                            @php
                                $status = $member['workload_status'];
                                $badgeColor = match($status) {
                                    'Overload'        => 'bg-red-100 text-red-800 border-red-200',
                                    'Tinggi'          => 'bg-orange-100 text-orange-800 border-orange-200',
                                    'Normal'          => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'Ringan'          => 'bg-green-100 text-green-800 border-green-200',
                                    default           => 'bg-gray-100 text-gray-800 border-gray-200',
                                };
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                {{ $status }}
                            </span>
                        </td>

                        {{-- Fallback --}}
                        <td class="px-4 py-3 text-center">
                            @if($member['fallback_used'])
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full"
                                      title="{{ $member['fallback_note'] }}">
                                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                    Fallback
                                </span>
                                <p class="text-xs text-yellow-600 mt-1">{{ $member['fallback_note'] }}</p>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                    <i class="fas fa-check mr-1"></i> Optimal
                                </span>
                            @endif
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- BREAKDOWN COST TABLE                                       --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">

        <div class="bg-indigo-600 px-6 py-4">
            <h3 class="text-lg text-white font-bold flex items-center gap-2">
                <i class="fas fa-coins"></i>
                Breakdown Biaya per Karyawan & Task
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">

                <thead class="bg-indigo-500 text-white uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Karyawan</th>
                        <th class="px-4 py-3 text-left">Job Title</th>
                        <th class="px-4 py-3 text-left">Catatan</th>
                        <th class="px-4 py-3 text-center">Progress</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Durasi</th>
                        <th class="px-4 py-3 text-center">Jam Kerja</th>
                        <th class="px-4 py-3 text-right">Cost / Jam</th>
                        <th class="px-4 py-3 text-right">Total Biaya</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @foreach ($tasks as $index => $task)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $task['karyawan'] }}</td>
                            <td class="px-4 py-3">{{ $task['job_title'] }}</td>
                            <td class="px-4 py-3">{{ $task['catatan'] }}</td>

                            <td class="px-4 py-3 text-center">
                                <span class="px-3 py-1 rounded-full text-white text-xs font-semibold
                                    @if($task['progress'] == 100) bg-green-600
                                    @elseif($task['progress'] > 0) bg-yellow-500
                                    @else bg-gray-400 @endif">
                                    {{ $task['progress'] }}%
                                </span>
                            </td>

                            <td class="px-4 py-3 text-center capitalize">{{ $task['status'] }}</td>
                            <td class="px-4 py-3 text-center">{{ $task['days'] }}</td>
                            <td class="px-4 py-3 text-center">{{ $task['hours'] }}</td>

                            <td class="px-4 py-3 text-right">
                                Rp {{ number_format($task['costPerHour'], 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-3 text-right font-bold text-indigo-600">
                                Rp {{ number_format($task['totalCost'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach

                    {{-- GRAND TOTAL --}}
                    <tr class="bg-green-100 font-bold text-green-700">
                        <td colspan="9" class="px-4 py-3 text-right">Grand Total</td>
                        <td class="px-4 py-3 text-right">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection

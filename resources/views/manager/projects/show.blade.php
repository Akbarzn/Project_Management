@extends('layouts.app')

@section('title', 'Detail Project')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-10">

    <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-200">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <i class="fas fa-folder-open text-indigo-600"></i>
                    {{ $project->projectRequest->name_project ?? 'Tanpa Nama' }}
                </h2>
                <p class="text-gray-500 mt-1">Detail lengkap informasi proyek</p>
            </div>

            <a href="{{ route('manager.projects.index') }}"
                class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg shadow hover:bg-indigo-700 transition">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

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

            {{-- Catatan --}}
            <div class="md:col-span-2 bg-gray-50 p-5 rounded-xl border">
                <span class="text-sm font-semibold text-gray-600">Deskripsi Project</span>
                <p class="text-gray-800 mt-2">{{ $project->projectRequest->description ?? '-' }}</p>
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

    {{-- BREAKDOWN COST TABLE  --}}
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

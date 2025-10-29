@extends('layouts.manager')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-8">

    {{-- Header Proyek --}}
    <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200">
        <div class="flex item-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                {{ $project->projectRequest->name_project ?? 'Tanpa Nama' }}
            </h2>
            <a href="{{ route('manager.projects.index') }}" class="bg-indigo-500 text-center px-4 py-2 mb-2 rounded-sm text-white bg-shadow-white">Kembali</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-gray-700">
            <p><span class="font-semibold">Klien:</span> {{ $project->client->name ?? '-' }}</p>
            <p><span class="font-semibold">Tanggal Mulai:</span> {{ $project->start_date_project }}</p>
            <p><span class="font-semibold">Tanggal Selesai:</span> {{ $project->finish_date_project ?? '-' }}</p>
            <p><span class="font-semibold">Status:</span> {{ ucfirst($project->status) }}</p>
            <p><span class="font-semibold">Catatan:</span> {{ $project->projectRequest->description ?? '-' }}</p>
        </div>

        <div class="mt-6">
            <p class="text-lg font-semibold text-green-600">
                Data Total Cost di Database :
                Rp {{ number_format($project->total_cost, 0, ',', '.') }}
            </p>
            <span class="text-sm text-red-400">Data diperbarui otomatis setiap kali karyawan update progress task.</span>
             {{-- 📈 Progress Keseluruhan Proyek --}}
            @php
                $totalProgress = $project->tasks->count() > 0 
                    ? round($project->tasks->avg('progress'), 2)
                    : 0;
            @endphp
             <div class="mt-4">
                <p class="font-semibold text-gray-700 mb-1">
                    Progress Proyek: <span class="text-indigo-600">{{ $totalProgress }}%</span>
                </p>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="h-4 rounded-full transition-all duration-500 
                        @if($totalProgress == 100) bg-green-500 
                        @elseif($totalProgress >= 50) bg-blue-500 
                        @else bg-yellow-400 @endif"
                        style="width: {{ $totalProgress }}%">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Breakdown Table --}}
    <div class="bg-white shadow-lg rounded-2xl border border-gray-200 overflow-hidden">
        <div class="bg-indigo-500 px-6 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-white">Breakdown Biaya per Karyawan & Task</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse text-sm">
                <thead class="bg-indigo-400 border-b">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-white">No</th>
                        <th class="px-4 py-2 text-left font-medium text-white">Nama Karyawan</th>
                        <th class="px-4 py-2 text-left font-medium text-white">Job Title</th>
                        <th class="px-4 py-2 text-left font-medium text-white">Catatan</th>
                        <th class="px-4 py-2 text-left font-medium text-white">Progress</th>
                        <th class="px-4 py-2 text-left font-medium text-white">Statu</th>
                        <th class="px-4 py-2 text-center font-medium text-white">Durasi (hari kerja)</th>
                        <th class="px-4 py-2 text-center font-medium text-white">Jam Kerja</th>
                        <th class="px-4 py-2 text-right font-medium text-white">Cost per Jam</th>
                        <th class="px-4 py-2 text-right font-medium text-white">Total Biaya</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @foreach ($project->tasks as $task)
                        @php
                            // total jam kerja untuk task ini
                            $totalHours = $task->workLogs->sum('hours');
                            $costPerHour = $task->karyawan->cost ?? 0;
                            $totalCost = $totalHours * $costPerHour;

                            // hitung hanya hari kerja (Senin–Jumat)
                            $durationDays = $task->workLogs
                                ->pluck('work_date')
                                ->unique()
                                ->filter(function ($date) {
                                    $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;
                                    return $dayOfWeek !== 6 && $dayOfWeek !== 0; // skip Sabtu & Minggu
                                })
                                ->count();
                        @endphp

                        <tr>
                            <td class="px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2">{{ $task->karyawan->name }}</td>
                            <td class="px-4 py-2">{{ $task->karyawan->job_title }}</td>
                            <td class="px-4 py-2">{{ $task->description_task }}</td>
                            <td class="text-center px-4 py-2 w-32">
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="h-3 rounded-full
                                        @if($task->progress == 100) bg-green-500
                                        @elseif($task->progress >= 50) bg-blue-500
                                        @else bg-yellow-400 @endif"
                                        style="width: {{ $task->progress }}%">
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600">{{ $task->progress }}</span>
                            </td>

                           {{-- Status --}}
                            <td class="text-center px-4 py-2">
                                <span class="
                                    px-2 py-1 rounded text-white text-xs font-small
                                    @if($task->status === 'complete') bg-green-500
                                    @elseif($task->status === 'inwork') bg-blue-500
                                    @elseif($task->status === 'pending') bg-gray-400
                                    @else bg-yellow-400 @endif
                                ">
                                    {{ ucfirst($task->status) }}
                            </span>
                            <td class="text-center px-4 py-2">{{ $durationDays }}</td>
                            <td class="text-center px-4 py-2">{{ $totalHours ?: '-' }}</td>
                            <td class="text-right px-4 py-2">
                                Rp {{ number_format($costPerHour, 0, ',', '.') }}
                            </td>
                            <td class="text-right px-4 py-2 font-semibold">
                                Rp {{ number_format($totalCost, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach

                    {{-- Baris Total --}}
                    @php
                        $grandTotal = $project->tasks->sum(function ($task) {
                            return $task->workLogs->sum('hours') * ($task->karyawan->cost ?? 0);
                        });
                    @endphp
                    <tr class="bg-green-50 font-semibold">
                        <td colspan="7" class="text-right px-4 py-3">Total Semua Cost</td>
                        <td class="text-right px-4 py-3">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

{{-- Auto-refresh tiap 1 menit --}}
<script>
    setInterval(() => window.location.reload(), 60000);
</script>

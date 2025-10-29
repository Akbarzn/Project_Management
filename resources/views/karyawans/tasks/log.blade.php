@extends('layouts.karyawan')

@section('title', 'Riwayat Task')

@section('content')
<div class="max-w-5xl mx-auto p-6 space-y-6">

    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">
            🕓 Riwayat Task — {{ $task->project->project_name ?? 'Tanpa Nama Project' }}
        </h2>

        <a href="{{ route('karyawan.tasks.index') }}"
           class="text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1.5 rounded-lg">
           ⬅️ Kembali
        </a>
    </div>

    <div class="bg-white shadow border border-gray-200 rounded-2xl p-5 space-y-3">
        <h3 class="text-lg font-semibold text-gray-700 mb-3">
            Detail Task
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm text-gray-600">
            <p><strong>Deskripsi:</strong> {{ $task->description_task ?? '-' }}</p>
            <p><strong>Status:</strong> {{ ucfirst($task->status) }}</p>
            <p><strong>Progress:</strong> {{ $task->progress }}%</p>
            <p><strong>Durasi:</strong> {{ $task->duration_days ?? 0 }} hari</p>
            <p><strong>Total Jam:</strong> {{ $task->total_work_hours ?? 0 }} jam</p>
            <p><strong>Total Cost:</strong> Rp {{ number_format($task->total_cost ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- =========================
        BAGIAN 1: LOG PERUBAHAN TASK
    ========================== --}}
    <div class="bg-white shadow border border-gray-200 rounded-2xl p-5">
        <h3 class="text-lg font-bold mb-4 text-gray-700">📋 Riwayat Perubahan Task</h3>

        @if($task->logs->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-3 py-2 border">Tanggal</th>
                            <th class="px-3 py-2 border">Field</th>
                            <th class="px-3 py-2 border">Sebelumnya</th>
                            <th class="px-3 py-2 border">Menjadi</th>
                            <th class="px-3 py-2 border">Diubah Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($task->logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 border">{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td class="px-3 py-2 border font-semibold text-blue-700">{{ ucfirst($log->field) }}</td>
                                <td class="px-3 py-2 border text-gray-600">{{ $log->old_value ?: '-' }}</td>
                                <td class="px-3 py-2 border text-gray-800">{{ $log->new_value ?: '-' }}</td>
                                <td class="px-3 py-2 border text-gray-600">{{ optional($log->user)->name ?? 'System' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600">Belum ada riwayat perubahan.</p>
        @endif
    </div>

    {{-- =========================
        BAGIAN 2: LOG JAM KERJA
    ========================== --}}
    <div class="bg-white shadow border border-gray-200 rounded-2xl p-5">
        <h3 class="text-lg font-bold mb-4 text-gray-700">⏱️ Riwayat Jam Kerja Harian</h3>

        @if($task->workLogs->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-3 py-2 border">Tanggal</th>
                            <th class="px-3 py-2 border">Jam Kerja (jam)</th>
                            <th class="px-3 py-2 border">Diperbarui Pada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($task->workLogs as $work)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 border">{{ \Carbon\Carbon::parse($work->work_date)->format('d M Y') }}</td>
                                <td class="px-3 py-2 border text-blue-700 font-semibold">{{ $work->hours }}</td>
                                <td class="px-3 py-2 border text-gray-600">{{ $work->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600">Belum ada log jam kerja.</p>
        @endif
    </div>

</div>
@endsection

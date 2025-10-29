@extends('layouts.karyawan') 

@section('title', 'History Log Task')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-2xl font-bold text-indigo-700">History Log Task: {{ $task->task_name }}</h2>
        <a href="{{ route('karyawan.tasks.index') }}" class="btn btn-secondary bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded">
            &larr; Kembali
        </a>
    </div>

    {{-- ===== Log Perubahan Task ===== --}}
    @if ($logs->isEmpty())
        <div class="alert alert-info bg-blue-100 text-blue-700 p-3 rounded">
            Belum ada catatan log perubahan untuk task ini.
        </div>
    @else
      <div class="overflow-x-auto mb-8">
    <table class="min-w-full border border-gray-200 rounded-lg">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Waktu Perubahan</th>
                <th class="px-4 py-2 text-left">Field</th>
                <th class="px-4 py-2 text-left">Nilai Lama</th>
                <th class="px-4 py-2 text-left">Nilai Baru</th>
                <th class="px-4 py-2 text-left">Diubah Oleh</th>
                <th class="px-4 py-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Kelompokkan log berdasarkan waktu perubahan
                $groupedLogs = $logs->groupBy(fn($log) => $log->created_at->format('Y-m-d H:i:s'));
            @endphp

            @foreach ($groupedLogs as $time => $group)
                @php
                    $user = $group->first()->user->name ?? 'User Tidak Dikenal';
                    $fields = $group->map(fn($log) => Str::title(str_replace('_', ' ', $log->field)))->implode('<br>');
                    $oldValues = $group->map(fn($log) => e($log->old_value ?? 'Kosong'))->implode('<br>');
                    $newValues = $group->map(fn($log) => '<span class="text-green-600 font-semibold">'.e($log->new_value ?? 'Kosong').'</span>')->implode('<br>');
                @endphp

                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2 text-sm align-top whitespace-nowrap">{{ \Carbon\Carbon::parse($time)->format('d M Y H:i:s') }}</td>
                    <td class="px-4 py-2 text-sm align-top">{!! $fields !!}</td>
                    <td class="px-4 py-2 text-sm align-top text-gray-600">{!! $oldValues !!}</td>
                    <td class="px-4 py-2 text-sm align-top">{!! $newValues !!}</td>
                    <td class="px-4 py-2 text-sm align-top whitespace-nowrap">{{ $user }}</td>
                    <td class="px-4 py-2 text-center align-top">
                        <form action="{{ route('karyawan.tasks.logs.destroy', $group->first()->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus riwayat ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

    @endif

    {{-- ===== Riwayat Jam Kerja ===== --}}
    <div class="mt-10">
        <h3 class="text-xl font-bold text-indigo-700 mb-3">⏱️ Riwayat Jam Kerja</h3>

        @if ($task->workLogs->isEmpty())
            <p class="text-gray-500 text-sm">Belum ada jam kerja yang diinput untuk task ini.</p>
        @else
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Tanggal</th>
                        <th class="px-4 py-2 text-left">Karyawan</th>
                        <th class="px-4 py-2 text-left">Jam Kerja (Jam)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($task->workLogs as $log)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm">{{ \Carbon\Carbon::parse($log->work_date)->format('d M Y') }}</td>
                            <td class="px-4 py-2 text-sm">{{ $log->karyawan->name ?? 'Tidak Diketahui' }}</td>
                            <td class="px-4 py-2 text-sm text-indigo-600 font-semibold">{{ $log->hours }} jam</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection

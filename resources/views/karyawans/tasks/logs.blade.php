@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-10">
<div class="flex justify-between items-start mb-6">

    <div>
        <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-history text-indigo-600"></i>
            Riwayat Task
        </h2>

        <p class="text-gray-600 mt-1 text-sm">
            Detail perubahan & jam kerja untuk task:
            <span class="font-semibold text-indigo-600">
                {{ $task->description_task }}
            </span>
        </p>
    </div>

    <div>
        <a href="{{ route('karyawan.tasks.index') }}"
           class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center gap-2 bg-indigo-50 px-3 py-2 rounded-lg border border-indigo-200">
            <i class="fas fa-arrow-left text-indigo-600"></i>
            <span>Kembali</span>
        </a>
    </div>

</div>



    {{--  LOG PERUBAHAN TASK --}}
   <div class="overflow-x-auto">
    <table class="min-w-full text-sm border border-gray-300 rounded-lg">
        <thead class="bg-indigo-600 text-white uppercase text-xs">
            <tr>
                <th class="py-3 px-4 text-left border border-gray-300">Waktu</th>
                <th class="py-3 px-4 text-left border border-gray-300">User</th>
                <th class="py-3 px-4 text-left border border-gray-300">Field</th>
                <th class="py-3 px-4 text-left border border-gray-300">Sebelumnya</th>
                <th class="py-3 px-4 text-left border border-gray-300">Sesudah</th>
                <th class="py-3 px-4 text-center border border-gray-300">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 bg-white">
            @foreach ($logs as $log)
                @foreach ($log['fields'] as $i => $field)
                <tr class="hover:bg-gray-50 transition">

                    @if ($i === 0)
                    <td rowspan="{{ count($log['fields']) }}" 
                        class="py-3 px-4 border border-gray-300 align-top whitespace-nowrap text-gray-600">
                        {{ \Carbon\Carbon::parse($log['time'])->format('d M Y, H:i:s') }}
                    </td>

                    <td rowspan="{{ count($log['fields']) }}"
                        class="py-3 px-4 border border-gray-300 align-top font-semibold text-gray-800">
                        {{ $log['user'] }}
                    </td>
                    @endif

                    <td class="py-3 px-4 border border-gray-300">
                        {{ ucfirst($field) }}
                    </td>

                    <td class="py-3 px-4 border border-gray-300 text-gray-500">
                        {{ $log['old_values'][$i] ?? '-' }}
                    </td>

                    <td class="py-3 px-4 border border-gray-300 font-medium text-gray-900">
                        {{ $log['new_values'][$i] ?? '-' }}
                    </td>

                    @if ($i === 0)
                    <td rowspan="{{ count($log['fields']) }}"
                        class="py-3 px-4 border border-gray-300 text-center align-top">
                        <form action="{{ route('karyawan.tasks.logs.destroy', $log['id']) }}" method="POST" onsubmit="return confirm('Hapus log ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                    @endif

                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>


    {{-- RIWAYAT JAM KERJA --}}
    <div class="bg-white shadow-xl border border-gray-200 rounded-xl p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-clock text-indigo-600"></i>
            Riwayat Jam Kerja
        </h3>

        @if ($task->workLogs->isEmpty())
            <div class="bg-gray-50 text-gray-600 p-4 rounded-lg border border-gray-200 text-sm">
                Belum ada jam kerja yang dicatat untuk task ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 text-sm rounded-lg">
                    <thead class="bg-indigo-600 text-white uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4 text-left">Tanggal</th>
                            <th class="py-3 px-4 text-left">Karyawan</th>
                            <th class="py-3 px-4 text-left">Durasi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach ($task->workLogs as $log)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-4 text-gray-700">
                                    {{ \Carbon\Carbon::parse($log->work_date)->format('d M Y') }}
                                </td>

                                <td class="py-3 px-4 text-gray-700">
                                    {{ $log->karyawan->name ?? '-' }}
                                </td>

                                <td class="py-3 px-4 text-indigo-600 font-bold">
                                    {{ $log->hours }} jam
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

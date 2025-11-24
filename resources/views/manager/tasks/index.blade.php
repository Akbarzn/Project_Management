@extends('layouts.app')
@section('title', 'Daftar Task Semua Karyawan')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10 space-y-8">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 flex items-center gap-3">
                <i class="fas fa-tasks text-indigo-600"></i>
                Semua Task Karyawan
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Lihat seluruh task dari semua karyawan.
            </p>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="bg-white shadow-xl rounded-xl border border-gray-200 p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">


            {{-- PROJECT FILTER --}}
            <select name="project_id"
                    class="border rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Semua Proyek</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->projectRequest->name_project }}
                    </option>
                @endforeach
            </select>

            {{-- KARYAWAN FILTER --}}
            <select name="karyawan_id"
                    class="border rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Semua Karyawan</option>
                @foreach ($karyawans as $karyawan)
                    <option value="{{ $karyawan->id }}" {{ request('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                        {{ $karyawan->user->name }}
                    </option>
                @endforeach
            </select>

            {{-- STATUS FILTER --}}
            <select name="status"
                    class="border rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Semua Status</option>
                <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="inwork"   {{ request('status') == 'inwork'   ? 'selected' : '' }}>Progress</option>
                <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Selesai</option>
            </select>

            {{-- BUTTON --}}
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition text-sm">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>

        </form>
    </div>

    {{-- TASK TABLE --}}
    @if($tasks->count() > 0)

        <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">

            <table class="min-w-full text-sm">
                <thead class="bg-indigo-600 text-white uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Karyawan</th>
                        <th class="px-4 py-3 text-left">Proyek</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Mulai Task</th>
                        <th class="px-4 py-3 text-left">Selesai Proyek</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @foreach ($tasks as $task)

                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-4 py-3">{{ $loop->iteration }}</td>

                            <td class="px-4 py-3 font-semibold text-gray-800">
                                {{ $task->karyawan->user->name ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $task->project->projectRequest->name_project ?? '-' }}
                            </td>

                            {{--  STATUS --}}
                            <td class="px-4 py-3">
                                @php
                                    $statusClass = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'inwork' => 'bg-blue-100 text-blue-800',
                                        'complete' => 'bg-green-100 text-green-800',
                                    ][$task->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $task->created_at->format('d M Y') }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $task->project->finish_date_project ?? 'N/A' }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('manager.tasks.show', $task->id) }}"
                                   class="inline-flex items-center bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow transition">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                            </td>

                        </tr>

                    @endforeach

                </tbody>
            </table>

        </div>

        {{-- PAGINATION --}}
        <div class="mt-6">
            {{ $tasks->links() }}
        </div>

    @else

        {{-- EMPTY STATE --}}
        <div class="bg-white shadow-xl rounded-xl p-12 text-center border border-gray-200">
            <div class="flex justify-center mb-4">
                <div class="h-20 w-20 bg-indigo-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-tasks text-indigo-600 text-3xl"></i>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-800">Tidak Ada Task</h3>
            <p class="text-gray-500 text-sm mt-2">
                Tidak ditemukan task sesuai filter.
            </p>
        </div>

    @endif

</div>

@endsection

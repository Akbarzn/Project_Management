@extends('layouts.app')
@section('title', 'Daftar Project')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10 space-y-8">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 flex items-center gap-3">
                <i class="fas fa-folder-open text-indigo-600"></i>
                Daftar Project
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Kelola seluruh data project.
            </p>
        </div>
    </div>

    @if($projects->count() > 0)

        <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">
            
            <table class="min-w-full text-sm">
                <thead class="bg-indigo-600 text-white uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3 text-left">Nama Project</th>
                        <th class="px-4 py-3 text-left">Client</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Disetujui</th>
                        <th class="px-4 py-3 text-left">Start</th>
                        <th class="px-4 py-3 text-left w-32">Finish</th>
                        <th class="px-4 py-3 text-center w-32">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @foreach ($projects as $project)

                        <tr class="hover:bg-gray-50 transition">

                            {{-- NOMOR --}}
                            <td class="px-4 py-3 text-center">
                                {{ $projects->firstItem() + $loop->index }}
                            </td>

                            {{-- NAMA PROJECT --}}
                            <td class="px-4 py-3 font-semibold text-gray-800">
                                {{ $project->projectRequest->name_project ?? '-' }}
                            </td>

                            {{-- CLIENT --}}
                            <td class="px-4 py-3 text-gray-700">
                                {{ $project->client->name ?? '-' }}
                            </td>

                            {{-- STATUS --}}
                            <td class="px-4 py-3">
                                @php
                                    $statusClass = match($project->status) {
                                        'ongoing'   => 'bg-yellow-100 text-yellow-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'overdue'    => 'bg-red-500 text-gray-100'
                                    };
                                @endphp

                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </td>

                            {{-- APPROVER --}}
                            <td class="px-4 py-3 text-gray-700">
                                {{ $project->approver->name ?? '-' }}
                            </td>

                            {{-- START DATE --}}
                            <td class="px-4 py-3 text-gray-700">
                                {{ $project->start_date_project ?? '-' }}
                            </td>

                            {{-- FINISH DATE --}}
                            <td class="px-4 py-3 text-gray-700">
                                {{ $project->finish_date_project ?? '-' }}
                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 py-3">
                                <div class="flex justify-center items-center gap-2">

                                    {{-- DETAIL --}}
                                    <a href="{{ route('manager.projects.show', $project->id) }}"
                                       class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow transition">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>

                                    {{-- EDIT --}}
                                    <a href="{{ route('manager.projects.edit', $project->id) }}"
                                       class="inline-flex items-center bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow transition">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>

                                    {{-- DELETE --}}
                                    <form action="{{ route('manager.projects.destroy', $project->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus project ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                        class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow transition">
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    @endforeach

                </tbody>
            </table>

        </div>

        {{-- PAGINATION --}}
        <div class="mt-6">
            {{ $projects->links() }}
        </div>

    @else

        {{-- EMPTY STATE --}}
        <div class="bg-white shadow-xl rounded-xl p-12 text-center border border-gray-200">
            <div class="flex justify-center mb-4">
                <div class="h-20 w-20 bg-indigo-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-folder-open text-indigo-600 text-3xl"></i>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-800">Belum Ada Project</h3>
            <p class="text-gray-500 text-sm mt-2">
                Tidak ada project yang tersedia saat ini.
            </p>

            <a href="{{ route('manager.projects.requests') }}"
               class="mt-5 inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-2"></i>
                Buat Project dari Request
            </a>
        </div>

    @endif

</div>

@endsection

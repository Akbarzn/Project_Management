@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Daftar Project</h2>
        <a href="{{ route('manager.projects.requests') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
            Lihat Request Project
        </a>
    </div>

    <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-indigo-600 text-white">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">No</th>
                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Nama Project</th>
                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Client</th>
                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Disetujui Oleh</th>
                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Tanggal Mulai</th>
                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Tanggal Selesai</th>
                    <th scope="col" class="px-6 py-3 text-center text-sm font-semibold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($projects as $index => $project)
                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                        <td class="whitespace-nowrap txt-sm text-center">
                            @if(method_exists($project, 'firstitem'))
                            {{ $project->firsItem() + $index }}
                            @else
                            {{ $loop->iteration }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">
                            {{ $project->projectRequest->name_project ?? $project->projectRequest->name_project }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $project->client->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($project->status === 'ongoing')
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                                    Ongoing
                                </span>
                            @elseif($project->status === 'completed')
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                    Completed
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">
                                    {{ ucfirst($project->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $project->approver->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $project->start_date_project ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $project->finish_date_project ?? '-' }}</td>

                        <!-- Aksi -->
                        <td class="px-6 py-4">
                            <div class="flex justify-center space-x-2">
                                <!-- Detail -->
                                <a href="{{ route('manager.projects.show', $project->id) }}"
                                   class="flex items-center px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg shadow transition duration-200">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>

                                <!-- Edit -->
                                <a href="{{ route('manager.projects.edit', $project->id) }}"
                                   class="flex items-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded-lg shadow transition duration-200">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>

                                <!-- Hapus -->
                                <form action="{{ route('manager.projects.destroy', $project->id) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus project ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg shadow transition duration-200">
                                        <i class="fas fa-trash-alt mr-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-6 text-center text-gray-500">Belum ada project yang tersedia</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

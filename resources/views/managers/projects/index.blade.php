@extends('layouts.manager')

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
                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Nama Project</th>
                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Client</th>
                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Disetujui Oleh</th>
                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Tanggal Mulai</th>
                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Tanggal Selesai</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($projects as $project)
                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $project->projectRequest->name_project }}</td>
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-gray-500">Belum ada project yang tersedia</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Dashboard Manager')

@section('content')

<div class="container p-6 space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('manager.karyawans.index') }}" class="transform hover:scale-105 duration-400 transition-transform ease-out">

            <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200 flex item-center justify-between">
                <h2 class="font-bold">Total Karyawan</h2>
                <p>{{ $totalKaryawan }}</p>
            </div>
        </a>

        <a href="{{ route('manager.clients.index') }}" class="trasnform hover:scale-105 transition-transform ease-in-out duration-100">
            <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200 flex item-center justify-between">
                <h2 class="font-bold">Total Client</h2>
                <p>{{ $totalClient }}</p>
            </div>
        </a>

        <a href="{{ route('manager.projects.index') }}">
            <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200 flex items-center justify-between">
                <h2 class="font-bold">Total Project</h2>
                <p>{{ $totalProject }}</p>
            </div>
        </a>

    <a href="{{ route('manager.tasks.index') }}">
            <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200 flex items-center justify-between">
                <h2 class="font-bold">Total Task</h2>
                <p>{{ $totalTask }}</p>
            </div>
        </a>
    </div>
</div>



<div class="flex flex-col lg:flex-row gap-6 w-full">
    <div class="bg-white shadow rounded-xl p-6 border border-gray-200 flex flex-col items-center justify-center  lg:w-1/3">
        <h3 class="text-lg font-semibold mb-4 text-center">Karyawan Berdasarkan Task</h3>
        <div class="w-44 h-44 md:w-52 md:h-52">
            <canvas id="karyawanTaskChart"></canvas>
        </div>
        <div class="flex justify-center mt-4 text-sm text-gray-600">
            <span class="mr-4"><span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-1"></span> Sudah Memiliki Task</span>
            <span><span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-1"></span> Belum Memiliki Task</span>
        </div>
    </div>

    <div class="bg-white shadow rounded-xl p-6 border border-gray-200  lg:w-2/3">
        <h3 class="text-lg font-semibold mb-4 text-center">Jumlah Task per Karyawan</h3>
        <div class="w-full" style="height: 250px;">
            <canvas id="jumlahTaskChart"></canvas>
        </div>
    </div>
</div>

{{-- 
<div class="max-w-6xl mx-auto p-6 space-y-8">

    <h2 class="text-2xl font-semibold mb-4">📊 Progress Project per Role</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($projectData as $project)
            <div class="bg-white rounded-2xl p-6 shadow border border-gray-200">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">
                    {{ $project['nama_project'] }}
                </h3>

                @foreach ($project['roles'] as $role)
                    <div class="mb-4">
                        <p class="font-semibold text-gray-700">{{ $role['job_title'] }}</p>

                        <div class="flex items-center justify-between text-sm">
                            <span>Progress:</span>
                            <span>{{ $role['progress'] }}%</span> 
                        </div>

                        <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
                            <div 
                                class="h-2.5 rounded-full transition-all duration-500 
                                @if ($role['progress'] < 50) bg-red-500
                                @elseif ($role['progress'] < 80) bg-yellow-500
                                @else bg-green-500 @endif"
                                style="width: {{ $role['progress'] }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center text-sm text-gray-600">
                    <span>Total Biaya Project:</span>
                    <span class="font-semibold text-gray-800">
                        Rp {{ number_format($project['total_cost'], 0, ',', '.') }} 
                    </span>
                </div>
            </div>
        @endforeach
    </div>
   

</div> --}}

<div class=" p-6 space-y-6">

    <!-- Header Page -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-gray-800">📁 Daftar Project</h2>
        <a href="{{ route('manager.projects.create') }}"
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
            <i class="fa-solid fa-plus mr-2"></i> Tambah Project
        </a>
    </div>

    <!-- Table Card -->
<div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-x-auto  ">
    <table class="table-auto border-collapse ">
        <thead class="bg-indigo-600 text-white">
            <tr>
                <th class="py-2 px-3 text-left text-sm font-semibold">No</th>
                <th class="py-2 px-3 text-left text-sm font-semibold">Nama Project</th>
                <th class="py-2 px-3 text-left text-sm font-semibold">Client</th>
                <th class="py-2 px-3 text-left text-sm font-semibold">Status</th>
                <th class="py-2 px-3 text-left text-sm font-semibold">Disetujui Oleh</th>
                <th class="py-2 px-3 text-left text-sm font-semibold">Mulai</th>
                <th class="py-2 px-3 text-left text-sm font-semibold">Selesai</th>
                <th class="py-2 px-3 text-center text-sm font-semibold">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
            @forelse($projects as $index => $project)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-2 px-3 text-center text-gray-700">{{ $loop->iteration }}</td>
                    <td class="py-2 px-3 font-medium text-gray-900">{{ $project->projectRequest->name_project ?? '-' }}</td>
                    <td class="py-2 px-3 text-gray-700">{{ $project->client->name ?? '-' }}</td>
                    <td class="py-2 px-3">
                        @php
                            $statusColors = [
                                'ongoing' => 'bg-yellow-100 text-yellow-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'pending' => 'bg-gray-100 text-gray-800',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusColors[$project->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </td>
                    <td class="py-2 px-3 text-gray-700">{{ $project->approver->name ?? '-' }}</td>
                    <td class="py-2 px-3 text-gray-700">{{ $project->start_date_project ?? '-' }}</td>
                    <td class="py-2 px-3 text-gray-700">{{ $project->finish_date_project ?? '-' }}</td>
                    <td class="py-2 px-3 text-center">
                        <div class="flex justify-center gap-1">
                            <a href="{{ route('manager.projects.show', $project->id) }}"
                               class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-md flex items-center gap-1">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('manager.projects.edit', $project->id) }}"
                               class="px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded-md flex items-center gap-1">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('manager.projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Hapus project ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-md flex items-center gap-1">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="py-4 text-center text-gray-500">Belum ada project yang tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="p-6 space-y-6" x-data="{ selected: null, projects: {{ json_encode($projects) }} }">
    <h2 class="text-2xl font-semibold text-gray-800">📁 Pilih Project</h2>

    <!-- Dropdown select -->
    <select x-model="selected" class="w-full md:w-1/3 border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
        <option value="">-- Pilih Project --</option>
        <template x-for="p in projects" :key="p.id">
            <option :value="p.id" x-text="p.project_request?.name_project ?? 'Tanpa Nama'"></option>
        </template>
    </select>

    <!-- Detail Project -->
    <template x-if="selected">
        <div class="bg-white rounded-lg shadow border border-gray-100 p-6 mt-4">
            <template x-for="p in projects.filter(x => x.id == selected)">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2" x-text="p.project_request?.name_project"></h3>
                    <p class="text-sm text-gray-600"><b>Client:</b> <span x-text="p.client?.name ?? '-'"></span></p>
                    <p class="text-sm text-gray-600"><b>Status:</b> <span x-text="p.status"></span></p>
                    <p class="text-sm text-gray-600"><b>Mulai:</b> <span x-text="p.start_date_project ?? '-'"></span></p>
                    <p class="text-sm text-gray-600"><b>Selesai:</b> <span x-text="p.finish_date_project ?? '-'"></span></p>
                    
                    <div class="mt-4 flex gap-2">
                        <a :href="`/manager/projects/${p.id}`" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-md">
                            <i class="fa-solid fa-eye"></i> Detail
                        </a>
                        <a :href="`/manager/projects/${p.id}/edit`" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded-md">
                            <i class="fa-solid fa-pen"></i> Edit
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </template>
</div>


{{-- <pre>
{{ var_dump($names) }}
{{ var_dump($projectCounts) }}
</pre> --}}


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // === Donut Chart (Karyawan Berdasarkan Task) ===
    const donutCtx = document.getElementById('karyawanTaskChart');
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Sudah Memiliki Task', 'Belum Memiliki Task'],
            datasets: [{
                data: [{{ $sudahMemilikiTask }}, {{ $belumMemilikiTask }}],
                backgroundColor: ['#22c55e', '#3b82f6'],
                borderWidth: 0,
            }]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: { display: false }
            }
        }
    });

    // === Bar Chart (Jumlah Task per Karyawan) ===
    const barCtx = document.getElementById('jumlahTaskChart');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: @json($names),
            datasets: [{
                label: 'Jumlah Task',
                data: @json($projectCounts),
                backgroundColor: '#93c5fd',
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Jumlah Task' }},
                x: { title: { display: true, text: 'Karyawan' }}
            },
            onClick:(event, elements) => {
                if(elements.length > 0){
                    const index = elements[0].index;
                    const karyawanId = @json($idKaryawan)[index];
                    window.location.href = `/manager/karyawan/${karyawanId}/project`;
                }
            }
        }
    });
</script>

<style>
    #jumlahTaskChart {
        cursor:pointer;
    }
</style>


@endsection
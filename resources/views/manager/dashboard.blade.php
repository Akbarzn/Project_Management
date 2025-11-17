@extends('layouts.app')
@section('title', 'Dashboard Manager')

@section('content')

<div class="max-w-7xl mx-auto p-6 space-y-8">

    {{-- === Statistik Header === --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white shadow rounded-xl p-5 text-center border border-gray-100 hover:shadow-lg transition">
            <h3 class="text-sm font-semibold text-gray-500">Total Karyawan</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $totalKaryawan }}</p>
        </div>
        <div class="bg-white shadow rounded-xl p-5 text-center border border-gray-100 hover:shadow-lg transition">
            <h3 class="text-sm font-semibold text-gray-500">Total Client</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $totalClient }}</p>
        </div>
        <div class="bg-white shadow rounded-xl p-5 text-center border border-gray-100 hover:shadow-lg transition">
            <h3 class="text-sm font-semibold text-gray-500">Total Project</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $totalProject }}</p>
        </div>
        <div class="bg-white shadow rounded-xl p-5 text-center border border-gray-100 hover:shadow-lg transition">
            <h3 class="text-sm font-semibold text-gray-500">Total Task</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $totalTask }}</p>
        </div>
    </div>

    {{-- CHARTS SECTION --}}
<div class="flex flex-col lg:flex-row gap-6">

    {{-- CHART KARYAWAN BERDASARKAN TASK (30%) --}}
    <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100 w-full lg:w-[30%]">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Karyawan Berdasarkan Task</h4>
        <canvas id="karyawanTaskChart" height="250"></canvas>
    </div>

    {{-- CHART JUMLAH TASK PER KARYAWAN (70%) --}}
    <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100 w-full lg:w-[70%]">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Jumlah Task per Karyawan</h4>
        <div class="overflow-x-auto">
            <canvas id="jumlahTaskChart" width="900" height="300"></canvas>
        </div>
    </div>

</div>

    {{-- === Daftar Project === --}}
    {{-- <div class="bg-white shadow rounded-xl border border-gray-100 overflow-hidden">
        <div class="flex justify-between items-center bg-indigo-600 px-6 py-3">
            <h3 class="text-white font-semibold text-lg">📁 Daftar Project</h3>
            <a href="{{ route('manager.projects.create') }}" 
               class="bg-white text-indigo-700 px-4 py-2 text-sm font-semibold rounded-md shadow hover:bg-gray-100 transition">
                + Tambah Project
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-gray-700">
                <thead class="bg-indigo-100 text-indigo-800 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Nama Project</th>
                        <th class="px-4 py-3 text-left">Client</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Disetujui Oleh</th>
                        <th class="px-4 py-3 text-left">Mulai</th>
                        <th class="px-4 py-3 text-left">Selesai</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($projects as $index => $project)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 font-semibold">{{ $project->projectRequest->name_project ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $project->client->name ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($project->status == 'complete') bg-green-100 text-green-700
                                    @elseif($project->status == 'ongoing') bg-yellow-100 text-yellow-700
                                    @elseif($project->status == 'overdue') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ $project->approver->name ?? 'Manager' }}</td>
                            <td class="px-4 py-2">{{ $project->start_date_project ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $project->finish_date_project ?? '-' }}</td>
                            <td class="px-4 py-2 text-center space-x-2">
                                <a href="{{ route('manager.projects.show', $project->id) }}" 
                                   class="text-blue-600 hover:text-blue-800" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('manager.projects.edit', $project->id) }}" 
                                   class="text-yellow-500 hover:text-yellow-700" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('manager.projects.destroy', $project->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin ingin hapus project ini?')" 
                                            class="text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-4 text-center text-gray-500">Belum ada project.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div> --}}

    {{-- detail project --}}
    <select name="" id="projectSelect" class="border rounded p-2">
        <option value="">-- Pilih Project --</option>
        @foreach ($projects as $project )
            <option value="{{ $project->id }}">
                {{ $project->projectRequest->name_project }}
            </option>
        @endforeach
    </select>

    <div id="projectDetail"></div>

</div>


{{-- <pre>
{{ var_dump($names) }}
{{ var_dump($projectCounts) }}
</pre>  --}}


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
    

    //ajax untuk detail project
   document.getElementById('projectSelect').addEventListener('change', async function () {
    if (!this.value) return;

    const detailBox = document.getElementById('projectDetail');

    // === Tampilkan loading biar tidak terasa jeda ===
    detailBox.innerHTML = `
        <div class="p-4 bg-gray-100 rounded-md text-gray-600 animate-pulse">
            Mengambil data project...
        </div>
    `;

    try {
        const res = await fetch(`/manager/project-detail/${this.value}`);
        const data = await res.json();

        let html = `
            <div class="p-6 bg-white shadow-md rounded-xl mt-4 border border-gray-200">
                <h3 class="text-2xl font-bold mb-2 text-indigo-700">${data.name_project}</h3>

                <div class="mt-3 text-gray-700">
                    <p><strong>Total Cost:</strong> Rp ${Number(data.total_cost).toLocaleString()}</p>
                    <p><strong>Total Progress:</strong> ${data.total_progress}%</p>
                </div>

                <h4 class="text-lg font-semibold mt-5 mb-3 text-gray-800">Progress per Role</h4>
        `;

        data.jobTitle.forEach(role => {
            html += `
                <div class="mb-4">
                    <div class="flex justify-between mb-1">
                        <span class="font-medium text-gray-700">${role.job_title}</span>
                        <span class="text-sm font-semibold text-indigo-600">${role.progress}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-indigo-500 h-3 rounded-full transition-all duration-500"
                             style="width:${role.progress}%"></div>
                    </div>
                </div>
            `;
        });

        html += `</div>`;

        detailBox.innerHTML = html;

    } catch (err) {
        detailBox.innerHTML = `
            <div class="p-4 bg-red-100 rounded-md text-red-700">
                Terjadi kesalahan saat mengambil data.
            </div>
        `;
    }
});

</script>

<style>
    #jumlahTaskChart {
        cursor:pointer;
    }
</style>


@endsection
@extends('layouts.manager')
@section('title', 'Dashboard Manager')

@section('content')

<div class="container max-w-7xl mx-auto p-6 space-y-6">
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

        <a href="">
            <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200 flex items-center justify-between">
                <h2 class="font-bold">Total Task</h2>
                <p>{{ $totalTask }}</p>
            </div>
        </a>
    </div>
</div>



<div class="max-w-7xl mx-auto p-6 space-y-8">

    {{-- === Baris 1 === --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Karyawan Berdasarkan Task --}}
        <div class="bg-white shadow rounded-xl p-6 border border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Karyawan Berdasarkan Task</h3>
            <canvas id="karyawanTaskChart" height="200"></canvas>
            <div class="flex justify-center mt-4 text-sm text-gray-600">
                <span class="mr-4"><span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-1"></span> Sudah Memiliki Task</span>
                <span><span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-1"></span> Belum Memiliki Task</span>
            </div>
        </div>

        {{-- Jumlah Task per Karyawan --}}
        <div class="bg-white shadow rounded-xl p-6 border border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Jumlah Task per Karyawan</h3>
            <canvas id="jumlahTaskChart" height="200"></canvas>
        </div>
    </div>

  

<div class="max-w-6xl mx-auto p-6 space-y-8">

    <h2 class="text-2xl font-semibold mb-4">📊 Progress Project per Role</h2>

   {{-- 🔹 Panel Section (Dinamis per Project) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($projectData as $project)
            <div class="bg-white rounded-2xl p-6 shadow border border-gray-200">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">
                    {{ $project['nama_project'] }}
                </h3>

                {{-- ITERATE OVER ROLES/JOB TITLES WITHIN THE PROJECT --}}
                {{-- *Change: We now expect $project['roles'] to be an array of roles* --}}
                @foreach ($project['roles'] as $role)
                    <div class="mb-4">
                        {{-- *Change: Use the role name (job_title) for the heading* --}}
                        <p class="font-semibold text-gray-700">{{ $role['job_title'] }}</p>

                        <div class="flex items-center justify-between text-sm">
                            <span>Progress:</span>
                            {{-- *Change: Use the role's progress* --}}
                            <span>{{ $role['progress'] }}%</span> 
                        </div>

                        <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
                            <div 
                                class="h-2.5 rounded-full transition-all duration-500 
                                @if ($role['progress'] < 50) bg-red-500 
                                @elseif ($role['progress'] < 80) bg-yellow-500 
                                @else bg-green-500 @endif"
                                {{-- *Change: Set the width based on the role's progress* --}}
                                style="width: {{ $role['progress'] }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
                
                {{-- Total Biaya Project (optional, can be kept as total or sum of role costs) --}}
                <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center text-sm text-gray-600">
                    <span>Total Biaya Project:</span>
                    <span class="font-semibold text-gray-800">
                        {{-- Assuming $project['total_cost'] is the overall project cost --}}
                        Rp {{ number_format($project['total_cost'], 0, ',', '.') }} 
                    </span>
                </div>
            </div>
        @endforeach
    </div>
   

</div>

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
            }
        }
    });
</script>


@endsection
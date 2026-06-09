@extends('layouts.app')
@inject('workloadService', 'App\Services\WorkloadService')

@section('title', 'Daftar Karyawan')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-12 space-y-8">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 flex items-center gap-3">
                <i class="fas fa-users text-indigo-600"></i>
                Daftar Karyawan
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Kelola seluruh data karyawan, workload balancing, dan skill matching.
            </p>
        </div>

        <a href="{{ route('manager.karyawans.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-5 rounded-lg shadow-lg shadow-indigo-300/40 transition">
            + Tambah Karyawan
        </a>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm">
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif


    {{-- TABLE --}}
    @if($karyawans->count() > 0)
        <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">

            <table class="min-w-full text-sm">
                <thead class="bg-indigo-600 text-white uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center">No</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">NIK</th>
                        <th class="px-4 py-3 text-left">Jabatan</th>
                        <th class="px-4 py-3 text-left">Skills</th>
                        <th class="px-4 py-3 text-center">Active Projects</th>
                        <th class="px-4 py-3 text-center">Workload Status</th>
                        <th class="px-4 py-3 text-left">Biaya/Jam</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @foreach($karyawans as $index => $karyawan)
                        @php
                            $workloadSummary = $workloadService->getWorkloadSummary($karyawan);
                            $activeProjects = $workloadSummary['active_projects'];
                            $status = $workloadSummary['workload_status'];

                            if ($status === 'Overload') {
                                $badgeColor = 'bg-red-100 text-red-800 border-red-200';
                                $badgeText = 'Overload';
                                $bulletColor = 'bg-red-500';
                            } elseif ($status === 'Tinggi') {
                                $badgeColor = 'bg-orange-100 text-orange-800 border-orange-200';
                                $badgeText = 'Tinggi';
                                $bulletColor = 'bg-orange-500';
                            } elseif ($status === 'Normal') {
                                $badgeColor = 'bg-blue-100 text-blue-800 border-blue-200';
                                $badgeText = 'Normal';
                                $bulletColor = 'bg-blue-500';
                            } elseif ($status === 'Ringan') {
                                $badgeColor = 'bg-green-100 text-green-800 border-green-200';
                                $badgeText = 'Ringan';
                                $bulletColor = 'bg-green-500';
                            } else {
                                $badgeColor = 'bg-gray-100 text-gray-800 border-gray-200';
                                $badgeText = 'Tidak Ada Beban';
                                $bulletColor = 'bg-gray-500';
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-center">
                                {{ $karyawans->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-800">
                                <div>{{ $karyawan->name }}</div>
                                @if($karyawan->level)
                                    @php
                                        $levelColors = [
                                            'Junior' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'Intermediate' => 'bg-sky-50 text-sky-700 border-sky-200',
                                            'Senior' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                            'Lead' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ][$karyawan->level] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                    @endphp
                                    <span class="inline-flex items-center px-1.5 py-0.5 mt-1 rounded text-2xs font-semibold border {{ $levelColors }}">
                                        {{ $karyawan->level }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $karyawan->nik }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $karyawan->job_title }}</td>
                            <td class="px-4 py-3">
                                @if(!empty($karyawan->skills))
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($karyawan->skills as $skill)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                {{ $skill }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 font-medium">{{ $activeProjects }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeColor }}">
                                    <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $bulletColor }}"></span>
                                    {{ $badgeText }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                Rp {{ number_format($karyawan->cost, 0, ',', '.') }}
                            </td>
                            {{-- <td class="px-4 py-3 text-center space-x-2 whitespace-nowrap"> --}}
                            <td class="px-4 py-3 ">
                                <div class="flex flex-col gap-2 items-center">

                                    
                                    {{-- EDIT --}}
                                    <a href="{{ route('manager.karyawans.edit', $karyawan->id) }}"
                                        class="inline-flex items-center bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow transition">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                
                                {{-- DELETE --}}
                                <form action="{{ route('manager.karyawans.destroy', $karyawan->id) }}"
                                      method="POST"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                    onclick="return confirm('Yakin ingin menghapus karyawan ini?')"
                                    class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow transition">
                                        <i class="fas fa-trash mr-1"></i> Hapus
                                    </button>
                                </form>

                            </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $karyawans->links() }}
        </div>

    @else

        {{-- EMPTY STATE  --}}
        <div class="bg-white shadow-xl rounded-xl p-12 text-center border border-gray-200">
            <div class="flex justify-center mb-4">
                <div class="h-20 w-20 bg-indigo-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-plus text-indigo-600 text-3xl"></i>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-800">Belum Ada Data Karyawan</h3>
            <p class="text-gray-500 text-sm mt-2">
                Tambahkan karyawan baru untuk mulai mengelola data.
            </p>

            <a href="{{ route('manager.karyawans.create') }}"
                class="mt-5 inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-2"></i>
                Tambah Karyawan
            </a>
        </div>

    @endif

</div>

@endsection

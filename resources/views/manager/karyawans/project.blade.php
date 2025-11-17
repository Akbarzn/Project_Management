@extends('layouts.app')

@section('title', "Project milik $karyawan->name")

@section('content')
<div class="max-w-5xl mx-auto px-6 py-8 space-y-8">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-briefcase text-indigo-600"></i>
            Project yang Dikerjakan {{ $karyawan->name }}
        </h2>

        <a href="{{ route('manager.karyawans.index') }}"
            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow transition">
            <i class="fas fa-arrow-left mr-1"></i>
            Kembali
        </a>
    </div>


    @forelse ($projects as $p)
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg transition">

            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-1 flex items-center gap-2">
                        <i class="fas fa-folder-open text-indigo-600"></i>
                        {{ $p->project_name }}
                    </h3>

                    <p class="text-gray-600 text-sm">
                        Total Jam: <strong>{{ $p->total_hours }} jam</strong>
                    </p>

                    <p class="text-gray-600 text-sm">
                        Total Biaya:
                        <strong>Rp {{ number_format($p->total_cost, 0, ',', '.') }}</strong>
                    </p>

                    <p class="text-gray-600 text-sm">
                        Status:
                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                            {{ $p->status === 'complete' ? 'bg-green-100 text-green-800' :
                               ($p->status === 'inwork' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700') }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </p>
                </div>

                <a href="{{ route('manager.projects.show', $p->project_id) }}"
                    class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow transition">
                    <i class="fas fa-eye mr-1"></i> Detail
                </a>
            </div>
        </div>

    @empty
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-10 text-center">
            <i class="fas fa-folder-open text-gray-400 text-5xl mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-700">Belum Ada Project</h3>
            <p class="text-gray-500 text-sm">Karyawan ini belum mengerjakan project apa pun.</p>
        </div>
    @endforelse

</div>
@endsection

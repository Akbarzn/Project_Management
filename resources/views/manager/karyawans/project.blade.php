@extends('layouts.app')

@section('title', "Project milik $karyawan->name")

@section('content')
<div class="max-w-4xl mx-auto p-6 space-y-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">
        Project yang Dikerjakan {{ $karyawan->name }}
    </h2>

    @forelse ($karyawan->projects as $project)
        <div class="bg-white p-4 shadow rounded-lg border border-gray-200">
            <h3 class="font-semibold text-lg text-gray-700 mb-1">
                {{ $project->projectRequest->name_project ?? 'Tanpa Nama' }}
            </h3>
            <p class="text-gray-600 text-sm">Biaya: Rp {{ number_format($project->total_cost, 0, ',', '.') }}</p>
        </div>
    @empty
        <p class="text-gray-500">Karyawan ini belum mengerjakan project apa pun.</p>
    @endforelse
</div>
@endsection

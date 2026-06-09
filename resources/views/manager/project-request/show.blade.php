@extends('layouts.app')

@section('title', 'Detail Project Request')

@section('content')
<div class="max-w-4xl mx-auto p-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-file-alt text-indigo-600"></i>
            Detail Project Request
        </h2>

        <a href="{{ route('manager.project-request.index') }}"
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow transition">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    {{-- Card Container --}}
    <div class="bg-white shadow-xl border border-gray-200 rounded-xl overflow-hidden">

        {{-- Top Banner --}}
        <div class="bg-indigo-600 px-6 py-4">
            <h3 class="text-xl font-semibold text-white flex items-center gap-2">
                <i class="fas fa-ticket-alt"></i>
                Tiket: {{ $projectRequest->tiket }}
            </h3>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-6 text-gray-800">

            {{-- Nama Project --}}
            <div>
                <p class="text-sm text-gray-500 font-medium">Nama Project</p>
                <p class="text-lg font-semibold">{{ $projectRequest->name_project }}</p>
            </div>

            {{-- Client --}}
            <div class="border rounded-lg p-4 bg-gray-50">
                <p class="text-sm text-gray-500 font-medium mb-2">Client</p>
                <p class="font-semibold text-gray-900">{{ $projectRequest->client->name }}</p>

                <div class="mt-2 text-sm text-gray-600 space-y-1">
                    <p><span class="font-medium">NIK:</span> {{ $projectRequest->client->nik }}</p>
                    <p><span class="font-medium">Kode Organisasi:</span> {{ $projectRequest->client->kode_organisasi }}</p>
                    <p><span class="font-medium">Phone:</span> {{ $projectRequest->client->phone }}</p>
                </div>
            </div>

            {{-- Kategori --}}
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1">Kategori</p>
                <span class="px-3 py-1 text-sm font-semibold rounded-full
                    {{ $projectRequest->kategori === 'New Aplikasi'
                        ? 'bg-blue-100 text-blue-700'
                        : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $projectRequest->kategori }}
                </span>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════ --}}
            {{-- Auto Assignment Parameters                                     --}}
            {{-- ═══════════════════════════════════════════════════════════════ --}}
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5">
                <h4 class="text-sm font-bold text-indigo-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-robot text-indigo-600"></i>
                    Parameter Auto Assignment
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- Priority --}}
                    <div class="bg-white rounded-lg p-4 border border-indigo-100">
                        <p class="text-xs text-gray-500 font-medium mb-1">Priority</p>
                        @php
                            $priorityBadge = [
                                1 => 'bg-gray-100 text-gray-800',
                                2 => 'bg-blue-100 text-blue-800',
                                3 => 'bg-yellow-100 text-yellow-800',
                                4 => 'bg-orange-100 text-orange-800',
                                5 => 'bg-red-100 text-red-800',
                            ][$projectRequest->priority] ?? 'bg-gray-100 text-gray-800';

                            $priorityLabel = \App\Models\ProjectRequest::PRIORITY_LABELS[$projectRequest->priority] ?? '-';
                        @endphp
                        <span class="inline-block px-3 py-1 text-xs font-bold rounded-full {{ $priorityBadge }}">
                            {{ $projectRequest->priority }} — {{ $priorityLabel }}
                        </span>
                    </div>

                    {{-- Difficulty --}}
                    <div class="bg-white rounded-lg p-4 border border-indigo-100">
                        <p class="text-xs text-gray-500 font-medium mb-1">Difficulty</p>
                        <p class="font-bold text-gray-800">
                            {{ $projectRequest->difficulty }} — {{ \App\Models\ProjectRequest::DIFFICULTY_LABELS[$projectRequest->difficulty] ?? '-' }}
                        </p>
                    </div>

                    {{-- Estimated Duration --}}
                    <div class="bg-white rounded-lg p-4 border border-indigo-100">
                        <p class="text-xs text-gray-500 font-medium mb-1">Estimasi Durasi</p>
                        <p class="font-bold text-gray-800">
                            {{ $projectRequest->estimated_duration ? $projectRequest->estimated_duration . ' Jam' : '-' }}
                        </p>
                    </div>

                </div>


            </div>

            {{-- Status --}}
            @php
                $badge = [
                    'pending'  => 'bg-yellow-100 text-yellow-800',
                    'approve'  => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800'
                ][$projectRequest->status] ?? 'bg-gray-100 text-gray-800';
            @endphp

            <div>
                <p class="text-sm text-gray-500 font-medium mb-1">Status</p>
                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full {{ $badge }}">
                    <i class="fas fa-circle mr-2 text-xs"></i>
                    {{ ucfirst($projectRequest->status) }}
                </span>
            </div>

            {{-- Deskripsi --}}
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1">Deskripsi</p>
                <div class="bg-gray-50 border rounded-lg p-4 text-gray-700 leading-relaxed">
                    {{ $projectRequest->description }}
                </div>
            </div>

            {{-- Dokumen --}}
            @if($projectRequest->document)
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Dokumen Pendukung</p>
                    <a href="{{ asset('storage/' . $projectRequest->document) }}"
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-white shadow transition">
                        <i class="fas fa-file-download mr-2"></i> Lihat Dokumen
                    </a>
                </div>
            @endif

            {{-- Approve Button --}}
            @if($projectRequest->status === 'pending')
                <div class="pt-2 border-t border-gray-200">
                    <a href="{{ route('manager.projects.create.from.request', ['requestId' => $projectRequest->id]) }}"
                        class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg shadow font-semibold transition">
                        <i class="fas fa-robot mr-2"></i>
                        Approve & Jalankan Auto Assignment
                    </a>
                    <p class="text-xs text-gray-500 mt-2">
                        Sistem akan otomatis membentuk tim 5 role berdasarkan Least Load algorithm.
                    </p>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection

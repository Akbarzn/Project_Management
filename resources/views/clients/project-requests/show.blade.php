@extends('layouts.app')

@section('title', 'Detail Project Request')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-10">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-extrabold text-gray-800 flex items-center gap-3">
            <i class="fas fa-eye text-indigo-600"></i>
            Detail Project Request
        </h2>

        <a href="{{ route('clients.project-requests.index') }}"
           class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    {{-- Card Utama --}}
    <div class="bg-white shadow-xl rounded-xl border border-gray-200 p-6 space-y-6">

        {{-- Tiket + Status --}}
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500">Nomor Tiket</p>
                <p class="text-xl font-semibold text-gray-800">
                    {{ $projectRequest->tiket }}
                </p>
            </div>

            @php
                $badge = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'approve' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                ][$projectRequest->status] ?? 'bg-gray-100 text-gray-800';
            @endphp

            <span class="px-4 py-1 text-sm font-semibold rounded-full {{ $badge }}">
                {{ ucfirst($projectRequest->status) }}
            </span>
        </div>

        <hr>

        {{-- Detail Project --}}
        <div class="space-y-4">

            {{-- Nama Project --}}
            <div>
                <p class="text-sm text-gray-500">Nama Project</p>
                <p class="text-lg font-semibold text-gray-800">
                    {{ $projectRequest->name_project }}
                </p>
            </div>

            {{-- Client --}}
            <div>
                <p class="text-sm text-gray-500">Client</p>
                <p class="font-medium text-gray-700">
                    {{ $projectRequest->client->name }}
                </p>
            </div>

            {{-- Kategori --}}
            <div>
                <p class="text-sm text-gray-500">Kategori</p>
                <p class="text-gray-700">{{ $projectRequest->kategori }}</p>
            </div>

            {{-- Deskripsi --}}
            <div>
                <p class="text-sm text-gray-500">Deskripsi</p>
                <div class="bg-gray-50 border rounded-lg p-3 text-gray-700 leading-relaxed">
                    {{ $projectRequest->description }}
                </div>
            </div>

            {{-- Dokumen --}}
            <div>
                <p class="text-sm text-gray-500">Dokumen</p>
                @if ($projectRequest->document)
                    <a href="{{ asset('storage/' . $projectRequest->document) }}"
                       target="_blank"
                       class="inline-flex items-center text-indigo-600 hover:underline font-medium">
                        <i class="fas fa-file-alt mr-2"></i> Lihat Dokumen
                    </a>
                @else
                    <p class="text-gray-400 text-sm italic">Tidak ada dokumen</p>
                @endif
            </div>

        </div>

        <hr>

        {{-- Tombol Aksi --}}
        <div class="flex justify-end space-x-3">

            <a href="{{ route('clients.project-requests.edit', $projectRequest->id) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-lg font-semibold shadow transition">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>

            <form action="{{ route('clients.project-requests.destroy', $projectRequest->id) }}"
                  method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus request ini?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg font-semibold shadow transition">
                    <i class="fas fa-trash-alt mr-2"></i> Hapus
                </button>
            </form>

        </div>

    </div>
</div>
@endsection

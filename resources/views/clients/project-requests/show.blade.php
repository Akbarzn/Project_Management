@extends('layouts.client')

@section('title', 'Detail Project Request')

@section('content')
<div class="max-w-4xl mx-auto p-6 space-y-6">

    {{-- Header --}}
    <div class="bg-white shadow rounded-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Detail Project Request</h2>
            <a href="{{ route('clients.project-requests.index') }}" class="px-3 py-1 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                ← Kembali
            </a>
        </div>

        {{-- Project Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4   text-gray-700">
        {{-- <div class=" gap-4 flex justify-center items-center  text-gray-700"> --}}
            <div>
                <p class="font-semibold font">Nomor Tiket:</p>
                <p>{{ $projectRequest->tiket }}</p>
            </div>

            <div>
                <p class="font-semibold">Kategori:</p>
                <p>{{ $projectRequest->kategori }}</p>
            </div>

            <div>
                <p class="font-semibold">Nama Project:</p>
                <p>{{ $projectRequest->name_project }}</p>
            </div>

            <div>
                <p class="font-semibold">Status:</p>
                <p class="capitalize">{{ $projectRequest->status ?? 'Pending' }}</p>
            </div>

            <div>
                <p class="font-semibold">Client:</p>
                <p>{{ $projectRequest->client->name ?? '-' }}</p>
            </div>

            <div>
                <p class="font-semibold">Tanggal Dibuat:</p>
                <p>{{ $projectRequest->created_at->format('d M Y H:i') }}</p>
            </div>

            <div class="md:col-span-2">
                <p class="font-semibold">Deskripsi:</p>
                <p class="whitespace-pre-line">{{ $projectRequest->description }}</p>
            </div>

            <div class="md:col-span-2">
                <p class="font-semibold">Dokumen:</p>
                @if($projectRequest->document)
                    <a href="{{ asset('storage/' . $projectRequest->document) }}" target="_blank" class="text-blue-600 hover:underline">📎 Lihat Dokumen</a>
                @else
                    <p>Tidak ada dokumen</p>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection

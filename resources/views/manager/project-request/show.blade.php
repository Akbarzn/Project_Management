@extends('layouts.manager')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-semibold mb-4">Detail Project Request</h2>

    <p><strong>Nama Project:</strong> {{ $request->name_project }}</p>
    <p><strong>Kategori:</strong> {{ $request->kategori }}</p>
    <p><strong>Client:</strong> {{ $request->client->name }}</p>
    <p><strong>Deskripsi:</strong> {{ $request->description }}</p>
    <p><strong>Status:</strong> {{ ucfirst($request->status) }}</p>

    @if($request->document)
        <p><strong>Dokumen:</strong>
            <a href="{{ asset('storage/' . $request->document) }}" class="text-indigo-600 underline">Lihat File</a>
        </p>
    @endif

    <div class="mt-4">
        <a href="{{ route('manager.project-request.index') }}"
           class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Kembali</a>
    </div>
</div>
@endsection

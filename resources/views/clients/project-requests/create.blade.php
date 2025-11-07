@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-2xl font-semibold mb-6 ">Buat Project Request</h3>

    <form action="{{ route('clients.project-requests.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- tiket --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Nomor Tiket</label>
            <input type="text" value="{{ $ticketNumber }}" readonly
                class="block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100">
        </div>

        {{-- nama --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Nama</label>
            <input type="text" value="{{ $client->name }}" readonly
                class="block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100">
        </div>

        {{-- nik --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Nik</label>
            <input type="text" value="{{ $client->nik }}" readonly
            class="block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100">
        </div>

        {{-- kode rganisasi--}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium">Kode Organisasi</label>
            <input type="text" value="{{ $client->kode_organisasi }}" readonly
            class="block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100">
        </div>

        {{-- phone --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium">Phone</label>
            <input type="text" value="{{ $client->phone }}" readonly
            class="block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100">
        </div>

        {{-- nama project --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Nama Project</label>
            <input type="text" name="name_project" required
                class="block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        {{-- kategori --}}
        <div class="mb-4">
            <span class="block text-gray-700 font-medium mb-2">Kategori</span>
            <label class="inline-flex items-center mr-4">
                <input type="radio" name="kategori" value="New Aplikasi" required
                    class="form-radio text-indigo-600">
                <span class="ml-2">New Aplikasi</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="kategori" value="Update Aplikasi" required
                    class="form-radio text-indigo-600">
                <span class="ml-2">Update Aplikasi</span>
            </label>
        </div>

        {{-- deskripsi --}}
        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-medium mb-1">Deskripsi</label>
            <textarea name="description" id="description" rows="4" required
                class="block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
        </div>

        {{-- dokumen --}}
        <div class="mb-4">
            <label for="document" class="block text-gray-700 font-medium mb-1">Upload Dokumen (opsional)</label>
            <input type="file" name="document" id="document"
                class="block w-full border border-gray-300 rounded-md px-3 py-2">
            <p class="text-sm text-gray-500 mt-1">PDF, DOC, DOCX, PNG, JPG, JPEG (Maks 2MB)</p>
        </div>

        <button type="submit"
            class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 transition">Kirim Request</button>
    </form>
</div>
@endsection

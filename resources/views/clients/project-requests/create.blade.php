@extends('layouts.client')

@section('content')
<div class="container">
    <h3>Buat Project Request</h3>

<form action="{{ route('clients.project-requests.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Informasi Client & Tiket --}}
        <div class="mb-3">
            <label class="form-label">Nomor Tiket</label>
            <input type="text" class="form-control" value="{{ $ticketNumber }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" class="form-control" value="{{ $client->name }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">NIK</label>
            <input type="text" class="form-control" value="{{ $client->nik }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Kode Organisasi</label>
            <input type="text" class="form-control" value="{{ $client->kode_organisasi }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="text" class="form-control" value="{{ $client->user->email }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Telepon</label>
            <input type="text" class="form-control" value="{{ $client->phone }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Project</label>
            <input type="text" name='name_project' class="form-control" required >
        </div>

        {{-- Form Kategori --}}
        <div class="mb-3">
            <label class="form-label d-block">Kategori</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="kategori" value="New Aplikasi" required>
                <label class="form-check-label">New Aplikasi</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="kategori" value="Update Aplikasi" required>
                <label class="form-check-label">Update Aplikasi</label>
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
        </div>

        {{-- Upload Dokumen --}}
        <div class="mb-3">
            <label for="document" class="form-label">Upload Dokumen (opsional)</label>
            <input type="file" name="document" id="document" class="form-control">
            <div class="form-text">PDF, DOC, DOCX, PNG, JPG, JPEG (Maks 2MB)</div>
        </div>

        <button type="submit" class="btn btn-primary">Kirim Request</button>
    </form>
</div>
@endsection

@extends('layouts.client')

@section('title', 'Edit Project Request')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Project Request</h5>
<a href="{{ route('clients.project-requests.index') }}" class="btn btn-light btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card-body">
<form action="{{ route('clients.project-requests.update', $projectRequest->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

              {{-- Kategori (RADIO BUTTONS) --}}
            <div class="mb-3">
                <label class="form-label d-block">Kategori</label>
                
                {{-- Pilihan 1: New Aplikasi --}}
                <div class="form-check form-check-inline">
                    <input class="form-check-input @error('kategori') is-invalid @enderror" type="radio" name="kategori" id="kategori_new" value="New Aplikasi" 
                           {{ old('kategori', $projectRequest->kategori) == 'New Aplikasi' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="kategori_new">New Aplikasi</label>
                </div>

                {{-- Pilihan 2: Update Aplikasi --}}
                <div class="form-check form-check-inline">
                    <input class="form-check-input @error('kategori') is-invalid @enderror" type="radio" name="kategori" id="kategori_update" value="Update Aplikasi" 
                           {{ old('kategori', $projectRequest->kategori) == 'Update Aplikasi' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="kategori_update">Update Aplikasi</label>
                </div>

                @error('kategori')
                    <div class="invalid-feedback d-block">{{ $message }}</div> 
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description', $projectRequest->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Upload Dokumen --}}
            <div class="mb-3">
                <label for="upload_file" class="form-label">Upload Dokumen (Opsional)</label>
                <input type="file" name="upload_file" id="upload_file" class="form-control @error('upload_file') is-invalid @enderror" accept=".pdf,.doc,.docx,.zip,.rar">
                @error('upload_file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                @if($projectRequest->upload_file)
                    <p class="mt-2">📎 Dokumen saat ini:
                        <a href="{{ asset('storage/' . $projectRequest->upload_file) }}" target="_blank">Lihat File</a>
                    </p>
                @endif
            </div>

            {{-- Status --}}
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="pending" {{ old('status', $projectRequest->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="team_assigned" {{ old('status', $projectRequest->status) == 'team_assigned' ? 'selected' : '' }}>Team Assigned</option>
                    <option value="approved" {{ old('status', $projectRequest->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ old('status', $projectRequest->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success px-4">
                    <i class="fa-solid fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.manager')

@section('content')
<div class="container">
    <h3 class="mb-4">Approve Project</h3>

    <form action="{{ route('manager.projects.update', $project->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nama Client</label>
            <input type="text" class="form-control" value="{{ $project->client->name }}" disabled>
        </div>

        <div class="mb-3">
            <label>Project Request</label>
            <input type="text" class="form-control" value="{{ $project->projectRequest->description ?? '-' }}" disabled>
        </div>

        <div class="mb-3">
            <label>Tanggal Mulai Project</label>
            <input type="date" name="start_date_project" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Tanggal Selesai Project</label>
            <input type="date" name="finish_date_project" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Pilih Karyawan yang Ditugaskan</label>
            <select name="karyawan_ids[]" class="form-select" multiple required>
                @foreach($karyawans as $karyawan)
                    <option value="{{ $karyawan->id }}">{{ $karyawan->name }} ({{ $karyawan->job_title }})</option>
                @endforeach
            </select>
            <small class="text-muted">Gunakan Ctrl (Windows) / Cmd (Mac) untuk memilih lebih dari satu.</small>
        </div>

        <button type="submit" class="btn btn-success">Approve & Simpan</button>
        <a href="{{ route('manager.projects.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

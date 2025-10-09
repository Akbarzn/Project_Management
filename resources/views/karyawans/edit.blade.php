@extends('layouts.manager')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Edit Karyawan</h2>

    <form action="{{ route('manager.karyawans.update', $karyawan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" value="{{ $karyawan->name }}" required>
        </div>

        <div class="mb-3">
            <label>NIK</label>
            <input type="text" name="nik" class="form-control" value="{{ $karyawan->nik }}" required>
        </div>

        <div class="mb-3">
            <label>Jabatan</label>
            <input type="text" name="jabatan" class="form-control" value="{{ $karyawan->jabatan}}">
        </div>

        <div class="mb-3">
            <label>Telepon</label>
            <input type="text" name="phone" class="form-control" value="{{ $karyawan->phone }}">
        </div>

        <div class="mb-3">
            <label>Job Title</label>
            <input type="text" name="job_title" class="form-control" value="{{ $karyawan->job_title }}" required>
        </div>

        <div class="mb-3">
            <label>Biaya</label>
            <input type="number" step="0.01" name="cost" class="form-control" value="{{ $karyawan->cost }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('manager.karyawans.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

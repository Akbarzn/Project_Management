@extends('layouts.manager')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Tambah Karyawan</h2>

    <form action="{{ route('manager.karyawans.store') }}" method="POST">
        @csrf

         <div>
            <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
            <input type="email" name="email" id="email"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                value="{{ old('email') }}" required>
        </div>

        <div>
            <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
            <input type="password" name="password" id="password"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                required>
        </div>

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>NIK</label>
            <input type="text" name="nik" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jabatan</label>
            <input type="text" name="jabatan" class="form-control">
        </div>

        <div class="mb-3">
            <label>Telepon</label>
            <input type="text" name="phone" class="form-control">
        </div>

        <div class="mb-3">
            <label>Jabatan</label>
            <input type="text" name="job_title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Biaya</label>
            <input type="number" step="0.01" name="cost" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('manager.karyawans.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

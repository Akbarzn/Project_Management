@extends('layouts.manager')

@section('content')
<div class="container">
    <h2>Edit Client</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('manager.clients.update', $client->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Nama Client</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $client->name) }}" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}" required>
        </div>

        <div class="mb-3">
            <label>NIK</label>
            <input type="text" name="nik" class="form-control" value="{{ old('nik', $client->nik) }}">
        </div>

        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone) }}">
        </div>

        <div class="mb-3">
            <label>Kode Organisasi</label>
            <input type="text" name="kode_organisasi" class="form-control" value="{{ old('kode_organisasi', $client->kode_organisasi) }}">
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('manager.clients.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

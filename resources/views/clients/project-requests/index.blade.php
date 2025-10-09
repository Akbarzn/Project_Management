@extends('layouts.client    ')

@section('content')
<div class="container">
    <h3>Daftar Project Request</h3>
<a href="{{ route('clients.project-requests.create') }}" class="btn btn-success mb-3">+ Tambah Request</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tiket</th>
                <th>Project Name</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Client</th>
                <th>Document</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($request as $r)
            <tr>
                <td>{{ $r->tiket }}</td>
                <td>{{ $r->name_project }}</td>
                <td>{{ $r->kategori }}</td>
                <td>{{ ucfirst($r->status) }}</td>
                <td>{{ $r->client->name ?? '-' }}</td>
                <td>
                   @if ($r->document)
        <a href="{{ asset('storage/' . $r->document) }}" target="_blank">Lihat Dokumen</a>
    @else
        <p>tidak ada document</p>
    @endif
                <td>
<a href="{{ route('clients.project-requests.show', $r->id) }}" class="btn btn-sm btn-info">Detail</a>
                    <a href="{{ route('clients.project-requests.edit', $r->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('clients.project-requests.destroy', $r->id) }}" method="POST" style="display:inline-block">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

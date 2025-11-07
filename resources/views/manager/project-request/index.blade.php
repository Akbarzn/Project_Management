@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow-md">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">📋 Daftar Project Request</h2>

        <a href="{{ route('manager.project-request.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
           + Buat Request Baru
        </a>
    </div>

    {{-- 🔹 Notifikasi sukses --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- 🔹 Filter status --}}
    <div class="flex gap-2 mb-5">
    {{-- @php
        $currentStatus = request('status', 'pending');
    @endphp --}}

        <a href="{{ route('manager.project-request.index', ['status' => 'pending']) }}"
           class="px-4 py-2 rounded-md text-sm font-medium border 
           {{ $status === 'pending' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Pending
        </a>

        <a href="{{ route('manager.project-request.index', ['status' => 'approve']) }}"
           class="px-4 py-2 rounded-md text-sm font-medium border 
           {{ $status === 'approve' ? 'bg-green-600 text-white border-green-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Approved
        </a>

        {{-- <a href="{{ route('manager.project-request.index', ['status' => 'rejected']) }}"
           class="px-4 py-2 rounded-md text-sm font-medium border 
           {{ $currentStatus === 'rejected' ? 'bg-red-600 text-white border-red-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Rejected
        </a> --}}
    </div>

    {{-- 🔹 Tabel daftar request --}}
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border text-center">No</th>
                    <th class="px-3 py-2 border text-center">Tiket</th>
                    <th class="px-3 py-2 border text-center">Nama Project</th>
                    <th class="px-3 py-2 border text-center">Client</th>
                    <th class="px-3 py-2 border text-center">Kategori</th>
                    <th class="px-3 py-2 border text-center">Status</th>
                    <th class="px-3 py-2 border text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($data as $req)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 border text-center">{{ $loop->iteration }}</td>
                        <td class="px-3 py-2 border text-center">{{ $req->tiket }}</td>
                        <td class="px-3 py-2 border">{{ $req->name_project }}</td>
                        <td class="px-3 py-2 border">{{ $req->client->name ?? '-' }}</td>
                        <td class="px-3 py-2 border text-center">{{ $req->kategori }}</td>

                        <td class="px-3 py-2 border text-center">
                            @if($req->status === 'pending')
                                <span class="text-yellow-600 font-medium">Pending</span>
                            @elseif($req->status === 'approve')
                                <span class="text-green-600 font-medium">Approved</span>
                            @elseif($req->status === 'rejected')
                                <span class="text-red-600 font-medium">Rejected</span>
                            @endif
                        </td>

                        <td class="px-3 py-2 border text-center space-x-1">
                            <div class="block ">

                                <a href="{{ route('manager.project-request.show', $req->id) }}"
                                   class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs">
                                   Detail
                                </a>
    
                                <a href="{{ route('manager.project-request.edit', $req->id) }}"
                                   class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-xs">
                                   Edit
                                </a>
                                
                                <form action="{{ route('manager.project-request.destroy', $req->id) }}"
                                      method="POST" class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus request ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                                        Hapus
                                    </button>
                                </form>
                            </div>

                            <div class="mt-2">
                                @if($req->status === 'pending')
                                    <a href="{{ route('manager.projects.create.from.request', ['requestId' => $req->id]) }}"
                                       class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs">
                                       Approve
                                    </a>
                                @endif
                            </div>


                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-gray-500">
                            Tidak ada data project request {{ $status }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 🔹 Pagination --}}
    <div class="mt-4">
        {{ $data->links() }}
    </div>
</div>
@endsection

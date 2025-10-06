@extends('layouts.manager')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">📋 Daftar Client</h2>

    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('manager.clients.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
            + Tambah Client
        </a>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif
    </div>

    <div class="overflow-x-auto bg-white rounded-xl shadow-lg border border-gray-200">
        <table class="min-w-full text-sm text-left text-gray-600">
            <thead class="bg-gray-100 text-gray-800 text-sm uppercase tracking-wide">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Phone</th>
                    <th class="px-6 py-3">Kode Organisasi</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr class="border-t hover:bg-gray-50 transition">
                        <td class="px-6 py-3 font-medium text-gray-700">{{ $client->id }}</td>
                        <td class="px-6 py-3">{{ $client->name }}</td>
                        <td class="px-6 py-3">{{ $client->user->email ?? 'Tidak ada' }}</td>
                        <td class="px-6 py-3">{{ $client->phone ?? '-' }}</td>
                        <td class="px-6 py-3">{{ $client->kode_organisasi ?? '-' }}</td>
                        <td class="px-6 py-3 text-center flex justify-center gap-2">
                            <a href="{{ route('manager.clients.edit', $client->id) }}" 
                               class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-md transition">
                                ✏️ Edit
                            </a>
                            <form action="{{ route('manager.clients.destroy', $client->id) }}" method="POST" 
                                  onsubmit="return confirm('Yakin ingin menghapus client ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md transition">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-6 text-gray-500">
                            Tidak ada data client.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

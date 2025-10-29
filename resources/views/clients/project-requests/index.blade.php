@extends('layouts.client')

@section('content')
<div class="container mx-auto px-4 py-6">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Daftar Project Request</h2>
        <a href="{{ route('clients.project-requests.create') }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow-sm transition">
            + Tambah Request
        </a>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-indigo-500">
                <tr>
                    <th class="px-4 py-2 text-left text-white font-medium">Tiket</th>
                    <th class="px-4 py-2 text-left text-white font-medium">Project Name</th>
                    <th class="px-4 py-2 text-left text-white font-medium">Kategori</th>
                    <th class="px-4 py-2 text-left text-white font-medium">Status</th>
                    <th class="px-4 py-2 text-left text-white font-medium">Document</th>
                    <th class="px-4 py-2 text-center text-white font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($requests as $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $r->tiket }}</td>
                    <td class="px-4 py-2">{{ $r->name_project }}</td>
                    <td class="px-4 py-2">{{ ucfirst($r->kategori) }}</td>
                    <td class="px-4 py-2">
                        @if($r->status === 'pending')
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">{{ ucfirst($r->status) }}</span>
                        @elseif($r->status === 'approved')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">{{ ucfirst($r->status) }}</span>
                        @elseif($r->status === 'rejected')
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">{{ ucfirst($r->status) }}</span>
                        @else
                            <span>{{ ucfirst($r->status) }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        @if ($r->document)
                            <a href="{{ asset('storage/' . $r->document) }}" target="_blank"
                               class="text-blue-600 hover:underline">Lihat Dokumen</a>
                        @else
                            <span class="text-gray-400">Tidak ada dokumen</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 flex justify-center gap-2">
                        <a href="{{ route('clients.project-requests.edit', $r->id) }}"
                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">Edit</a>
                        <form action="{{ route('clients.project-requests.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus request ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                        Belum ada project request.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $requests->links() }}
    </div>
</div>
@endsection

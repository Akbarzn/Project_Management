@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Daftar Project Request</h2>
        <a href="{{ route('clients.project-requests.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow transition">
            + Tambah Request
        </a>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto bg-white shadow-xl rounded-xl border border-gray-200">
        <table class="min-w-full table-fixed divide-y divide-gray-200">
            <thead class="bg-indigo-600">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white w-28">Tiket</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white w-48">Project Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white w-32">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white w-32">Status</th>

                    {{-- Description kolom panjang pakai truncate --}}
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Description</th>

                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white w-32">Document</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-white w-40">Aksi</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">

                @forelse ($data as $requestProject)
                <tr class="hover:bg-gray-50 transition duration-150">

                    {{-- Tiket --}}
                    <td class="px-4 py-2 text-sm text-gray-700">
                        {{ $requestProject->tiket }}
                    </td>

                    {{-- Nama Project --}}
                    <td class="px-4 py-2 text-sm font-medium text-gray-800 truncate">
                        {{ $requestProject->name_project }}
                    </td>

                    {{-- Kategori --}}
                    <td class="px-4 py-2 text-sm text-gray-700">
                        {{ ucfirst($requestProject->kategori) }}
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-2 text-sm">
                        @php
                            $statusClass = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approve' => 'bg-green-100 text-green-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                            ][$requestProject->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp

                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                            {{ ucfirst($requestProject->status) }}
                        </span>
                    </td>

                    {{-- Description Truncate + Tooltip --}}
                    <td class="px-4 py-2 max-w-xs truncate text-sm text-gray-700 relative group">

                        {{ $requestProject->description }}

                        {{-- Tooltip --}}
                        <div class="absolute hidden group-hover:block bg-gray-900 text-white text-xs rounded px-3 py-2 w-72
                                    left-0 top-8 shadow-lg z-50">
                            {{ $requestProject->description }}
                        </div>
                    </td>

                    {{-- Document --}}
                    <td class="px-4 py-2 text-sm">
                        @if ($requestProject->document)
                            <a href="{{ asset('storage/'.$requestProject->document) }}" target="_blank"
                               class="text-indigo-600 hover:underline">
                                Lihat Dokumen
                            </a>
                        @else
                            <span class="text-gray-400">Tidak ada</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-4 py-2 text-sm">
                        <div class="flex justify-center gap-2">

                            {{-- Show --}}
                            <a href="{{ route('clients.project-requests.show', $requestProject->id) }}"
                               class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs shadow">
                                Detail
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('clients.project-requests.edit', $requestProject->id) }}"
                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs shadow">
                                Edit
                            </a>

                            {{-- Delete --}}
                            <form action="{{ route('clients.project-requests.destroy', $requestProject->id) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus request ini?')">
                                @csrf @method('DELETE')
                                <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs shadow">
                                    Hapus
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>

                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500 text-sm">
                        Belum ada project request.
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $data->links() }}
    </div>

</div>
@endsection

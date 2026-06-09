@extends('layouts.app')
@section('title', 'Daftar Project Request')

@section('content')

    <div class="max-w-7xl mx-auto px-6 py-10 space-y-8">

        {{-- HEADER --}}
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-800 flex items-center gap-3">
                    <i class="fas fa-envelope-open-text text-indigo-600"></i>
                    Daftar Project Request
                </h2>
                <p class="text-gray-500 text-sm mt-1">
                    Semua permintaan project dari client yang perlu ditinjau.
                </p>
            </div>

            <a href="{{ route('manager.project-request.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-5 rounded-lg shadow-lg shadow-indigo-300/40 transition">
                <i class="fas fa-plus mr-2"></i>
                Buat Request Baru
            </a>
        </div>

        {{-- SUCCESS MESSAGE --}}
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm">
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        {{-- TABLE --}}
        @if ($data->count() > 0)

            <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-indigo-600 text-white uppercase text-xs tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-center w-10">No</th>
                                <th class="px-4 py-3 text-center">Tiket</th>
                                <th class="px-4 py-3 text-left">Nama Project</th>
                                <th class="px-4 py-3 text-left">Client</th>
                                <th class="px-4 py-3 text-center">Kategori</th>
                                {{-- Kolom baru --}}
                                <th class="px-4 py-3 text-center">Priority</th>
                                <th class="px-4 py-3 text-center">Difficulty</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Dokumen</th>
                                <th class="px-4 py-3 text-center w-32">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">

                            @foreach ($data as $req)
                                <tr class="hover:bg-gray-50 transition">

                                    {{-- No --}}
                                    <td class="px-4 py-3 text-center text-gray-600">
                                        {{ $data->firstItem() + $loop->index }}
                                    </td>

                                    {{-- Tiket --}}
                                    <td class="px-4 py-3 text-center font-mono text-gray-800">
                                        {{ $req->tiket }}
                                    </td>

                                    {{-- Nama Project --}}
                                    <td class="px-4 py-3 font-semibold text-gray-900">
                                        {{ $req->name_project }}
                                    </td>

                                    {{-- Client --}}
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ $req->client->name ?? '-' }}
                                    </td>

                                    {{-- Kategori --}}
                                    <td class="px-4 py-3 text-center text-gray-700">
                                        {{ $req->kategori }}
                                    </td>

                                    {{-- Priority Badge --}}
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $priorityBadge = [
                                                1 => 'bg-gray-100 text-gray-800 border border-gray-200',
                                                2 => 'bg-blue-100 text-blue-800 border border-blue-200',
                                                3 => 'bg-orange-100 text-orange-800 border border-orange-200',
                                                4 => 'bg-red-100 text-red-800 border border-red-200',
                                            ][$req->priority] ?? 'bg-gray-100 text-gray-800 border border-gray-200';

                                            $priorityLabel = [
                                                1 => 'Low',
                                                2 => 'Medium',
                                                3 => 'High',
                                                4 => 'Critical',
                                            ][$req->priority] ?? '-';
                                        @endphp
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $priorityBadge }}">
                                            {{ $priorityLabel }}
                                        </span>
                                    </td>

                                    {{-- Difficulty --}}
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $diffBadge = [
                                                1 => 'bg-green-100 text-green-800 border border-green-200',
                                                2 => 'bg-blue-100 text-blue-800 border border-blue-200',
                                                3 => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                                4 => 'bg-red-100 text-red-800 border border-red-200',
                                                5 => 'bg-gray-800 text-gray-100 border border-gray-700',
                                            ][$req->difficulty] ?? 'bg-gray-100 text-gray-800 border border-gray-200';

                                            $diffLabel = [
                                                1 => 'Sangat Mudah',
                                                2 => 'Mudah',
                                                3 => 'Sedang',
                                                4 => 'Sulit',
                                                5 => 'Sangat Sulit',
                                            ][$req->difficulty] ?? '-';
                                        @endphp
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $diffBadge }}">
                                            {{ $diffLabel }}
                                        </span>
                                    </td>


                                    {{-- Status --}}
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $badge =
                                                [
                                                    'pending'  => 'bg-yellow-100 text-yellow-800',
                                                    'approve'  => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                ][$req->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp

                                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $badge }}">
                                            {{ ucfirst($req->status) }}
                                        </span>
                                    </td>

                                    {{-- Dokumen --}}
                                    <td class="px-4 py-3 text-center">
                                        @if ($req->document)
                                            <a href="{{ asset('storage/' . $req->document) }}" target="_blank"
                                                class="text-indigo-600 hover:underline font-medium text-xs">
                                                Lihat
                                            </a>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                  {{-- AKSI --}}
<td class="px-4 py-3">
    <div class="flex justify-center items-center gap-2">

        {{-- DETAIL --}}
        <a href="{{ route('manager.project-request.show', $req->id) }}"
            title="Detail"
            class="w-9 h-9 rounded-lg bg-blue-500 hover:bg-blue-600 text-white flex items-center justify-center shadow transition">
            <i class="fas fa-eye"></i>
        </a>

        {{-- EDIT --}}
        <a href="{{ route('manager.project-request.edit', $req->id) }}"
            title="Edit"
            class="w-9 h-9 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white flex items-center justify-center shadow transition">
            <i class="fas fa-edit"></i>
        </a>

        {{-- DELETE --}}
        <form action="{{ route('manager.project-request.destroy', $req->id) }}"
            method="POST"
            onsubmit="return confirm('Yakin ingin menghapus request ini?');">
            @csrf
            @method('DELETE')

            <button type="submit"
                title="Hapus"
                class="w-9 h-9 rounded-lg bg-red-600 hover:bg-red-700 text-white flex items-center justify-center shadow transition">
                <i class="fas fa-trash"></i>
            </button>
        </form>

        {{-- APPROVE --}}
        @if ($req->status === 'pending')
            <a href="{{ route('manager.projects.create.from.request', ['requestId' => $req->id]) }}"
                title="Approve & Auto Assign"
                class="w-9 h-9 rounded-lg bg-green-600 hover:bg-green-700 text-white flex items-center justify-center shadow transition">
                <i class="fas fa-check"></i>
            </a>
        @endif

    </div>
</td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                </div>
            </div>

            {{-- PAGINATION --}}
            <div class="mt-6">
                {{ $data->links() }}
            </div>
        @else
            {{-- EMPTY STATE --}}
            <div class="bg-white shadow-xl rounded-xl p-12 text-center border border-gray-200">
                <div class="flex justify-center mb-4">
                    <div class="h-20 w-20 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-envelope-open-text text-indigo-600 text-3xl"></i>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-800">Tidak Ada Project Request</h3>
                <p class="text-gray-500 text-sm mt-2">
                    Belum ada request yang diajukan.
                </p>

                <a href="{{ route('manager.project-request.create') }}"
                    class="mt-5 inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Request Baru
                </a>
            </div>

        @endif

    </div>

@endsection

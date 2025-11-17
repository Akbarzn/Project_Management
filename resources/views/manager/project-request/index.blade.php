@extends('layouts.app')
@section('title', 'Daftar Project Request')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10 space-y-8">

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
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm">
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif


    {{-- TABLE --}}
    @if($data->count() > 0)

        <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">

            <table class="min-w-full text-sm">
                <thead class="bg-indigo-600 text-white uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3 text-center">Tiket</th>
                        <th class="px-4 py-3 text-left">Nama Project</th>
                        <th class="px-4 py-3 text-left">Client</th>
                        <th class="px-4 py-3 text-center">Kategori</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center w-40">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @foreach ($data as $req)

                        <tr class="hover:bg-gray-50 transition">

                            {{-- No --}}
                            <td class="px-4 py-3 text-center">
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

                            {{-- Status --}}
                            <td class="px-4 py-3 text-center">
                                @php
                                    $badge = [
                                        'pending'  => 'bg-yellow-100 text-yellow-800',
                                        'approve'  => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                    ][$req->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp

                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $badge }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3">
                                <div class="flex justify-center items-center gap-2">

                                    {{-- DETAIL --}}
                                    <a href="{{ route('manager.project-request.show', $req->id) }}"
                                       class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow transition">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>

                                    {{-- EDIT --}}
                                    <a href="{{ route('manager.project-request.edit', $req->id) }}"
                                       class="inline-flex items-center bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow transition">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>

                                    {{-- DELETE --}}
                                    <form action="{{ route('manager.project-request.destroy', $req->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus request ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                        class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow transition">
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </button>
                                    </form>

                                </div>

                                {{-- APPROVE --}}
                                @if($req->status === 'pending')
                                <a href="{{ route('manager.projects.create.from.request', ['requestId' => $req->id]) }}"
                                   class="mt-2 block bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-xs font-semibold text-center shadow transition">
                                    <i class="fas fa-check-circle mr-1"></i> Approve
                                </a>
                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

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

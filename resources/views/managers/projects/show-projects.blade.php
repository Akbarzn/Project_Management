@extends('layouts.manager')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Daftar Request Project</h2>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Client</th>
                    <th class="py-3 px-6 text-left">Deskripsi Singkat</th>
                    <th class="py-3 px-6 text-center">Status</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @foreach($requests as $r)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap">
                        <span class="font-medium">{{ $r->client->name }}</span>
                    </td>
                    <td class="py-3 px-6 text-left max-w-lg truncate" title="{{ $r->description }}">
                        {{ Str::limit($r->description, 100) }} {{-- Membatasi deskripsi agar tidak terlalu panjang --}}
                    </td>
                    <td class="py-3 px-6 text-center">
                        <span class="relative inline-block px-3 py-1 font-semibold text-orange-900 leading-tight">
                            <span aria-hidden class="absolute inset-0 bg-orange-200 opacity-50 rounded-full"></span>
                            <span class="relative">{{ $r->status }}</span>
                        </span>
                    </td>
                    <td class="py-3 px-6 text-center">
                        {{-- Tombol 'Approve' mengarah ke form create dengan request ID --}}
                        <a href="{{ route('manager.projects.create.from.request', ['requestId' => $r->id]) }}" 
                           class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                            Approve & Konfigurasi
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
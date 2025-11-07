@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Daftar Request Project</h2>
<a href="{{ route('manager.projects.index') }}" class="bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transtition duration-300 ease-in-out">Kembali</a>
    </div>


    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal  divide-gray-200">
            <thead class="bg-indigo-600 text-white">
                <tr class="bg-indigo-600 text-white uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Tiket</th>
                    <th class="py-3 px-6 text-left">Kategori</th>
                    <th class="py-3 px-6 text-left">Client</th>
                    <th class="py-3 px-6 text-left">Deskripsi Singkat</th>
                    <th class="py-3 px-6 text-center">Status</th>
                    <th class="py-3 px-6 text-center">Document</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @foreach($requests as $requestProject)
                <tr class="border-b border-gray-200 hover:bg-gray-100 text-center">
                    <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="font-medium">{{ $requestProject->tiket }}</span>
                    </td>
                    <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="font-medium">{{ $requestProject->kategori }}</span>
                    </td>
                    <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="font-medium">{{ $requestProject->client->name }}</span>
                    </td>
                    <td class="py-3 px-6 text-left max-w-lg truncate" title="{{ $requestProject->description }}">
                    {{ Str::limit($requestProject->description, 100) }} 
                    </td>
                    <td class="py-3 px-6 text-center">
                        <span class="relative inline-block px-3 py-1 font-semibold text-orange-900 leading-tight">
                            <span aria-hidden class="absolute inset-0 bg-orange-200 opacity-50 rounded-full"></span>
                            <span class="relative">{{ $requestProject->status }}</span>
                        </span>
                    </td>
                    <td class="py-3 px-6 whitespace-nowrap text-center">
                        {{-- <span class="font-medium">{{ $r->document }}</span> --}}
                        @if($requestProject->document)
                        <a class="text-blue-500 " href="{{ asset('storage/' . $requestProject->document) }}" target="_blank">Lihat Document</a>
                        @else
                        <p>Tidak Ada Document</p>
                        @endif
                    </td>
                    <td class="py-3 px-6 text-center">
                        <a href="{{ route('manager.projects.create.from.request', ['requestId' => $requestProject->id]) }}" 
                           class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                            Approve
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
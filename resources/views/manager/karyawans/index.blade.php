@extends('layouts.manager')

@section('content')

<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">
            <svg class="h-8 w-8 text-gray-800 inline-block mr-2 align-middle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Daftar Karyawan
        </h2>
        <a href="{{ route('manager.karyawans.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
            Tambah Karyawan Baru
        </a>
    </div>

    {{-- Pesan Success --}}
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 p-4 rounded-lg shadow-sm">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if($karyawans->count() > 0)
        <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">NIK</th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Telepon</th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Jabatan</th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Biaya/Jam</th>
                        <th scope="col" class="px-6 py-3 text-center text-sm font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                
                {{-- Table Body --}}
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($karyawans as $index => $karyawan)
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            {{-- Menggunakan pagination index jika ada, jika tidak, pakai loop iteration --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if (method_exists($karyawans, 'firstItem'))
                                    {{ $karyawans->firstItem() + $index }}
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ $karyawan->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $karyawan->nik }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $karyawan->phone }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $karyawan->job_title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                Rp {{ number_format($karyawan->cost, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-3">
                                
                                <a href="{{ route('manager.karyawans.edit', $karyawan->id) }}"
                                   class="inline-block bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded-md text-xs font-semibold transition duration-150 shadow-sm">
                                    Edit
                                </a>
                                
                                <form action="{{ route('manager.karyawans.destroy', $karyawan->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-xs font-semibold transition duration-150 shadow-sm"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus karyawan ini? Tindakan ini permanen.')">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            @if (method_exists($karyawans, 'links'))
                {{ $karyawans->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-12 bg-white shadow-lg rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak Ada Data Karyawan</h3>
            <p class="mt-1 text-sm text-gray-500">
                Silakan tambahkan data karyawan baru untuk memulai.
            </p>
            <div class="mt-6">
                <a href="{{ route('manager.karyawans.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Tambah Karyawan
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
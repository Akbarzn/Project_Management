@extends('layouts.manager')

@section('content')
<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Daftar Karyawan</h2>

    {{-- Tombol Tambah --}}
    <a href="{{ route('manager.karyawans.create') }}" 
       class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out mb-4">
        + Tambah Karyawan
    </a>

    {{-- Pesan Success --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Tabel Karyawan --}}
    <div class="overflow-x-auto shadow-lg sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            {{-- Header Tabel --}}
            <thead class="bg-gray-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">#</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nama</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">NIK</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Telepon</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Jabatan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Biaya</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            {{-- Body Tabel --}}
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($karyawans as $karyawan)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $karyawan->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $karyawan->nik }}</td>
                        {{-- PERHATIAN: Saya Mengatur Urutan Sesuai Header Anda (Telepon, Jabatan, Biaya) --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $karyawan->phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $karyawan->job_title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Rp {{ number_format($karyawan->cost, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            {{-- Tombol Edit --}}
                            <a href="{{ route('manager.karyawans.edit', $karyawan->id) }}" 
                               class="text-indigo-600 hover:text-indigo-900 px-3 py-1 rounded-md bg-indigo-100 hover:bg-indigo-200 transition duration-150 ease-in-out text-xs font-semibold">
                                Edit
                            </a>
                            
                            {{-- Form Hapus --}}
                            <form action="{{ route('manager.karyawans.destroy', $karyawan->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Yakin ingin hapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 px-3 py-1 rounded-md bg-red-100 hover:bg-red-200 transition duration-150 ease-in-out text-xs font-semibold">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-400 italic">
                            Belum ada data karyawan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
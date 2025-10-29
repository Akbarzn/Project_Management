@extends('layouts.manager')

@section('title', 'Users')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">
            <svg class="h-8 w-8 text-gray-800 inline-block mr-2 align-middle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a2 2 0 100 3.292 2 2 0 000-3.292zM7 11h3.5v1H7v-1zM17 11h-3.5v1H17v-1zM4 21h16a2 2 0 002-2v-4a2 2 0 00-2-2H4a2 2 0 00-2 2v4a2 2 0 002 2zM12 11a3 3 0 100 6 3 3 0 000-6z" />
            </svg>
            User Management
        </h2>
        <a href="{{ route('manager.users.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
            + Tambah User
        </a>
    </div>

    {{-- Pesan Success --}}
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 p-4 rounded-lg shadow-sm">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if($users->count() > 0)
        <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-center text-sm font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($users as $u)
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $u->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ $u->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $u->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $u->getRoleNames()->first() ?? '-' }}</td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-3">
                                
                                <a href="{{ route('manager.users.edit', $u) }}"
                                   class="inline-block bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded-md text-xs font-semibold transition duration-150 shadow-sm">
                                    Edit
                                </a>
                                
                                <form action="{{ route('manager.users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Tindakan ini permanen.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-xs font-semibold transition duration-150 shadow-sm">
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
            {{ $users->links() }}
        </div>
    @else
        {{-- Kondisi Data Kosong --}}
        <div class="text-center py-12 bg-white shadow-lg rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak Ada Data User</h3>
            <p class="mt-1 text-sm text-gray-500">
                Silakan tambahkan user baru untuk memulai manajemen pengguna.
            </p>
            <div class="mt-6">
                <a href="{{ route('manager.users.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Tambah User
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
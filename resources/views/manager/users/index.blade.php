@extends('layouts.app')

@section('title', 'User Management')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10 space-y-8">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 flex items-center gap-3">
                <i class="fas fa-users-cog text-indigo-600"></i>
                User Management
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Kelola seluruh data .
            </p>
        </div>

        <a href="{{ route('manager.users.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-5 rounded-lg shadow-lg shadow-indigo-300/40 transition">
            + Tambah User
        </a>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm">
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- TABLE --}}
    @if($users->count() > 0)

        <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">

            <table class="min-w-full text-sm">
                <thead class="bg-indigo-600 text-white uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center">ID</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-4 py-3 text-center text-gray-800 font-semibold">
                                {{ $user->id }}
                            </td>

                            <td class="px-4 py-3 font-semibold text-gray-800">
                                {{ $user->name }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $user->email }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $user->getRoleNames()->first() ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-center space-x-2">


                                {{-- EDIT --}}
                                <a href="{{ route('manager.users.edit', $user) }}"
                                   class="inline-flex items-center bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow transition">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>

                                {{-- DELETE --}}
                                <form action="{{ route('manager.users.destroy', $user) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Yakin ingin menghapus user ini?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow transition">
                                        <i class="fas fa-trash mr-1"></i> Hapus
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

        {{-- EMPTY STATE --}}
        <div class="bg-white shadow-xl rounded-xl p-12 text-center border border-gray-200">

            <div class="flex justify-center mb-4">
                <div class="h-20 w-20 bg-indigo-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-plus text-indigo-600 text-3xl"></i>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-800">Belum Ada User</h3>
            <p class="text-gray-500 text-sm mt-2">
                Tambahkan user baru untuk memulai manajemen.
            </p>

            <a href="{{ route('manager.users.create') }}"
                class="mt-5 inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-2"></i>
                Tambah User
            </a>

        </div>

    @endif

</div>

@endsection

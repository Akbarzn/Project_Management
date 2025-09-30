@extends('layouts.manager')

@section('title','Users')

@section('content')
<div class="bg-white p-4 rounded shadow">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-bold">User Management</h2>
        <a href="{{ route('manager.users.create') }}" class="bg-blue-500 text-white px-3 py-1 rounded">+ Add User</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-3">{{ session('success') }}</div>
    @endif

    <table class="w-full table-auto">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2">ID</th><th class="p-2">Name</th><th class="p-2">Email</th><th class="p-2">Role</th><th class="p-2">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
            <tr class="border-b">
                <td class="p-2">{{ $u->id }}</td>
                <td class="p-2">{{ $u->name }}</td>
                <td class="p-2">{{ $u->email }}</td>
                <td class="p-2">{{ $u->getRoleNames()->first() ?? '-' }}</td>
                <td class="p-2">
                    <a href="{{ route('manager.users.edit', $u) }}" class="text-yellow-600 mr-2">Edit</a>
                    <form action="{{ route('manager.users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Delete user?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection

@extends('layouts.manager')

@section('title','Edit User')

@section('content')
<div class="bg-white p-4 rounded shadow max-w-lg">
    <form action="{{ route('manager.users.update', $user->id) }}" method="POST">
        @csrf @method('PUT')
        <label class="block">Name</label>
        <input name="name" value="{{ old('name', $user->name) }}" class="border p-2 w-full mb-2" />
        <label class="block">Email</label>
        <input name="email" value="{{ old('email', $user->email) }}" class="border p-2 w-full mb-2" />
        <label class="block">New Password (leave blank to keep)</label>
        <input type="password" name="password" class="border p-2 w-full mb-2" />
        <label class="block">Confirm Password</label>
        <input type="password" name="password_confirmation" class="border p-2 w-full mb-2" />
        <label class="block">Role</label>
        <select name="role" class="border p-2 w-full mb-3">
            <option value="karyawan" {{ $user->hasRole('karyawan') ? 'selected' : '' }}>Karyawan</option>
            <option value="client" {{ $user->hasRole('client') ? 'selected' : '' }}>Client</option>
            <option value="manager" {{ $user->hasRole('manager') ? 'selected' : '' }}>Manager</option>
        </select>

        <button class="bg-green-500 text-white px-3 py-1 rounded">Update</button>
    </form>
</div>
@endsection

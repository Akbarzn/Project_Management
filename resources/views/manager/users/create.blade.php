@extends('layouts.manager')

@section('title','Create User')

@section('content')
<div class="bg-white p-4 rounded shadow max-w-lg">
    <form action="{{ route('manager.users.store') }}" method="POST">
        @csrf
        <label class="block">Name</label>
        <input name="name" value="{{ old('name') }}" class="border p-2 w-full mb-2" />
        <label class="block">Email</label>
        <input name="email" value="{{ old('email') }}" class="border p-2 w-full mb-2" />
        <label class="block">Password</label>
        <input type="password" name="password" class="border p-2 w-full mb-2" />
        <label class="block">Confirm Password</label>
        <input type="password" name="password_confirmation" class="border p-2 w-full mb-2" />
        <label class="block">Role</label>
        <select name="role" class="border p-2 w-full mb-3">
            <option value="karyawan">Karyawan</option>
            <option value="client">Client</option>
            <option value="manager">Manager</option>
        </select>

        @error('*') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror

        <button class="bg-blue-500 text-white px-3 py-1 rounded">Save</button>
    </form>
</div>
@endsection

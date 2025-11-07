<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 text-gray-900">

<div class="flex min-h-screen">
    {{-- Sidebar --}}
    @include('layouts.sidebar')
    {{-- <x-manager.sidebar></x-manager.sidebar> --}}

    {{-- head--}}
    <div class="flex-1 flex flex-col">
        {{-- Navbar --}}
        <nav class="bg-indigo-600 text-white shadow-md">
            <div class="flex justify-end items-center px-6 py-3">
                {{-- <h1 class="text-xl font-semibold">Panel Karyawan</h1> --}}
                <div class="flex items-center gap- py-4 max-w-3xl">
                    {{-- <a href="{{ route('karyawan.tasks.index') }}" class="hover:underline">Tugas Saya</a> --}}
                    {{-- <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                            Logout
                        </button>
                    </form> --}}
                </div>
            </div>
        </nav>

        {{-- content--}}
        <main class="flex-1 px-6 py-4">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="text-center text-gray-500 py-4 border-t">
            &copy; {{ date('Y') }} Project Management System
        </footer>
    </div>
</div>

</body>
</html>

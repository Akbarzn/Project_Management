<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Client Dashboard')</title>
    @vite('resources/css/app.css') {{-- Pastikan Tailwind CSS sudah terintegrasi --}}
</head>
<body class="bg-gray-100">

    <div class="flex h-screen">

        {{-- Sidebar --}}
        {{-- <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
            <div class="px-6 py-4 text-2xl font-bold border-b border-gray-200">
                Client Panel
            </div>
            <nav class="flex-1 px-2 py-4 space-y-2">
                <a href="{{ route('clients.project-requests.index') }}" 
                   class="block px-4 py-2 rounded hover:bg-indigo-100 @if(request()->routeIs('clients.project-requests.*')) bg-indigo-200 font-semibold @endif">
                   Dashboard
                </a>
                <a href="{{ route('profile.edit') }}">Edit profile</a>
            </nav>
            <div class="px-6 py-4 border-t border-gray-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        Logout
                    </button>
                </form>
            </div>
        </aside> --}}

        @include('layouts.sidebar')

        {{-- Konten Utama --}}
        <main class="flex-1 p-6 overflow-auto">
            @yield('content')
        </main>

    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-gray-100" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">

    {{-- Sidebar --}}
    <x-layouts.sidebar />

    {{-- Header --}}
    <x-layouts.header></x-layouts.header>

    {{-- Main content --}}
    <main class="pt-16 transition-all duration-300" :class="sidebarOpen ? 'lg:ml-56' : 'lg:ml-14'">
        <div class="p-6">
            @yield('content')
        </div>
    </main>

</body>

</html>

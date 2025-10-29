<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title') - Manager</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
     @livewireStyles
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <div class="w-64 bg-indigo-600 text-white shadow p-4 hidden md:block">
            <h3 class="font-bold mb-4">Manager</h3>
            <a href="{{ route('manager.dashboard') }}" class="block py-2">Dashboard</a>
            <a href="{{ route('manager.users.index') }}" class="block py-2">Users</a>
            <a href="{{ route('manager.clients.index') }}" class="block py-2">Clients</a>
            <a href="{{ route('manager.karyawans.index') }}" class="block py-2">Karyawans</a>
            <a href="{{ route('manager.projects.index') }}" class="block py-2">projects</a>
            <a href="{{ route('manager.project-request.index') }}" class="block py-2">Project Request</a>
        </div>

        <div class="flex-1">
            {{-- navbar --}}
            <div class="bg-indigo-600 text-white shadow p-4 flex justify-between">
                <div>@yield('title')</div>
                <div>
                    {{ auth()->user()->name ?? 'Guest' }}
                    <form method="POST" action="{{ route('logout') }}" class="inline">@csrf
                      <button class="text-sm text-red-800">Logout</button>
                    </form>
                </div>
            </div>

            <main class="p-6">
                @yield('content')

                @livewireScripts
            </main>
        </div>
    </div>
</body>
</html>

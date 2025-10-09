<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title') - Manager</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        {{-- sidebar (bisa x-sidebar jika sudah kompopnen) --}}
        <div class="w-64 bg-white shadow p-4 hidden md:block">
            <h3 class="font-bold mb-4">Manager</h3>
            <a href="{{ route('manager.users.index') }}" class="block py-2">Users</a>
            <a href="{{ route('manager.clients.index') }}" class="block py-2">Clients</a>
            <a href="{{ route('manager.karyawans.index') }}" class="block py-2">Karyawans</a>
            <a href="{{ route('manager.projects.index') }}" class="block py-2">projects</a>
            <a href="{{ route('clients.project-requests.index') }}" class="block py-2">Project Reques</a>
            {{-- <a href="{{ route('manager.dashboard') ?? '#' }}" class="block py-2">Dashboard</a> --}}
        </div>

        <div class="flex-1">
            {{-- navbar --}}
            <div class="bg-white shadow p-4 flex justify-between">
                <div>@yield('title')</div>
                <div>
                    {{ auth()->user()->name ?? 'Guest' }}
                    <form method="POST" action="{{ route('logout') }}" class="inline">@csrf
                      <button class="text-sm text-red-600">Logout</button>
                    </form>
                </div>
            </div>

            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

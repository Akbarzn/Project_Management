<div class="w-64 bg-indigo-700 text-white flex flex-col justify-between min-h-screen p-5">
    <div>
        <h3 class="text-xl font-semibold mb-8 border-b border-indigo-500 pb-3">
            {{ ucfirst(Auth::user()->roles->pluck('name')->first() ?? 'User') }}
        </h3>

        {{-- Menu Manager --}}
        @hasrole('manager')
            <a href="{{ route('manager.dashboard') }}" class="flex items-center gap-2 py-2.5 px-3 rounded-md hover:bg-indigo-800">
                🏠 <span>Dashboard</span>
            </a>
            <a href="{{ route('manager.users.index') }}" class="flex items-center gap-2 py-2.5 px-3 rounded-md hover:bg-indigo-800">
                👥 <span>Users</span>
            </a>
            <a href="{{ route('manager.clients.index') }}" class="flex items-center gap-2 py-2.5 px-3 rounded-md hover:bg-indigo-800">
                💼 <span>Clients</span>
            </a>
            <a href="{{ route('manager.karyawans.index') }}" class="flex items-center gap-2 py-2.5 px-3 rounded-md hover:bg-indigo-800">
                🧑‍💻 <span>Karyawans</span>
            </a>
            <a href="{{ route('manager.projects.index') }}" class="flex items-center gap-2 py-2.5 px-3 rounded-md hover:bg-indigo-800">
                📁 <span>Projects</span>
            </a>
            <a href="{{ route('manager.project-request.index') }}" class="flex items-center gap-2 py-2.5 px-3 rounded-md hover:bg-indigo-800">
                📨 <span>Project Request</span>
            </a>
        @endhasrole

        {{-- Menu Karyawan --}}
        @hasrole('karyawan')
            <a href="{{ route('karyawan.tasks.index') }}" class="flex items-center gap-2 py-2.5 px-3 rounded-md hover:bg-indigo-800">
                 <span>Tugas Saya</span>
            </a>
        @endhasrole

        {{-- Menu Client --}}
        @hasrole('client')
            <a href="{{ route('clients.project-requests.index') }}" class="flex items-center gap-2 py-2.5 px-3 rounded-md hover:bg-indigo-800">
                📨 <span>Project Request</span>
            </a>
        @endhasrole

        <div class=" border-t border-indigo-500 pt-2">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 py-2.5 px-3 rounded-md hover:bg-indigo-800">
                 <span>Edit Profile</span>
            </a>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2 py-2.5 px-3 rounded-md bg-red-600 hover:bg-red-700">
                🚪 <span>Logout</span>
            </button>
        </form>
    </div>

</div>

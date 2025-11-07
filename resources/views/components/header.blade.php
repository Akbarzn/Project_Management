 {{-- HEADER --}}
    <nav
        x-data
        @toggle-sidebar.window="sidebarOpen = $event.detail.open"
        :class="{ 'lg:left-56': sidebarOpen, 'lg:left-14': !sidebarOpen }"
        class="fixed top-0 right-0 mb-4 bg-white border-b border-gray-200
               h-16 flex items-center justify-between
               px-6 z-30 transition-all duration-300 ease-in-out w-full lg:w-auto"
    >
       <div class="flex items-center gap-3 w-1/3">
    <form method="GET" action="{{ url()->current() }}" class="flex items-center w-full">
        <i class="fa-solid fa-magnifying-glass text-gray-500 text-lg"></i>
        <input 
            type="text" 
            name="search"
            value="{{ request('search') }}"
            placeholder="Search..."
            class="w-full border border-gray-300 rounded-lg py-1 px-3 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >
    </form>
</div>

        <div class="relative flex items-center gap-2" x-data="{ open: false }">
            <span class="text-gray-700 font-medium hidden sm:block">
                {{ Auth::user()->name }}
            </span>

            <img src="{{ Auth::user()->potho_profile ? asset('storage/' . Auth::user()->potho_profile) : asset('images/default.jpg') }}"
                alt="Profile"
                class="w-9 h-9 rounded-full object-cover border cursor-pointer hover:ring-2 hover:ring-indigo-400 transition"
                @click="open = !open" />

            {{-- Dropdown --}}
            <div x-show="open" @click.away="open = false" x-transition
                class="absolute right-0 mt-12 w-44 bg-white border rounded-lg shadow-md z-30 py-2">
                <a href="{{ route(Auth::user()->hasRole('manager') ? 'manager.dashboard' : (Auth::user()->hasRole('client') ? 'clients.project-requests.index' : 'karyawan.tasks.index')) }}"
                   class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fa-solid fa-house text-indigo-500"></i>
                    Dashboard
                </a>

                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fa-solid fa-user-gear text-indigo-500"></i>
                    Edit Profil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                        <i class="fa-solid fa-right-from-bracket text-red-500"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>
{{-- SIDEBAR --}}
<aside x-data="{
    open: sidebarOpen,
    toggle() {
        this.open = !this.open;
        $dispatch('toggle-sidebar', { open: this.open });
    }
}" :class="{ 'w-56': sidebarOpen, 'w-14': !sidebarOpen }"
    class="border-r border-gray-200 h-screen fixed top-0 left-0 transition-all duration-300 ease-in-out z-50 flex flex-col bg-indigo-500 shadow-xl">
    <div :class="{ 'justify-between': sidebarOpen, 'justify-center': !sidebarOpen }"
        class="flex items-center p-4 h-16 border-b border-gray-100 bg-white">

        <span x-show="sidebarOpen" class="text-md font-extrabold text-indigo-700 whitespace-nowrap overflow-hidden"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-3"
            x-transition:enter-end="opacity-100 translate-x-0">
            Project Management
        </span>

        <button @click="toggle()"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition duration-150 ease-in-out"
            :title="sidebarOpen ? 'Tutup Sidebar' : 'Buka Sidebar'">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
    </div>

    <nav class="flex-1 p-2 overflow-y-auto">
        @php $currentRoute = Route::currentRouteName(); @endphp

        @role('manager')
            <x-nav-link :href="route('manager.dashboard')" :active="$currentRoute == 'manager.dashboard'">
                <i :class="{'justify-center' : !sidebarOpen}" 
                class="fa-solid fa-house mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen"
                    class="whitespace-nowrap transition-opacity duration-300 ease-in-out">Dashboard
                </span>
            </x-nav-link>

            <p x-show="open" class="uppercase text-xs font-semibold text-white py-1.5 border-t mt-2 mb-2">Manajemen</p>
            
            <x-nav-link :href="route('manager.users.index')" :active="$currentRoute == 'manager.users.index'">
                <i class="fa-solid fa-users mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen"
                class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Users
            </span>
            </x-nav-link>
            
            <x-nav-link :href="route('manager.karyawans.index')" :active="$currentRoute == 'manager.karyawans.index'">
                <i class="fa-solid fa-users mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen"
                class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Karyawans
            </span>
            </x-nav-link>
            
            <x-nav-link :href="route('manager.clients.index')" :active="$currentRoute == 'manager.clients.index'">
                <i class="fa-solid fa-handshake mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen"
                class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Clients
            </span>
            </x-nav-link>
            
            <x-nav-link :href="route('manager.tasks.index')" :active="$currentRoute == 'manager.tasks.index'">
                <i class="fa-solid fa-list-check mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen"
                class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Tasks
            </span>
            </x-nav-link>
            
            <x-nav-link :href="route('manager.projects.index')" :active="$currentRoute == 'manager.projects.index'">
                <i class="fa-solid fa-diagram-project mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen"
                class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Projects
            </span>
            </x-nav-link>
            
            <x-nav-link :href="route('manager.project-request.index')" :active="$currentRoute == 'manager.project-request.index'">
                <i class="fa-solid fa-file-signature mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen"
                class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Projects Requests 
            </span>
            </x-nav-link>

            <x-nav-link :href="route('profile.edit')" :active="$currentRoute == 'profile.edit'">
                <i class="fa-solid fa-user-pen mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen"
                class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Profile
            </span>
            </x-nav-link>
        @endrole



        {{-- <a href="#"
            class="flex items-center p-2 rounded-lg text-gray-700 hover:bg-indigo-50/70 hover:text-indigo-700 transition duration-150 ease-in-out">
            <i class="fa-solid fa-user-tie mr-3 w-5 flex-shrink-0"></i>
            <span x-show="sidebarOpen"
                class="whitespace-nowrap transition-opacity duration-300 ease-in-out">Karyawan</span>
        </a> --}}

    </nav>
</aside>

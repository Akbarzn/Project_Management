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

        <div class="flex flex-col">
            <span x-show="sidebarOpen" class="text-md text-center font-extrabold text-indigo-700 whitespace-nowrap overflow-hidden"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-3"
                x-transition:enter-end="opacity-100 translate-x-0">
                Project
            </span>
            <span x-show="sidebarOpen" class="text-md font-extrabold text-indigo-700 whitespace-nowrap overflow-hidden"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-3"
                x-transition:enter-end="opacity-100 translate-x-0">
                Management
            </span>
        </div>

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
                <i :class="{ 'justify-center': !sidebarOpen }" class="fa-solid fa-house mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap transition-opacity duration-300 ease-in-out">Dashboard
                </span>
            </x-nav-link>

            <p x-show="open" class="uppercase text-xs font-semibold text-white py-1.5 border-t mt-2 mb-2">Manajemen</p>

            <x-nav-link :href="route('manager.users.index')" :active="$currentRoute == 'manager.users.index'">
                <i class="fa-solid fa-users-cog mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Users
                </span>
            </x-nav-link>

            <x-nav-link :href="route('manager.karyawans.index')" :active="$currentRoute == 'manager.karyawans.index'">
                <i class="fa-solid fa-users mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Karyawans
                </span>
            </x-nav-link>

            <x-nav-link :href="route('manager.clients.index')" :active="$currentRoute == 'manager.clients.index'">
                <i class="fa-solid fa-user-tie text-white mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Clients
                </span>
            </x-nav-link>

            <x-nav-link :href="route('manager.tasks.index')" :active="$currentRoute == 'manager.tasks.index'">
                <i class="fa-solid fa-list-check mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Tasks
                </span>
            </x-nav-link>

            <x-nav-link :href="route('manager.projects.index')" :active="$currentRoute == 'manager.projects.index'">
                <i class="fa-solid fa-diagram-project mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Projects
                </span>
            </x-nav-link>

            <x-nav-link :href="route('manager.project-request.index')" :active="$currentRoute == 'manager.project-request.index'">
                <i class="fa-solid fa-file-signature mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Projects
                    Requests
                </span>
            </x-nav-link>

            <x-nav-link :href="route('profile.edit')" :active="$currentRoute == 'profile.edit'">
                <i class="fa-solid fa-user-pen mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Profile
                </span>
            </x-nav-link>
        @endrole

        @role('karyawan')
            <x-nav-link :href="route('karyawan.tasks.index')" :active="$currentRoute == 'karyawan.tasks.index'">
                <i class="fa-solid fa-list-check mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Tasks
                </span>
            </x-nav-link>

            <x-nav-link :href="route('profile.edit')" :active="$currentRoute == 'profile.edit'">
                <i class="fa-solid fa-user-pen mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Profile
                </span>
            </x-nav-link>
        @endrole

        @role('client')
            <x-nav-link :href="route('clients.project-requests.index')" :active="$currentRoute == 'clients.project-requests.index'">
                <i class="fa-solid fa-user-pen mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Project Request
                </span>
            </x-nav-link>

            <x-nav-link :href="route('profile.edit')" :active="$currentRoute == 'profile.edit'">
                <i class="fa-solid fa-file-signature mr-2 w-3 flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap transitio-opacity duration-300 ease-in-out">Profile
                </span>
            </x-nav-link>
        @endrole

    </nav>
</aside>

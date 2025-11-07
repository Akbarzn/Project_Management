
    @role('karyawan')
    <x-nav-link class="text-white font-semibold font-2xl" href="{{ route('karyawan.tasks.index') }}" :active="request()->routeIs('karyawan.tasks.index')">
        Task
    </x-nav-link>
    <x-nav-link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.edit')" class="text-white font-semibold font-2xl">
        Edit Profile
    </x-nav-link>
    @endrole
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

                            @php
                                // total jam kerja untuk task ini
                                // $totalHours = $task->workLogs->sum('hours');
                                // $costPerHour = $task->karyawan->cost ?? 0;
                                // $totalCost = $totalHours * $costPerHour;

                                // // hitung hanya hari kerja (Senin–Jumat)
                                // $durationDays = $task->workLogs
                                //     ->pluck('work_date')
                                //     ->unique()
                                //     ->filter(function ($date) {
                                //         $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;
                                //         return $dayOfWeek !== 6 && $dayOfWeek !== 0; // skip Sabtu & Minggu
                                //     })
                                //     ->count();

                                $pivotCost = $project->getKaryawanCost($task->karyawan_id);
                                $totalHours = $task->workLogs->sum('hours');
                                $totalCost = $totalHours * $pivotCost;

                                // hitung hari kerja (Senin–Jumat)
                                $durationDays = $task->workLogs
                                    ->pluck('work_date')
                                    ->unique()
                                    ->filter(fn($date) => !in_array(\Carbon\Carbon::parse($date)->dayOfWeek, [0, 6]))
                                    ->count();
                            @endphp

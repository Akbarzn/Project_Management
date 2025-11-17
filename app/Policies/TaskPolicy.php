<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;


class TaskPolicy{
    public function view(User $user, Task $task): bool{
        return $user->hasRole('manager') || $task->karyawan_id === $user->karyawan->id;
    }

    public function update(User $user, Task $task): bool{
        return $user->hasRole('manager') || $task->karyawan_id === $user->karyawan->id;
    }

    public function delete(User $user, Task $task):bool{
        return $user->hasRole('manager');
    }
}
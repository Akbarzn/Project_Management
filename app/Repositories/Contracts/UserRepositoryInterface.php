<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface{ 
    public function getAlluserWithFilter(?string $search = null);

    public function createUser (array $data);

    public function updateUser(array $data, User $user);

    public function deleteUser(User $user);
}
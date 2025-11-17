<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface{
    public function getAlluserWithFilter(?string $search = null);
    public function findUserById(int $id);
    public function  createUser(array $data);
    public function updateUser(array $data, $user);

    public function deleteUser($user);
}
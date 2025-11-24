<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;

class UserService
{
    protected UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllUsers(?string $search = null)
    {
        return $this->repository->getAlluserWithFilter($search);
    }

    /**
     * Cari user berdasarkan ID.
     */
    public function findUserById($id)
    {
        return $this->repository->findById($id);
    }

    public function createUser(array $data)
    {
        return $this->repository->createUser($data);
    }

    public function updateUser(array $data, $user)
    {
        return $this->repository->updateUser($data, $user);
    }

    public function deleteUser($user)
    {
        return $this->repository->deleteUser($user);
    }
}

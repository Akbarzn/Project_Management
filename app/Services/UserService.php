<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;

/**
 * UserService adalah tempat logika bisnis user disimpan.
 *
 * Bahasa pelajar:
 * - Controller kasih perintah
 * - Service yang mikir logikanya
 * - Repository yang ambil data dari database
 */
class UserService
{
    /**
     * Tempat nyimpen repository user
     */
    protected UserRepositoryInterface $repository;

    /**
     * Masukkan repository ke service
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Ambil semua user dengan search.
     * $search bisa berisi kata pencarian atau null.
     */
    public function getAllUsers(?string $search = null)
    {
        return $this->repository->getAlluserWithFilter($search);
    }

    /**
     * Cari user berdasarkan ID.
     */
    public function findUserById($id)
    {
        return $this->repository->findUserById($id);
    }

    /**
     * Buat user baru.
     * Data sudah tervalidasi di controller.
     */
    public function createUser(array $data)
    {
        return $this->repository->createUser($data);
    }

    /**
     * Update user berdasarkan data baru.
     */
    public function updateUser(array $data, $user)
    {
        return $this->repository->updateUser($data, $user);
    }

    /**
     * Hapus user.
     */
    public function deleteUser($user)
    {
        return $this->repository->deleteUser($user);
    }
}

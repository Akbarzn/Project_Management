<?php

namespace App\Services;

use App\Repositories\Contracts\KaryawanRepositoryInterface;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class KaryawanService{
    protected KaryawanRepositoryInterface $repository;

    public function __construct(KaryawanRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function listKaryawan(?string $search = null){
        return $this->repository->getAllKaryawan($search);
    }

    public function showKaryawan(int $id){
        return $this->repository->findById($id, ['user']);
    }

      public function createKaryawan(array $data): Karyawan
    {
        return DB::transaction(function () use ($data) {
            // Buat user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);

            // Assign role ke user
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('karyawan');
            }

            // Buat karyawan
            $karyawan = $this->repository->create([
                'user_id' => $user->id,
                'name' => $user->name,
                'nik' => $data['nik'],
                'jabatan' => $data['jabatan'],
                'phone' => $data['phone'],
                'job_title' => $data['job_title'],
                'cost' => $data['cost'],
            ]);

            return $karyawan->load('user');
        });
    }

    /**
     * Update data karyawan dan user
     */
    public function updateKaryawan(Karyawan $karyawan, array $data): Karyawan
    {
        return DB::transaction(function () use ($karyawan, $data) {
            $user = $karyawan->user;

            // Update user
            $userData = array_filter($data, fn($value, $key) =>
                in_array($key, ['name', 'email', 'password']) && !empty($value),
                ARRAY_FILTER_USE_BOTH
            );

            if (isset($userData['password'])) {
                $userData['password'] = bcrypt($userData['password']);
            }

            if (!empty($userData)) {
                $user->update($userData);
            }

            // Update karyawan
            $karyawanData = array_filter($data, fn($value, $key) =>
                in_array($key, ['name', 'nik', 'phone', 'job_title', 'cost', 'jabatan']) && !empty($value),
                ARRAY_FILTER_USE_BOTH
            );

            if (!empty($karyawanData)) {
                $this->repository->update($karyawan, $karyawanData);
            }

            // Pastikan role tetap 'karyawan'
            if (method_exists($user, 'syncRoles')) {
                $user->syncRoles('karyawan');
            }

            return $karyawan->fresh(['user']);
        });
    }

     public function deleteKaryawan(Karyawan $karyawan): bool
    {
        return DB::transaction(function () use ($karyawan) {
        // Pastikan user terkait ada
        if ($karyawan->user) {
            // Hapus user (jika punya)
            $karyawan->user()->delete();
        }

        // Hapus semua relasi tambahan kalau ada (misal project, task)
        if (method_exists($karyawan, 'tasks')) {
            $karyawan->tasks()->delete();
        }

        // Hapus data karyawan lewat repository
        return $this->repository->delete($karyawan);
        });
    }
}
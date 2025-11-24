<?php

namespace App\Services;

use App\Repositories\Contracts\KaryawanRepositoryInterface;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class KaryawanService
{
    protected KaryawanRepositoryInterface $repository;

    public function __construct(KaryawanRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function listKaryawan(?string $search = null, ?string $filter = null)
    {
        return $this->repository->getAllKaryawan($search, $filter);
    }

    public function showKaryawan(int $id)
    {
        return $this->repository->findById($id, ['user']);
    }

    /**
     * Summary of createKaryawan
     * buat karyawan baru
     * buat user
     * assign role karyawan
     * @param array $data
     * @return Karyawan
     */
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

            // kembalikan data lengkap dgn relasi user
            return $karyawan->load('user');
        });
    }


    /**
     * Summary of updateKaryawan
     * update data user dan karyawan
     * @param Karyawan $karyawan
     * @param array $data
     * @return Karyawan
     */
    public function updateKaryawan(Karyawan $karyawan, array $data): Karyawan
    {
        return DB::transaction(function () use ($karyawan, $data) {

            $user = $karyawan->user;

            // update user
            $user->name = $data['name'];

            if (!empty($data['email'])) {
                $user->email = $data['email'];  // email update
            }

            if (!empty($data['password'])) {
                $user->password = bcrypt($data['password']); // password update
            }

            $user->save();

            // update karyawan
            $karyawan->update([
                'name' => $data['name'],
                'nik' => $data['nik'],
                'phone' => $data['phone'],
                'job_title' => $data['job_title'],
                'jabatan' => $data['jabatan'],
                'cost' => $data['cost'],
            ]);

            return $karyawan->fresh(['user']);
        });
    }


    /**
     * Summary of deleteKaryawan
     * hapus data karyawan + user + task terkait
     * @param Karyawan $karyawan
     * @return bool
     */
    public function deleteKaryawan(Karyawan $karyawan): bool
    {
        return DB::transaction(function () use ($karyawan) {
            // cek apa ada user yg terkait
            if ($karyawan->user) {
                // jika ada hapus user 
                $karyawan->user()->delete();
            }

            // cek apa ada task yg terkait jika ada hapus
            if (method_exists($karyawan, 'tasks')) {
                $karyawan->tasks()->delete();
            }

            return $this->repository->delete($karyawan);
        });
    }
}
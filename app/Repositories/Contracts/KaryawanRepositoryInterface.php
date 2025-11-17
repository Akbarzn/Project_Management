<?php

namespace App\Repositories\Contracts;

use App\Models\Karyawan;
use Illuminate\Dabase\Eloquent\Model;

interface KaryawanRepositoryInterface{
    // ambil semua karyawan
    public function getAllKaryawan(?string $search = null);

    // cari karyawan berdasrakan id
    public function findById(int $id, array $relations = []);

    // simpan data baru
    public function create(array $data);

    // update Data karyawan
    public function update(Karyawan $karyawan, array $data): Karyawan;

    // hapus karyawan
    public function delete(Karyawan $karyawan): bool;
}
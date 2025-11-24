<?php

namespace App\Repositories\Contracts;

use App\Models\Karyawan;
use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Dabase\Eloquent\Model;

interface KaryawanRepositoryInterface  extends BaseRepositoryInterface{
    // ambil semua karyawan
    public function getAllKaryawan(?string $search = null, ?string $filter = null);

}
<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Summary of BaseRepositoryInterface
 * contract utk demua repository
 * semua repository wajib ada fungsi yg sama dgn ini
 */
interface BaseRepositoryInterface {
    // public function getAll(array $relations = [], string $orderBy = 'created_at', string $direction = 'desc');
    /**
     * Summary of findById
     * cari data berdasrkan id
     * @param int $id ID data yang ingin dicari
     * @param array $relations relasi yg ingin diload
     * @return void
     */
    public function findById(int $id, array $relations = []): ?Model;
   
    /**
     * Summary of create
     * buat data baru 
     * @param array $data
     * @return Model
     */
    public function create(array $data);

    /**
     * Summary of update
     * @param Model $model model yg akan diupdate
     * @param array $data  data baru yg ingin diubah
     * @return Model
     */
    public function update(Model $model, array $data);

    /**
     * Summary of delete
     * hapus data dari database
     * @param Model $model
     * @return bool
     */
    public function delete(Model $model): bool;
}

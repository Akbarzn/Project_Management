<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository{
    protected Model $model;
    
    public function __construct(Model $model){
        $this->model = $model;
    }
    
    public function getAll(array $relations = [], string $orderBy = 'created_at', string $direction = 'desc'){
        return $this->model->with($relations)->orderBy($orderBy, $direction)->pagination(10);
    }

    public function findById(int $id, array $relations = []){
        return $this->model->with($relations)->findOrFail($id);
    }

    public function create(array $data){
        return $this->model->create($data);
    }

    public function update(Model $model, array $data){
        $model->update($data);
        return $model;
    }

    public function delete(Model $model): bool{
        return $model->delete();
    }
}
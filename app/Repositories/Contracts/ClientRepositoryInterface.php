<?php

namespace App\Repositories\Contracts;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;

interface ClientRepositoryInterface{
    // ambil semua data client
    public function getAllClient(?string $search = null);

    // ambil data client berdasrkan id
    public function findById(int $id, $relations = []): ?Client;

    // simpan client ke DB
    public function create(array $data);

    // update client
    public function update(Model $client, array $data): Model;

// delete client
    public function delete(Client $client): bool;
}
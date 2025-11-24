<?php

namespace App\Services;

use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
class ClientService
{
    protected ClientRepositoryInterface $repository;

    public function __construct(ClientRepositoryInterface $repository)
    {
        // dependency injection service bergantung pada interface(contratc)
        $this->repository = $repository;
    }

    // ambil semua client via repository
    public function listClients(?string $search = null)
    {
        return $this->repository->getAllClient($search);
    }

    // tampilkan client berdasarkan id
    public function showClient(int $Id): ?Client
    {
        return $this->repository->findById($Id);
    }

    // buat client
    public function createClient(array $data): Client
    {
        return DB::transaction(function () use ($data) {
            // buat user baru
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);

            // beri role client
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('client');
            }

            // buat data client terhubung dgn user
            $client = $this->repository->create([
                'user_id' => $user->id,
                'name' => $user->name,
                'nik' => $data['nik'],
                'phone' => $data['phone'],
                'kode_organisasi' => $data['kode_organisasi'],
            ]);

            return $client->load('user');
        });
    }

    // update client dgn mengembalikan model sesuai repository
    public function updateClient(Client $client, array $data): Client
    {
        DB::transaction(function () use ($client, $data) {
            // update client
             $this->repository->update($client, [
                'name' => $data['name'],
                'nik' => $data['nik'],
                'phone' => $data['phone'],
                'kode_organisasi' => $data['kode_organisasi'],
            ]);

            $user = $client->user;

            // update user
            $userUpdate = [
                'name' => $data['name'] ?? $client->user->name,
                'email' => $data['email'] ?? $client->user->email,
        ];

        // update password hanya jika DIISI
        if (!empty($data['password'])) {
            $userUpdate['password'] = bcrypt($data['password']);
        }

        $user->update($userUpdate);

            // sync role
            $client->user->syncRoles('client');
        });

        return $client->fresh(['user']);
    }

    // delete dgn kembalikan true/false
    public function deleteClient(Client $client): bool
    {
        return DB::transaction(function () use ($client) {
            $client->user()->delete();
            return $client->delete();
        });
    }
}
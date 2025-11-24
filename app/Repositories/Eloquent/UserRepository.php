<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\UserRepositoryInterface;

/**
 * UserRepository = tempat khusus mengatur query database User.
 * Bahasa mudah:
 * - Semua yang berhubungan dengan query user ditaruh di sini.
 * - Biar controller tidak kotor.
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Constructor untuk mengirim model User ke BaseRepository.
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Ambil semua user dengan fitur search.
     * Bahasa pelajar:
     * - Kalau ada input pencarian, cari name/email yang mirip.
     * - Kalau tidak ada, tampilkan semua user.
     */
    public function getAlluserWithFilter(?string $search = null)
    {
        // mulai query dari BaseRepository model
        $query = $this->model->query();

        // jika user memasukkan kata pencarian
        if ($search) {
            $query->where('name', 'LIKE', "%$search%")
                  ->orWhere('email', 'LIKE', "%$search%");
        }

        // terakhir, paginasi
        return $query->paginate(10);
    }

    /**
     * Ambil user berdasarkan ID.
     * Bahasa pelajar:
     * - findOrFail = kalau tidak ada → otomatis 404.
     */
    public function findUserById(int $id)
    {
        return $this->findById($id);
    }

    /**
     * Membuat user baru + membuat data karyawan/client jika role-nya sesuai.
     */
    public function createUser(array $data)
    {
        // buat user
        $user = $this->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // assign role
        $user->assignRole($data['role']);

        // jika role = karyawan → buat data karyawan
        if ($data['role'] === 'karyawan') {
            Karyawan::create([
                'user_id' => $user->id,
                'name'    => $user->name,
                'nik' => 'KR' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                'jabatan' => 'Belum Ditentukan',
                'phone'   => '0000000000',
                'job_title' => 'Default Job',
                'cost' => 0,
            ]);
        }

        // jika role = client → buat data client
        if ($data['role'] === 'client') {
            Client::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'nik'  => 'CL' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                'kode_organisasi' => 'ORG-' . strtoupper(Str::random(4)),
                'phone' => null,
            ]);
        }

        return $user;
    }

    /**
     * Update user + logika role otomatis.
     */
    public function updateUser(array $data, $user)
    {
        // update data dasar user
        parent::update($user, [
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => !empty($data['password'])
                            ? Hash::make($data['password'])
                            : $user->password,
        ]);

        // update role
        $user->syncRoles([$data['role']]);

        /**
         * PER ROLE LOGIC
         */

        // role karyawan
        if ($data['role'] === 'karyawan') {
            $karyawan = Karyawan::where('user_id', $user->id)->first();

            if (!$karyawan) {
                Karyawan::create([
                    'user_id' => $user->id,
                    'name'    => $user->name,
                    'nik'     => 'KR' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                    'jabatan' => 'Belum Ditentukan',
                    'phone'   => '0000000000',
                    'job_title' => 'Default Job',
                    'cost' => 0,
                ]);
            } else {
                $karyawan->update(['name' => $user->name]);
            }

            Client::where('user_id', $user->id)->delete();
        }

        // role client
        if ($data['role'] === 'client') {
            $client = Client::where('user_id', $user->id)->first();

            if (!$client) {
                Client::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'nik'  => 'CL' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                    'kode_organisasi' => 'ORG-' . strtoupper(Str::random(4)),
                    'phone' => null,
                ]);
            } else {
                $client->update(['name' => $user->name]);
            }

            Karyawan::where('user_id', $user->id)->delete();
        }

        // role manager
        if ($data['role'] === 'manager') {
            Karyawan::where('user_id', $user->id)->delete();
            Client::where('user_id', $user->id)->delete();
        }

        return $user;
    }

    /**
     * Hapus user dari database.
     */
    public function deleteUser($user)
    {
        if (auth()->id() === $user->id) {
            return false;
        }

        return parent::delete($user);
    }
}

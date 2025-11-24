<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ProjectRequest;
use App\Repositories\Contracts\ProjectRequestRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectRequestService
{
    /**
     * Summary of __construct
     * inject projectrequest ke dalam service
     * @param ProjectRequestRepositoryInterface $repository
     */
    public function __construct(protected ProjectRequestRepositoryInterface $repository)
    {
    }

    /**
     * Summary of listProjectRequests
     * ambil semua project request + filter search dan status
     * @param mixed $search
     * @param mixed $status
     */
    public function listProjectRequests(?string $search,?string $status = null)
    {
        $user = Auth::user();
        $clientId = $user->hasRole('client') ? $user->client->id : null;

        return $this->repository->getAllWithFilter($search,$status, $clientId, );
    }

    /**
     * Summary of create
     * buat project request baru
     * upload document
     * tentukan client_id berdasarkan role
     * generate tiket
     * @param array $data
     * @throws \Exception
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        // cek apa ada document, jika ada maka buat
        if (isset($data['document'])) {
            $data['document'] = $data['document']->store('project_documents', 'public');
        }

       
    // tentuka client_id sesuai role
    if (Auth::user()->hasRole('client')) {
        $data['client_id'] = Auth::user()->client->id;
    } elseif (Auth::user()->hasRole('manager')) {
        // manager login harus pilih client dari form
        if (!isset($data['client_id']) || empty($data['client_id'])) {
            throw new \Exception('Pilih Client sebelum menyimpan Project Request.');
        }
    } else {
        // role tidak dikenali atau tolak akses
        throw new \Exception('Anda tidak memiliki izin untuk membuat project request.');
    }

    // generate nomor tiket
        $data['tiket'] = $this->generateTicket();

        // set status awal projetc request pending
        $data['status'] = 'pending';

        if (Auth::user()->hasRole('client')) {
            $data['client_id'] = Auth::user()->client->id;
        }

        return $this->repository->create($data);
    }

    public function update(ProjectRequest $projectRequest, array $data): ProjectRequest
    {
        // cek apa ada document baru , maka hapus documeny lama
        if (isset($data['document'])) {
            // hapus document lama jika ada
            if ($projectRequest->document) {
                Storage::disk('public')->delete($projectRequest->document);
            }
            // simpan document baru
            $data['document'] = $data['document']->store('project_documents', 'public');
        }

        return $this->repository->update($projectRequest, $data);
    }

    public function delete(ProjectRequest $projectRequest): bool
    {
        // hapus document 
        if ($projectRequest->document) {
            Storage::disk('public')->delete($projectRequest->document);
        }

        return $this->repository->delete($projectRequest);
    }

    /**
     * Summary of generateTicket
     * formatnya : 0012125
     * 3 digit nomor tiket + bulan + tahun
     * @return string
     */
    public function generateTicket(): string
    {
        $currentYear = now()->format('Y');
        $currentMounth = now()->format('m');

        // cari tiket yg tahun ini
        $lastTiket = ProjectRequest::where('tiket', 'like', '%' .$currentYear)
            ->orderBy('tiket', 'desc')
            ->first();

            // cek apa ada tiket terakhir
        if($lastTiket){
            // ambil 4 digit terakhir sebagai tahun tiket
            $lastYear = substr($lastTiket->tiket, -4);

            // ambil 3 digit pertama sebagai nomor urut
            $lastNumber = (int) substr($lastTiket->tiket,0,3);

            // cek apa tiket nya ada di tahun yg sama , jika ada maka + 1
        if($lastYear == $currentYear){
            $newNumber = str_pad($lastNumber +1, 3, '0', STR_PAD_LEFT);
        }else{
            $newNumber = '001';
        }
        }else{
            //kalo blm ada tiket sama sekali
            $newNumber = '001';
        }

        // gabungkan bulan dan tahun
        $today = $currentMounth. $currentYear;

        // gabungkan tiket dan bulan dan tahun
        return $newNumber . $today;
    }

    
}

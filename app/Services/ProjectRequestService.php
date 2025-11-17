<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ProjectRequest;
use App\Repositories\Contracts\ProjectRequestRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectRequestService
{
    public function __construct(protected ProjectRequestRepositoryInterface $repository)
    {
    }

    public function listProjectRequests(?string $search,?string $status = null)
    {
        $user = Auth::user();
        $clientId = $user->hasRole('client') ? $user->client->id : null;

        return $this->repository->getAllWithFilter($search,$status, $clientId, );
    }

    public function create(array $data)
    {
        if (isset($data['document'])) {
            $data['document'] = $data['document']->store('project_documents', 'public');
        }

       
    // 🔹 Tentukan client_id sesuai role
    if (Auth::user()->hasRole('client')) {
        // Client login → otomatis ambil ID-nya
        $data['client_id'] = Auth::user()->client->id;
    } elseif (Auth::user()->hasRole('manager')) {
        // Manager login → harus pilih client dari form
        if (!isset($data['client_id']) || empty($data['client_id'])) {
            throw new \Exception('Pilih Client sebelum menyimpan Project Request.');
        }
    } else {
        // Role tidak dikenali → tolak akses
        throw new \Exception('Anda tidak memiliki izin untuk membuat project request.');
    }

        $data['tiket'] = $this->generateTicket();
        $data['status'] = 'pending';

        if (Auth::user()->hasRole('client')) {
            $data['client_id'] = Auth::user()->client->id;
        }

        return $this->repository->create($data);
    }

    public function update(ProjectRequest $projectRequest, array $data): ProjectRequest
    {
        if (isset($data['document'])) {
            if ($projectRequest->document) {
                Storage::disk('public')->delete($projectRequest->document);
            }
            $data['document'] = $data['document']->store('project_documents', 'public');
        }

        return $this->repository->update($projectRequest, $data);
    }

    public function delete(ProjectRequest $projectRequest): bool
    {
        if ($projectRequest->document) {
            Storage::disk('public')->delete($projectRequest->document);
        }

        return $this->repository->delete($projectRequest);
    }

    public function generateTicket(): string
    {
         $currentYear = now()->format('Y');
        $currentMounth = now()->format('m');

        // cari tiket yg tahun ini
        $lastTiket = ProjectRequest::where('tiket', 'like', '%' .$currentYear)
            ->orderBy('tiket', 'desc')
            ->first();

        if($lastTiket){
            $lastYear = substr($lastTiket->tiket, -4);
            $lastNumber = (int) substr($lastTiket->tiket,0,3);

        if($lastYear == $currentYear){
            $newNumber = str_pad($lastNumber +1, 3, '0', STR_PAD_LEFT);
        }else{
            $newNumber = '001';
        }
        }else{
            //kalo blm ada tiket sama sekali
            $newNumber = '001';
        }

        $today = $currentMounth. $currentYear;

        return $newNumber . $today;
    }

    
}

<?php

namespace App\Repositories\Eloquent;

use App\Models\Karyawan;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\KaryawanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class KaryawanRepository extends BaseRepository implements KaryawanRepositoryInterface
{
    /**
     * Inject model Karyawan ke BaseRepository.
     * BaseRepository menyimpan model ini di $this->model.
     */
    public function __construct(Karyawan $model)
    {
        parent::__construct($model);
    }

    /**
     * Ambil semua karyawan dengan filter search dan status task.
     * Mendukung paginasi (10 per halaman) dan eager load relasi user.
     */
    public function getAllKaryawan(?string $search = null, ?string $filter = null)
    {
        $query = $this->model->with('user');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%')
                    ->orWhere('job_title', 'like', '%' . $search . '%')
                    ->orWhere('jabatan', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('email', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($filter === 'with-task') {
            $query->whereHas('tasks');
        }

        if ($filter === 'no-task') {
            $query->whereDoesntHave('tasks');
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    /**
     * Ambil karyawan berdasarkan skill yang dibutuhkan project.
     *
     * @param string|null $skill  Skill yang dicari
     * @return Collection<int, Karyawan>
     */
    public function getAvailableBySkill(?string $skill): Collection
    {
        $query = $this->model
            ->with([
                // Hanya task yang aktif (bukan complete) untuk kalkulasi workload
                'tasks' => fn($q) => $q->whereIn('status', ['pending', 'inwork']),
                // Work logs dari task aktif untuk hitung total jam
                'tasks.workLogs',
                // Project aktif untuk hitung jumlah project × 5
                'projects' => fn($q) => $q->where('status', 'ongoing'),
            ]);

        // Filter skill hanya jika skill diberikan dan tidak kosong
        if (!empty($skill)) {
            $driver = \Illuminate\Support\Facades\DB::getDriverName();

            $query->where(function ($q) use ($skill, $driver) {
                if ($driver === 'mysql' || $driver === 'mariadb') {
                    $q->whereRaw('JSON_CONTAINS(skills, ?)', [json_encode($skill)]);
                } else {
                    $q->where('skills', 'LIKE', '%"' . addslashes($skill) . '"%');
                }
            });
        }

        return $query->get();
    }

    /**
     * Ambil semua karyawan yang memiliki job_title sesuai role yang diminta.
     */
    public function getCandidatesByRole(string $role): Collection
    {
        $roles = [$role];

        return $this->model->with([
            'tasks' => fn($q) => $q->whereIn('status', ['pending', 'inwork']),
            'tasks.workLogs',
            'projects' => fn($q) => $q->where('status', 'ongoing'),
        ])
        ->whereIn('job_title', $roles)
        ->get();
    }

    /**
     * Saring koleksi kandidat berdasarkan skill.
     */
    public function filterBySkill(Collection $candidates, ?string $skill): Collection
    {
        if (empty($skill)) {
            return $candidates;
        }

        $filtered = $candidates->filter(function ($karyawan) use ($skill) {
            return $karyawan->hasSkill($skill);
        });

        // Fallback jika tidak ada kandidat yang memiliki skill tersebut
        if ($filtered->isEmpty()) {
            return $candidates;
        }

        return $filtered;
    }

    /**
     * Saring koleksi kandidat berdasarkan sekumpulan level.
     */
    public function filterByLevel(Collection $candidates, array $levels): Collection
    {
        if (empty($levels)) {
            return $candidates;
        }

        return $candidates->filter(function ($karyawan) use ($levels) {
            return in_array($karyawan->level, $levels);
        });
    }

    /**
     * Dapatkan satu kandidat dengan total workload terendah.
     */
    public function getLeastLoadCandidate(Collection $candidates)
    {
        if ($candidates->isEmpty()) {
            return null;
        }

        $workloadService = app(\App\Services\WorkloadService::class);

        return $candidates->sortBy(function ($karyawan) use ($workloadService) {
            return $workloadService->calculateWorkload($karyawan);
        })->first();
    }

    // Override dari BaseRepository untuk type-hint yang lebih spesifik

    public function findById(int $id, $relations = []): ?Karyawan
    {
        return parent::findById($id, $relations);
    }

    public function update(Model $karyawan, array $data): Karyawan
    {
        return parent::update($karyawan, $data);
    }

    public function delete(Model $karyawan): bool
    {
        return parent::delete($karyawan);
    }
}
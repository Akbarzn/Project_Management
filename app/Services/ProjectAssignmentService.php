<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\Project;
use App\Repositories\Contracts\KaryawanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * ProjectAssignmentService
 *
 * Bertanggung jawab untuk logika bisnis penugasan karyawan ke project
 * menggunakan algoritma Workload Balancing.
 *
 * Algoritma Auto-Assignment (urutan langkah):
 *  1. Ambil karyawan yang memiliki skill sesuai required_skill project
 *  2. Jika tidak ada yang cocok (skill tidak match) → FALLBACK: ambil semua karyawan
 *  3. Filter: buang karyawan yang sudah overloaded
 *  4. Sort: urutkan berdasarkan workload terkecil (paling ringan bebannya)
 *  5. Assign: pilih karyawan hasil sort, attach ke pivot table, generate tasks
 *
 * Kelas ini bergantung pada:
 * - KaryawanRepositoryInterface : untuk query karyawan berdasarkan skill
 * - WorkloadService             : untuk kalkulasi dan filter workload
 */
class ProjectAssignmentService
{
    public function __construct(
        protected KaryawanRepositoryInterface $karyawanRepository,
        protected WorkloadService $workloadService
    ) {}

    /**
     * Ambil daftar kandidat karyawan yang direkomendasikan untuk project tertentu.
     *
     * Method ini digunakan oleh endpoint "suggest" (GET) agar manager dapat
     * melihat rekomendasi sebelum melakukan assignment. Tidak ada data yang diubah.
     *
     * Mengembalikan Collection berisi karyawan yang sudah:
     * - Difilter berdasarkan skill (atau fallback semua karyawan)
     * - Difilter: tidak overloaded
     * - Diurutkan: workload terkecil di posisi pertama
     * - Dilengkapi: data workload summary untuk tampilan
     *
     * @param Project $project
     * @return Collection<int, Karyawan>
     */
    public function getSuggestedCandidates(Project $project): Collection
    {
        $requiredSkill = $project->required_skill;

        // STEP 1: Ambil karyawan berdasarkan skill
        $candidates = $this->karyawanRepository->getAvailableBySkill($requiredSkill);

        // STEP 2: FALLBACK — jika tidak ada yang memiliki skill cocok,
        // ambil semua karyawan agar project tetap bisa diassign
        $usedFallback = false;
        if ($candidates->isEmpty()) {
            $candidates   = $this->karyawanRepository->getAvailableBySkill(null);
            $usedFallback = true;
        }

        // STEP 3: Filter karyawan yang overloaded
        // Karyawan yang workload-nya >= max_workload tidak direkomendasikan
        $eligible = $candidates->reject(
            fn(Karyawan $k) => $this->workloadService->isOverloaded($k)
        );

        // STEP 4: Sort berdasarkan workload terkecil (ascending)
        // Karyawan dengan beban paling ringan ada di indeks 0
        $sorted = $eligible->sortBy(
            fn(Karyawan $k) => $this->workloadService->calculateWorkload($k)
        )->values(); // reset indeks agar mulai dari 0

        return $sorted;
    }

    /**
     * Ambil daftar kandidat beserta data workload summary-nya.
     *
     * Method ini digunakan oleh endpoint API "suggest" yang mengembalikan JSON.
     * Setiap item dalam array berisi data karyawan + workload metrics.
     *
     * @param Project $project
     * @return array<int, array<string, mixed>>
     */
    public function getCandidatesWithWorkload(Project $project): array
    {
        $candidates = $this->getSuggestedCandidates($project);

        return $candidates->map(function (Karyawan $karyawan) use ($project) {
            $summary = $this->workloadService->getWorkloadSummary($karyawan);

            // Tambahkan flag apakah skill karyawan cocok dengan kebutuhan project
            $summary['skill_match'] = !empty($project->required_skill)
                ? $karyawan->hasSkill($project->required_skill)
                : true;

            return $summary;
        })->values()->toArray();
    }

    /**
     * Lakukan auto-assignment: pilih karyawan terbaik dan assign ke project.
     *
     * Method ini melakukan perubahan data (DB write), sehingga menggunakan
     * DB::transaction untuk memastikan atomicity. Jika salah satu operasi
     * gagal, seluruh perubahan akan di-rollback.
     *
     * Langkah dalam transaksi:
     * 1. Dapatkan kandidat terbaik dari getSuggestedCandidates()
     * 2. Attach karyawan terpilih ke tabel pivot karyawan_projects
     * 3. Generate task untuk setiap karyawan yang diassign
     *
     * @param Project $project  Project yang akan diassign karyawannya
     * @param int     $limit    Maksimal jumlah karyawan yang diassign (default: 1)
     * @return array{
     *   assigned: array<int, array<string, mixed>>,
     *   fallback_used: bool,
     *   message: string
     * }
     * @throws \RuntimeException  Jika tidak ada karyawan yang tersedia sama sekali
     */
    public function autoAssign(Project $project, int $limit = 1): array
    {
        return DB::transaction(function () use ($project, $limit) {

            // Ambil kandidat yang sudah difilter dan diurutkan
            $candidates = $this->getSuggestedCandidates($project);

            // Cek apakah ada kandidat yang tersedia
            if ($candidates->isEmpty()) {
                throw new \RuntimeException(
                    'Tidak ada karyawan yang tersedia untuk project ini. ' .
                    'Semua karyawan mungkin sedang overloaded.'
                );
            }

            // Apakah fallback digunakan? Cek dengan membandingkan skill
            $usedFallback = !empty($project->required_skill)
                && !$candidates->first()?->hasSkill($project->required_skill ?? '');

            // Ambil karyawan terbaik sesuai limit
            // take() mengambil N pertama dari collection yang sudah terurut
            $selected = $candidates->take($limit);

            // STEP 5: Assign ke project dan generate tasks
            foreach ($selected as $karyawan) {
                // Attach ke pivot table karyawan_projects
                // Cek dulu apakah sudah terdaftar untuk menghindari duplicate
                $alreadyAssigned = $project->karyawans()
                    ->where('karyawan_id', $karyawan->id)
                    ->exists();

                if (!$alreadyAssigned) {
                    // Simpan snapshot cost dan job_title saat di-assign
                    // agar data historis tidak berubah walau karyawan diupdate
                    $project->karyawans()->attach($karyawan->id, [
                        'cost_snapshot'      => $karyawan->cost,
                        'job_title_snapshot' => $karyawan->job_title,
                        'role'               => $karyawan->job_title,
                        'assigned_by_system' => true,
                        'task_weight'        => 0,
                        'projected_workload' => 0,
                        'fallback_used'      => false,
                        'fallback_note'      => null,
                    ]);
                    
                    // Generate task untuk karyawan ini dalam project
                    $project->tasks()->create([
                        'karyawan_id' => $karyawan->id,
                        'catatan'     => 'Auto-assigned berdasarkan workload balancing',
                        'status'      => 'pending',
                        'progress'    => 0,
                    ]);
                }
            }

            // Siapkan response data
            $assignedData = $selected->map(
                fn(Karyawan $k) => $this->workloadService->getWorkloadSummary($k)
            )->values()->toArray();

            return [
                'assigned'      => $assignedData,
                'fallback_used' => $usedFallback,
                'message'       => $usedFallback
                    ? 'Karyawan diassign menggunakan fallback (tidak ada skill yang cocok).'
                    : 'Karyawan berhasil diassign berdasarkan skill dan workload.',
            ];
        });
    }

    /**
     * Lakukan manual assignment: manager memilih karyawan secara eksplisit.
     *
     * Berbeda dengan autoAssign(), di sini manager menentukan sendiri siapa
     * yang akan diassign. Sistem tetap memvalidasi agar karyawan yang dipilih
     * tidak dalam kondisi overloaded (kecuali di-override dengan $forceAssign).
     *
     * @param Project   $project       Project target
     * @param array<int> $karyawanIds  ID karyawan yang akan diassign
     * @param bool      $forceAssign   Jika true, abaikan validasi overload
     * @return array{
     *   assigned: array,
     *   rejected: array,
     *   message: string
     * }
     */
    public function manualAssign(Project $project, array $karyawanIds, bool $forceAssign = false): array
    {
        return DB::transaction(function () use ($project, $karyawanIds, $forceAssign) {

            $assigned = [];
            $rejected = [];

            foreach ($karyawanIds as $karyawanId) {
                $karyawan = Karyawan::find($karyawanId);

                if (!$karyawan) {
                    $rejected[] = [
                        'id'     => $karyawanId,
                        'reason' => 'Karyawan tidak ditemukan.',
                    ];
                    continue;
                }

                // Validasi overload (bisa di-bypass dengan $forceAssign)
                if (!$forceAssign && $this->workloadService->isOverloaded($karyawan)) {
                    $summary    = $this->workloadService->getWorkloadSummary($karyawan);
                    $rejected[] = [
                        'id'             => $karyawanId,
                        'name'           => $karyawan->name,
                        'reason'         => 'Karyawan sudah overloaded.',
                        'workload_score' => $summary['workload_score'],
                        'max_workload'   => $summary['max_workload'],
                    ];
                    continue;
                }

                // Cek duplikat assignment
                $alreadyAssigned = $project->karyawans()
                    ->where('karyawan_id', $karyawan->id)
                    ->exists();

                if ($alreadyAssigned) {
                    $rejected[] = [
                        'id'     => $karyawanId,
                        'name'   => $karyawan->name,
                        'reason' => 'Karyawan sudah diassign ke project ini.',
                    ];
                    continue;
                }

                // Attach ke pivot
                $project->karyawans()->attach($karyawan->id, [
                    'cost_snapshot'      => $karyawan->cost,
                    'job_title_snapshot' => $karyawan->job_title,
                    'role'               => $karyawan->job_title,
                    'assigned_by_system' => false,
                    'task_weight'        => 0,
                    'projected_workload' => 0,
                    'fallback_used'      => false,
                    'fallback_note'      => null,
                ]);

                // Generate task
                $project->tasks()->create([
                    'karyawan_id' => $karyawan->id,
                    'catatan'     => '-',
                    'status'      => 'pending',
                    'progress'    => 0,
                ]);

                $assigned[] = $this->workloadService->getWorkloadSummary($karyawan);
            }

            $message = count($assigned) > 0
                ? count($assigned) . ' karyawan berhasil diassign.'
                : 'Tidak ada karyawan yang berhasil diassign.';

            if (count($rejected) > 0) {
                $message .= ' ' . count($rejected) . ' karyawan ditolak.';
            }

            return compact('assigned', 'rejected', 'message');
        });
    }
}

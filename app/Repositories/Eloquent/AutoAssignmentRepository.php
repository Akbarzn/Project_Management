<?php

namespace App\Repositories\Eloquent;

use App\Models\Karyawan;
use App\Repositories\Contracts\AutoAssignmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * AutoAssignmentRepository
 *
 * Implementasi Eloquent untuk query kandidat karyawan
 * dalam fitur Full Automatic Assignment.
 *
 * Semua query di sini TIDAK mengandung logika bisnis;
 * hanya raw data retrieval. Seluruh keputusan bisnis
 * (fallback, pemilihan kandidat) dilakukan di AutoAssignmentService.
 */
class AutoAssignmentRepository implements AutoAssignmentRepositoryInterface
{
    /**
     * Ambil karyawan berdasarkan role (job_title) dan level.
     *
     * Eager-load relasi:
     * - tasks (aktif)          → untuk kalkulasi jam kerja
     * - tasks.workLogs         → detail log jam per task
     * - projects (ongoing)     → untuk penalti jumlah project aktif
     *
     * @param string      $role
     * @param string|null $level  Jika null → ambil semua level
     * @return Collection<int, Karyawan>
     */
    public function getRoleCandidates(string $role, ?string $level = null): Collection
    {
        $query = Karyawan::with([
            // Hanya task aktif yang mempengaruhi workload saat ini
            'tasks'          => fn($q) => $q->whereIn('status', ['pending', 'inwork']),
            'tasks.workLogs',
            // Hanya project yang sedang berjalan
            'projects'       => fn($q) => $q->where('status', 'ongoing'),
        ])
        // Filter berdasarkan job_title yang cocok dengan role
        ->where('job_title', $role);

        // Filter berdasarkan level jika diberikan
        if (!empty($level)) {
            $query->where('level', $level);
        }

        return $query->get();
    }

    /**
     * Hitung workload aktif seorang karyawan.
     *
     * @param int $karyawanId
     * @return float
     */
    public function calculateWorkload(int $karyawanId): float
    {
        // Jumlah project aktif (ongoing)
        $activeProjects = (int) DB::table('karyawan_projects as kp')
            ->join('projects as p', 'kp.project_id', '=', 'p.id')
            ->where('kp.karyawan_id', $karyawanId)
            ->where('p.status', 'ongoing')
            ->count();

        return (float) $activeProjects;
    }

    /**
     * Pilih kandidat terbaik berdasarkan Least Load algorithm.
     *
     * Algoritma Baru:
     * 1. Untuk setiap kandidat, hitung workload_score
     * 2. Pilih kandidat dengan workload_score terkecil
     * 3. Kembalikan data kandidat beserta workload-nya
     *
     * @param Collection<int, Karyawan> $candidates
     * @param int                       $taskWeight
     * @return array{karyawan: Karyawan, current_workload: float, projected_workload: float}|null
     */
    public function getBestCandidate(Collection $candidates, int $taskWeight): ?array
    {
        if ($candidates->isEmpty()) {
            return null;
        }

        $best             = null;
        $bestWorkloadLoad = PHP_FLOAT_MAX;

        foreach ($candidates as $karyawan) {
            $currentWorkload = $this->calculateWorkload($karyawan->id);

            // Pilih berdasarkan workload terkecil langsung (konsep Least Load)
            if ($currentWorkload < $bestWorkloadLoad) {
                $bestWorkloadLoad = $currentWorkload;
                $best = [
                    'karyawan'           => $karyawan,
                    'current_workload'   => $currentWorkload,
                    'projected_workload' => $currentWorkload, // tanpa taskWeight karena tidak masuk perhitungan workload
                ];
            }
        }

        return $best;
    }
}

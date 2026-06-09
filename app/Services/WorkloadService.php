<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Repositories\Contracts\WorkloadRepositoryInterface;

/**
 * WorkloadService
 *
 * Service yang bertanggung jawab untuk:
 * 1. Menghitung workload aktif seorang karyawan (dalam jam)
 * 2. Menentukan apakah karyawan overloaded
 *
 * Formula Workload (sesuai spesifikasi skripsi):
 *   Workload = total_jam_kerja_aktif + (jumlah_project_aktif × 5)
 *
 * Penjelasan formula:
 * - total_jam_kerja_aktif : total jam dari task_work_logs pada task yg belum complete
 * - jumlah_project_aktif  : jumlah project ongoing yang diikuti karyawan
 * - × 5                   : penalti 5 jam per project (overhead koordinasi)
 *
 * Kelas ini TIDAK mengakses database secara langsung.
 * Semua query dilakukan melalui WorkloadRepositoryInterface.
 */
class WorkloadService
{
    public function __construct(
        protected WorkloadRepositoryInterface $workloadRepository
    ) {}

    /**
     * Hitung total workload karyawan menggunakan formula baru:
     *   Workload = Jumlah Active Projects (Ongoing)
     *
     * @param Karyawan $karyawan  Instance karyawan yang akan dihitung
     * @return float  Total workload score (jumlah project ongoing)
     */
    public function calculateWorkload(Karyawan $karyawan): float
    {
        return (float) $this->getActiveProjectsCount($karyawan);
    }

    /**
     * Dapatkan jumlah project ongoing yang ditangani karyawan.
     */
    public function getActiveProjectsCount(Karyawan $karyawan): int
    {
        return $this->workloadRepository->getActiveProjectCount($karyawan->id);
    }

    /**
     * Dapatkan status workload berdasarkan jumlah project ongoing.
     *
     * - 0 Project: Tidak Ada Beban
     * - 1 Project: Ringan
     * - 2 Project: Normal
     * - 3 Project: Tinggi
     * - >= 4 Project: Overload
     */
    public function getWorkloadStatus(Karyawan $karyawan): string
    {
        $count = $this->getActiveProjectsCount($karyawan);

        if ($count === 0) {
            return 'Tidak Ada Beban';
        } elseif ($count === 1) {
            return 'Ringan';
        } elseif ($count === 2) {
            return 'Normal';
        } elseif ($count === 3) {
            return 'Tinggi';
        } else {
            return 'Overload';
        }
    }

    /**
     * Periksa apakah karyawan sudah overloaded.
     *
     * Karyawan dianggap overloaded jika memiliki 4 atau lebih project ongoing.
     *
     * @param Karyawan $karyawan
     * @return bool  true jika overloaded, false jika masih bisa menerima tugas
     */
    public function isOverloaded(Karyawan $karyawan): bool
    {
        return $this->getActiveProjectsCount($karyawan) >= 4;
    }

    /**
     * Buat ringkasan data workload karyawan untuk ditampilkan ke manager.
     */
    public function getWorkloadSummary(Karyawan $karyawan): array
    {
        $activeProjects = $this->getActiveProjectsCount($karyawan);
        $status = $this->getWorkloadStatus($karyawan);

        return [
            'id'              => $karyawan->id,
            'name'            => $karyawan->name,
            'job_title'       => $karyawan->job_title,
            'skills'          => $karyawan->skills ?? [],
            'active_projects' => $activeProjects,
            'workload_status' => $status,
            'is_overloaded'   => $activeProjects >= 4,
            // Keep keys for compatibility
            'workload_score'  => $activeProjects,
            'max_workload'    => 4,
            'capacity_pct'    => round(($activeProjects / 4) * 100, 1),
            'active_tasks'    => 0,
        ];
    }
}

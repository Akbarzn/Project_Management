<?php

namespace App\Repositories\Contracts;

/**
 * Interface WorkloadRepositoryInterface
 *
 * Contract untuk query data workload karyawan dari database.
 * Memisahkan logika query dari logika bisnis (WorkloadService).
 *
 * Prinsip: Repository hanya bertugas mengambil data mentah,
 * kalkulasi & keputusan bisnis dilakukan di Service layer.
 */
interface WorkloadRepositoryInterface
{
    /**
     * Hitung total jam kerja aktif seorang karyawan.
     *
     * "Aktif" didefinisikan sebagai task dengan status 'pending' atau 'inwork'.
     * Task yang sudah 'complete' tidak dihitung agar workload mencerminkan
     * beban kerja yang sedang berjalan, bukan historis.
     *
     * Query: SUM(task_work_logs.hours)
     *        JOIN tasks WHERE tasks.karyawan_id = $id AND tasks.status != 'complete'
     *
     * @param int $karyawanId  ID karyawan yang dicari
     * @return float  Total jam kerja aktif (bisa 0.0 jika belum ada log)
     */
    public function getActiveWorkHours(int $karyawanId): float;

    /**
     * Hitung jumlah task aktif yang dimiliki karyawan.
     *
     * "Aktif" didefinisikan sebagai task dengan status 'pending' atau 'inwork'.
     *
     * @param int $karyawanId
     * @return int
     */
    public function getActiveTaskCount(int $karyawanId): int;

    /**
     * Hitung jumlah project aktif yang sedang dikerjakan karyawan.
     *
     * "Aktif" didefinisikan sebagai project dengan status 'ongoing'.
     * Digunakan dalam formula: workload += (jumlah_project_aktif × 5)
     * untuk memberikan penalti pada karyawan yang terlibat banyak project.
     *
     * Query: COUNT(*) FROM karyawan_projects
     *        JOIN projects WHERE project.status = 'ongoing'
     *        AND karyawan_projects.karyawan_id = $id
     *
     * @param int $karyawanId  ID karyawan yang dicari
     * @return int  Jumlah project aktif
     */
    public function getActiveProjectCount(int $karyawanId): int;
}

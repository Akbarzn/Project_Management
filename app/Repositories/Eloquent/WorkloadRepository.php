<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\WorkloadRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * WorkloadRepository
 *
 * Bertanggung jawab untuk mengambil data workload karyawan langsung dari database.
 * Menggunakan raw query (DB::table) agar lebih efisien untuk agregasi data besar,
 * dibandingkan memuat seluruh koleksi model ke memori.
 *
 * Kelas ini TIDAK extends BaseRepository karena tidak beroperasi pada satu model tunggal,
 * melainkan melakukan JOIN antar beberapa tabel (tasks, task_work_logs, projects, karyawan_projects).
 */
class WorkloadRepository implements WorkloadRepositoryInterface
{
    /**
     * Hitung total jam kerja aktif karyawan dari tabel task_work_logs.
     *
     * Hanya menghitung jam kerja dari task yang masih aktif (pending/inwork).
     * Task yang 'complete' tidak dihitung karena sudah selesai dan tidak
     * merepresentasikan beban kerja saat ini.
     *
     * SQL yang dieksekusi:
     * SELECT COALESCE(SUM(twl.hours), 0)
     * FROM task_work_logs twl
     * INNER JOIN tasks t ON twl.task_id = t.id
     * WHERE t.karyawan_id = ?
     *   AND t.status IN ('pending', 'inwork')
     *
     * @param int $karyawanId
     * @return float
     */
    public function getActiveWorkHours(int $karyawanId): float
    {
        return (float) DB::table('task_work_logs as twl')
            ->join('tasks as t', 'twl.task_id', '=', 't.id')
            ->where('t.karyawan_id', $karyawanId)
            ->whereIn('t.status', ['pending', 'inwork'])
            ->sum('twl.hours');
        // COALESCE ditangani oleh Laravel: sum() mengembalikan 0 jika tidak ada baris
    }

    /**
     * Hitung jumlah task aktif yang dimiliki karyawan.
     */
    public function getActiveTaskCount(int $karyawanId): int
    {
        return (int) DB::table('tasks')
            ->where('karyawan_id', $karyawanId)
            ->whereIn('status', ['pending', 'inwork'])
            ->count();
    }

    /**
     * Hitung jumlah project aktif yang sedang dikerjakan karyawan.
     *
     * "Aktif" = project dengan status 'ongoing'.
     * Digunakan dalam formula workload: jumlah_project × 5 jam
     * sebagai penalti koordinasi untuk karyawan yang multi-project.
     *
     * SQL yang dieksekusi:
     * SELECT COUNT(*)
     * FROM karyawan_projects kp
     * INNER JOIN projects p ON kp.project_id = p.id
     * WHERE kp.karyawan_id = ?
     *   AND p.status = 'ongoing'
     *
     * @param int $karyawanId
     * @return int
     */
    public function getActiveProjectCount(int $karyawanId): int
    {
        return (int) \App\Models\Task::where('karyawan_id', $karyawanId)
            ->where('status', '!=', 'complete')
            ->whereHas('project', function ($query) {
                $query->where('status', 'ongoing');
            })
            ->distinct('project_id')
            ->count('project_id');
    }
}

<?php

namespace App\Repositories\Contracts;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Collection;

/**
 * AutoAssignmentRepositoryInterface
 *
 * Contract untuk query kandidat karyawan berdasarkan:
 * - Role dalam tim project (5 role tetap)
 * - Level / seniority yang sesuai difficulty
 * - Workload aktif untuk keperluan Least Load algorithm
 */
interface AutoAssignmentRepositoryInterface
{
    /**
     * Ambil karyawan yang job_title-nya sesuai role yang dibutuhkan.
     * Eager-load relasi yang dibutuhkan untuk kalkulasi workload agar
     * tidak terjadi N+1 query.
     *
     * @param string      $role    Salah satu dari 5 role tetap project
     * @param string|null $level   Level minimum yang dicari (nullable → semua level)
     * @return Collection<int, Karyawan>
     */
    public function getRoleCandidates(string $role, ?string $level = null): Collection;

    /**
     * Hitung current workload seorang karyawan.
     * Formula: total_jam_kerja_aktif + (jumlah_project_aktif × 5)
     *
     * @param int $karyawanId
     * @return float  Workload dalam satuan jam
     */
    public function calculateWorkload(int $karyawanId): float;

    /**
     * Pilih kandidat terbaik (Least Load) dari sekumpulan kandidat
     * yang sudah difilter berdasarkan role dan level.
     *
     * Mengembalikan karyawan dengan projected workload terkecil,
     * atau null jika tidak ada kandidat sama sekali.
     *
     * @param Collection<int, Karyawan> $candidates
     * @param int                       $taskWeight    TaskWeight = priority × difficulty × duration
     * @return array{karyawan: Karyawan, current_workload: float, projected_workload: float}|null
     */
    public function getBestCandidate(Collection $candidates, int $taskWeight): ?array;
}

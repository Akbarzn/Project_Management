<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface KaryawanRepositoryInterface
 *
 * Contract untuk semua operasi data karyawan.
 * Setiap implementasi (Eloquent, dll) wajib memenuhi contract ini.
 */
interface KaryawanRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Ambil semua karyawan dengan filter search dan status task.
     *
     * @param string|null $search  Kata kunci pencarian (nama, nik, job_title, dll)
     * @param string|null $filter  Filter status task ('with-task' | 'no-task')
     */
    public function getAllKaryawan(?string $search = null, ?string $filter = null);

    /**
     * Ambil karyawan yang memiliki skill tertentu, beserta relasi
     * yang dibutuhkan untuk menghitung workload (tasks, workLogs, projects).
     *
     * Jika $skill null atau kosong, kembalikan semua karyawan aktif.
     *
     * @param string|null $skill  Skill yang dicari, contoh: "Laravel"
     * @return Collection<int, \App\Models\Karyawan>
     */
    public function getAvailableBySkill(?string $skill): Collection;

    /**
     * Ambil semua karyawan yang memiliki job_title sesuai role yang diminta.
     * Mendukung pemetaan role alternatif untuk fleksibilitas (misalnya 'Business Analyst').
     *
     * @param string $role
     * @return Collection
     */
    public function getCandidatesByRole(string $role): Collection;

    /**
     * Saring koleksi kandidat berdasarkan skill.
     *
     * @param Collection $candidates
     * @param string|null $skill
     * @return Collection
     */
    public function filterBySkill(Collection $candidates, ?string $skill): Collection;

    /**
     * Saring koleksi kandidat berdasarkan sekumpulan level (Junior, Intermediate, Senior, Lead).
     *
     * @param Collection $candidates
     * @param array $levels
     * @return Collection
     */
    public function filterByLevel(Collection $candidates, array $levels): Collection;

    /**
     * Dapatkan satu kandidat dengan total workload terendah (Least Load).
     *
     * @param Collection $candidates
     * @return \App\Models\Karyawan|null
     */
    public function getLeastLoadCandidate(Collection $candidates);
}
<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\Project;
use App\Models\ProjectRequest;
use App\Repositories\Contracts\AutoAssignmentRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * AutoAssignmentService
 *
 * Service yang bertanggung jawab atas seluruh logika
 * Full Automatic Assignment menggunakan algoritma Least Load.
 *
 * ─────────────────────────────────────────────────
 * ROLE TIM (5 role tetap):
 * ─────────────────────────────────────────────────
 *  - Business Analyst
 *  - Programmer
 *  - Database Functional
 *  - Quality Test
 *  - SysAdmin
 *
 * ─────────────────────────────────────────────────
 * DIFFICULTY → LEVEL MAPPING:
 * ─────────────────────────────────────────────────
 *  Difficulty 1 → Junior
 *  Difficulty 2 → Junior
 *  Difficulty 3 → Intermediate
 *  Difficulty 4 → Senior
 *  Difficulty 5 → Lead (fallback: Senior)
 *
 * ─────────────────────────────────────────────────
 * WORKLOAD FORMULA:
 * ─────────────────────────────────────────────────
 *  TaskWeight          = priority × difficulty × estimated_duration
 *  ProjectedWorkload   = current_workload + TaskWeight
 *
 * ─────────────────────────────────────────────────
 * FALLBACK:
 * ─────────────────────────────────────────────────
 *  Jika tidak ada kandidat dengan level sesuai,
 *  sistem mencoba level di bawahnya secara berurutan
 *  dan mencatat log fallback ke karyawan_projects.
 */
class AutoAssignmentService
{
    /**
     * 5 Role tetap yang harus diisi dalam setiap tim project.
     */
    public const PROJECT_ROLES = [
        'Business Analyst',
        'Programmer',
        'Database Functional',
        'Quality Test',
        'SysAdmin',
    ];

    /**
     * Mapping difficulty → level yang dibutuhkan.
     */
    public const DIFFICULTY_LEVEL_MAP = [
        1 => ['Junior'],
        2 => ['Junior'],
        3 => ['Intermediate'],
        4 => ['Senior'],
        5 => ['Lead', 'Senior'],
    ];

    /**
     * Urutan fallback level dari tertinggi ke terendah.
     * Jika level asli tidak tersedia, coba level berikutnya
     * dalam array ini (bergerak ke bawah / lebih junior).
     */
    private const FALLBACK_ORDER = ['Lead', 'Senior', 'Intermediate', 'Junior'];

    public function __construct(
        protected AutoAssignmentRepositoryInterface $assignmentRepository
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // PUBLIC API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Entry point utama: Assign tim otomatis untuk satu project.
     *
     * Dipanggil oleh ProjectController saat manager approve project request.
     * Seluruh operasi DB dibungkus dalam satu transaksi.
     *
     * @param Project $project  Project yang baru saja di-approve
     * @return array{
     *   success: bool,
     *   team: array<string, array>,
     *   fallbacks: array<string, string>,
     *   task_weight: int,
     *   message: string
     * }
     * @throws \RuntimeException  Jika tidak ada kandidat sama sekali untuk salah satu role
     */
    public function assignProjectTeam(Project $project): array
    {
        return DB::transaction(function () use ($project) {

            /** @var ProjectRequest|null $projectRequest */
            $projectRequest = $project->projectRequest;

            // Hitung TaskWeight dari data project request
            $taskWeight = $this->calculateTaskWeight($projectRequest);

            // Tentukan level yang dibutuhkan berdasarkan difficulty
            $requiredLevels = $this->mapDifficultyToLevels(
                $projectRequest?->difficulty ?? 1
            );

            $team      = [];   // Role → data karyawan terpilih
            $fallbacks = [];   // Role → catatan fallback (jika ada)

            foreach (self::PROJECT_ROLES as $role) {

                // Cari kandidat + best candidate dengan fallback
                $result = $this->findBestCandidateWithFallback(
                    $role,
                    $requiredLevels,
                    $taskWeight,
                    $project
                );

                if ($result === null) {
                    // Tidak ada karyawan SAMA SEKALI untuk role ini
                    // Batalkan seluruh transaksi
                    throw new \RuntimeException(
                        "Tidak ada karyawan tersedia untuk role \"{$role}\". " .
                        "Pastikan ada karyawan dengan job_title \"{$role}\" di sistem."
                    );
                }

                /** @var Karyawan $selectedKaryawan */
                $selectedKaryawan = $result['karyawan'];
                $currentWorkload  = $result['current_workload'];
                $projectedLoad    = $result['projected_workload'];
                $usedFallback     = $result['fallback_used'];
                $fallbackNote     = $result['fallback_note'];

                // Cek apakah karyawan ini sudah ada dalam project ini
                // (hindari duplicate pada role berbeda yang kebetulan assign orang sama)
                $alreadyAssigned = $project->karyawans()
                    ->where('karyawan_id', $selectedKaryawan->id)
                    ->where('karyawan_projects.role', $role)
                    ->exists();

                if (!$alreadyAssigned) {
                    // Simpan ke pivot karyawan_projects
                    $project->karyawans()->attach($selectedKaryawan->id, [
                        'cost_snapshot'      => $selectedKaryawan->cost,
                        'job_title_snapshot' => $selectedKaryawan->job_title,
                        'role'               => $role,
                        'assigned_by_system' => true,
                        'task_weight'        => 0,
                        'projected_workload' => 0,
                        'fallback_used'      => $usedFallback,
                        'fallback_note'      => $fallbackNote,
                    ]);

                    // Generate task untuk karyawan ini dalam project
                    $project->tasks()->create([
                        'karyawan_id' => $selectedKaryawan->id,
                        'catatan'     => 'Auto-assigned berdasarkan workload balancing',
                        'status'      => 'pending',
                        'progress'    => 0,
                    ]);

                    // Log fallback ke Laravel log agar mudah di-audit
                    if ($usedFallback) {
                        Log::info("[AutoAssignment] Fallback digunakan", [
                            'project_id'    => $project->id,
                            'role'          => $role,
                            'karyawan_id'   => $selectedKaryawan->id,
                            'karyawan_name' => $selectedKaryawan->name,
                            'fallback_note' => $fallbackNote,
                        ]);

                        $fallbacks[$role] = $fallbackNote;
                    }
                }

                // Kumpulkan data tim untuk response
                $team[$role] = [
                    'karyawan_id'        => $selectedKaryawan->id,
                    'name'               => $selectedKaryawan->name,
                    'job_title'          => $selectedKaryawan->job_title,
                    'level'              => $selectedKaryawan->level,
                    'skills'             => $selectedKaryawan->skills ?? [],
                    'skills_text'        => $selectedKaryawan->skills_text,
                    'current_workload'   => round($currentWorkload, 2),
                    'projected_workload' => 0,
                    'task_weight'        => 0,
                    'fallback_used'      => $usedFallback,
                    'fallback_note'      => $fallbackNote,
                ];
            }

            return [
                'success'     => true,
                'team'        => $team,
                'fallbacks'   => $fallbacks,
                'task_weight' => 0,
                'message'     => count($fallbacks) > 0
                    ? 'Tim berhasil dibentuk dengan ' . count($fallbacks) . ' fallback.'
                    : 'Tim berhasil dibentuk secara otomatis.',
            ];
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Hitung TaskWeight dari ProjectRequest.
     * TaskWeight = priority × difficulty × estimated_duration
     *
     * @param ProjectRequest|null $projectRequest
     * @return int
     */
    private function calculateTaskWeight(?ProjectRequest $projectRequest): int
    {
        if (!$projectRequest) {
            return 0;
        }

        $priority          = (int) ($projectRequest->priority          ?? 1);
        $difficulty        = (int) ($projectRequest->difficulty        ?? 1);
        $estimatedDuration = (int) ($projectRequest->estimated_duration ?? 0);

        return $priority * $difficulty * $estimatedDuration;
    }

    /**
     * Map difficulty (1–5) ke level karyawan yang dibutuhkan.
     *
     * @param int $difficulty
     * @return array  Level array: ['Junior'] | ['Intermediate'] | ['Senior'] | ['Lead', 'Senior']
     */
    private function mapDifficultyToLevels(int $difficulty): array
    {
        return self::DIFFICULTY_LEVEL_MAP[$difficulty] ?? ['Junior'];
    }

    /**
     * Cari kandidat terbaik untuk satu role, dengan dukungan fallback level.
     *
     * @param string  $role
     * @param array   $preferredLevels
     * @param int     $taskWeight
     * @param Project $project        Untuk memeriksa siapa sudah di-assign
     * @return array{
     *   karyawan: Karyawan,
     *   current_workload: float,
     *   projected_workload: float,
     *   fallback_used: bool,
     *   fallback_note: string|null
     * }|null
     */
    private function findBestCandidateWithFallback(
        string $role,
        array $preferredLevels,
        int $taskWeight,
        Project $project
    ): ?array {
        // Susun urutan level yang akan dicoba: mulai dari requiredLevel ke bawah
        $levelOrder = self::FALLBACK_ORDER;

        // 1. Coba cari di preferredLevels
        $candidates = collect();
        foreach ($preferredLevels as $lvl) {
            $candidates = $candidates->merge($this->assignmentRepository->getRoleCandidates($role, $lvl));
        }

        if ($candidates->isNotEmpty()) {
            $best = $this->assignmentRepository->getBestCandidate($candidates, $taskWeight);
            if ($best !== null) {
                return [
                    'karyawan'           => $best['karyawan'],
                    'current_workload'   => $best['current_workload'],
                    'projected_workload' => $best['projected_workload'],
                    'fallback_used'      => false,
                    'fallback_note'      => null,
                ];
            }
        }

        // Fallback: Jika tidak ada di level preferred, coba cari level dibawahnya secara berurutan
        // Cari level preferred tertinggi untuk menentukan titik awal fallback
        $highestPreferred = $preferredLevels[0];
        $startIndex = array_search($highestPreferred, $levelOrder);

        if ($startIndex === false) {
            $startIndex = 0;
        }

        for ($i = $startIndex + 1; $i < count($levelOrder); $i++) {
            $fallbackLevel = $levelOrder[$i];
            if (in_array($fallbackLevel, $preferredLevels)) {
                continue;
            }

            $fallbackCandidates = $this->assignmentRepository->getRoleCandidates($role, $fallbackLevel);

            if ($fallbackCandidates->isNotEmpty()) {
                $best = $this->assignmentRepository->getBestCandidate($fallbackCandidates, $taskWeight);
                if ($best !== null) {
                    $preferredStr = implode(' atau ', $preferredLevels);
                    return [
                        'karyawan'           => $best['karyawan'],
                        'current_workload'   => $best['current_workload'],
                        'projected_workload' => $best['projected_workload'],
                        'fallback_used'      => true,
                        'fallback_note'      => "{$preferredStr} tidak tersedia, dialokasikan ke {$fallbackLevel}.",
                    ];
                }
            }
        }

        // Belum ditemukan — coba tanpa filter level sama sekali (last resort)
        $allCandidates = $this->assignmentRepository->getRoleCandidates($role, null);

        if ($allCandidates->isEmpty()) {
            return null;
        }

        $best = $this->assignmentRepository->getBestCandidate($allCandidates, $taskWeight);

        if ($best === null) {
            return null;
        }

        $actualLevel  = $best['karyawan']->level;
        $preferredStr = implode(' atau ', $preferredLevels);
        $fallbackNote = "{$preferredStr} tidak tersedia, dialokasikan ke {$actualLevel}.";

        return [
            'karyawan'           => $best['karyawan'],
            'current_workload'   => $best['current_workload'],
            'projected_workload' => $best['projected_workload'],
            'fallback_used'      => true,
            'fallback_note'      => $fallbackNote,
        ];
    }
}

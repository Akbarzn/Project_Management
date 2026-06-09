<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\Project;
use App\Models\ProjectRequest;
use App\Repositories\Contracts\KaryawanRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * LeastLoadAssignmentService
 *
 * Mengimplementasikan Algoritma Least Load untuk Automatic Team Assignment.
 *
 * 1 Project terdiri dari 5 role tetap:
 *  - Business Analyst
 *  - Programmer
 *  - Database Functional
 *  - Quality Test
 *  - SysAdmin
 *
 * Tingkat Kesulitan (Difficulty) & Level Matching:
 *  - Difficulty 1 -> Junior
 *  - Difficulty 2 -> Junior
 *  - Difficulty 3 -> Intermediate
 *  - Difficulty 4 -> Senior
 *  - Difficulty 5 -> Senior atau Lead
 *
 * Fallback Mechanism:
 *  Jika level preferred tidak tersedia, sistem mencari level di bawahnya secara berurutan:
 *  Lead -> Senior -> Intermediate -> Junior
 *  Jika level fallback digunakan, log fallback disimpan ke pivot karyawan_projects.
 */
class LeastLoadAssignmentService
{
    // 5 Role tetap
    public const ROLES = [
        'Business Analyst',
        'Programmer',
        'Database Functional',
        'Quality Test',
        'SysAdmin',
    ];

    // Fallback level order
    private const FALLBACK_ORDER = ['Lead', 'Senior', 'Intermediate', 'Junior'];

    public function __construct(
        protected KaryawanRepositoryInterface $karyawanRepository,
        protected WorkloadService $workloadService
    ) {}

    /**
     * Membentuk tim secara otomatis menggunakan Algoritma Least Load.
     *
     * @param Project $project
     * @return array
     * @throws \RuntimeException
     */
    public function assignTeam(Project $project): array
    {
        return DB::transaction(function () use ($project) {
            /** @var ProjectRequest|null $projectRequest */
            $projectRequest = $project->projectRequest;

            $priority = (int) ($projectRequest?->priority ?? $project->difficulty ?? 1);
            $difficulty = (int) ($projectRequest?->difficulty ?? $project->difficulty ?? 1);

            // Mapping difficulty ke level preferred
            $preferredLevels = $this->mapDifficultyToLevels($difficulty);

            $team = [];
            $fallbacks = [];

            foreach (self::ROLES as $role) {
                $result = $this->findBestCandidateWithFallback($role, $preferredLevels, $project);

                if ($result === null) {
                    throw new \RuntimeException(
                        "Tidak ada karyawan tersedia atau semua karyawan overloaded untuk role \"{$role}\"."
                    );
                }

                /** @var Karyawan $selectedKaryawan */
                $selectedKaryawan = $result['karyawan'];
                $currentWorkload = $result['current_workload'];
                $usedFallback = $result['fallback_used'];
                $fallbackNote = $result['fallback_note'];

                // Attach karyawan ke pivot table
                $project->karyawans()->attach($selectedKaryawan->id, [
                    'cost_snapshot'      => $selectedKaryawan->cost,
                    'job_title_snapshot' => $selectedKaryawan->job_title,
                    'role'               => $role,
                    'assigned_by_system' => true,
                    'task_weight'        => 0, // Dihapus karena tidak sesuai konsep Least Load
                    'projected_workload' => 0, // Dihapus karena tidak sesuai konsep Least Load
                    'fallback_used'      => $usedFallback,
                    'fallback_note'      => $fallbackNote,
                ]);

                // Buat task otomatis untuk role bersangkutan
                $project->tasks()->create([
                    'karyawan_id' => $selectedKaryawan->id,
                    'catatan'     => "Auto-assigned [{$role}] berdasarkan algoritma Least Load",
                    'status'      => 'pending',
                    'progress'    => 0,
                ]);

                if ($usedFallback) {
                    $fallbacks[$role] = $fallbackNote;
                    Log::info("[LeastLoadAssignment] Fallback digunakan pada Project ID {$project->id}: {$fallbackNote}");
                }

                $team[$role] = [
                    'karyawan_id'        => $selectedKaryawan->id,
                    'name'               => $selectedKaryawan->name,
                    'level'              => $selectedKaryawan->level,
                    'skills'             => $selectedKaryawan->skills ?? [],
                    'current_workload'   => $currentWorkload,
                    'projected_workload' => 0,
                    'fallback_used'      => $usedFallback,
                    'fallback_note'      => $fallbackNote,
                ];
            }

            return [
                'success'   => true,
                'team'      => $team,
                'fallbacks' => $fallbacks,
                'message'   => count($fallbacks) > 0
                    ? 'Tim berhasil terbentuk secara otomatis dengan beberapa fallback.'
                    : 'Tim berhasil terbentuk secara otomatis dengan alokasi optimal.',
            ];
        });
    }

    /**
     * Map difficulty (1-5) ke level array.
     */
    private function mapDifficultyToLevels(int $difficulty): array
    {
        return match ($difficulty) {
            1, 2    => ['Junior'],
            3       => ['Intermediate'],
            4       => ['Senior'],
            5       => ['Lead', 'Senior'],
            default => ['Junior'],
        };
    }

    /**
     * Cari kandidat terbaik dengan fallback.
     */
    private function findBestCandidateWithFallback(string $role, array $preferredLevels, Project $project): ?array
    {
        // 1. Cari kandidat sesuai role (Role Matching)
        $candidates = $this->karyawanRepository->getCandidatesByRole($role);

        if ($candidates->isEmpty()) {
            return null;
        }

        // 2. Filter skill yang sesuai (Skill Matching)
        if (!empty($project->required_skill)) {
            $candidates = $this->karyawanRepository->filterBySkill($candidates, $project->required_skill);
        }

        // 3. Level Matching & FILTER OVERLOAD & Fallback
        $difficulty = (int) ($project->projectRequest?->difficulty ?? $project->difficulty ?? 1);

        $levelChain = [];
        if ($difficulty === 5) {
            $levelChain = [
                ['Lead', 'Senior'],
                ['Intermediate'],
                ['Junior']
            ];
        } elseif ($difficulty === 4) {
            $levelChain = [
                ['Senior'],
                ['Intermediate'],
                ['Junior']
            ];
        } elseif ($difficulty === 3) {
            $levelChain = [
                ['Intermediate'],
                ['Junior']
            ];
        } else { // 1, 2 or default
            $levelChain = [
                ['Junior']
            ];
        }

        $selectedCandidate = null;
        $usedFallback = false;
        $fallbackNote = null;

        foreach ($levelChain as $index => $levels) {
            // Filter candidates at these levels
            $candidatesAtLevel = $candidates->filter(function ($c) use ($levels) {
                return in_array($c->level, $levels);
            });

            if ($candidatesAtLevel->isEmpty()) {
                continue;
            }

            // FILTER OVERLOAD: Reject overloaded candidates
            $eligibleCandidates = $candidatesAtLevel->reject(function ($c) {
                return $this->workloadService->isOverloaded($c);
            });

            if ($eligibleCandidates->isNotEmpty()) {
                // Pick the one with the least workload (Least Load)
                $best = $eligibleCandidates->sortBy(function ($c) {
                    return $this->workloadService->calculateWorkload($c);
                })->first();

                $selectedCandidate = $best;

                if ($index > 0) {
                    $usedFallback = true;
                    $preferredStr = implode(' atau ', $levelChain[0]);
                    $fallbackLevelStr = implode(' atau ', $levels);
                    $fallbackNote = "{$preferredStr} tidak tersedia atau overload, dialokasikan ke {$fallbackLevelStr}.";
                }
                break;
            }
        }

        if ($selectedCandidate !== null) {
            return [
                'karyawan'         => $selectedCandidate,
                'current_workload' => $this->workloadService->calculateWorkload($selectedCandidate),
                'fallback_used'    => $usedFallback,
                'fallback_note'    => $fallbackNote,
            ];
        }

        return null;
    }
}

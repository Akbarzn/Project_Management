<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectAssignmentService;
use App\Services\WorkloadService;
use App\Repositories\Contracts\KaryawanRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * ProjectAssignmentController
 *
 * Controller untuk fitur Auto Assignment karyawan ke project.
 *
 * Endpoint yang tersedia:
 * 1. GET  /manager/projects/{project}/assignment/suggest  → suggest()
 *    Tampilkan daftar karyawan yang direkomendasikan + workload score (JSON)
 *
 * 2. POST /manager/projects/{project}/assignment/auto     → autoAssign()
 *    Sistem otomatis pilih karyawan terbaik dan assign ke project
 *
 * 3. POST /manager/projects/{project}/assignment/manual   → manualAssign()
 *    Manager pilih sendiri karyawan, sistem validasi overload
 *
 * Prinsip controller: thin controller.
 * Seluruh logika bisnis ada di Service layer, controller hanya:
 * - Menerima request
 * - Meneruskan ke service
 * - Mengembalikan response
 */
class ProjectAssignmentController extends Controller
{
    public function __construct(
        protected ProjectAssignmentService   $assignmentService,
        protected WorkloadService            $workloadService,
        protected KaryawanRepositoryInterface $karyawanRepository
    ) {}

    /**
     * GET /manager/projects/{project}/assignment/suggest
     *
     * Tampilkan rekomendasi karyawan untuk project berdasarkan:
     * - Skill match dengan required_skill project
     * - Workload score (terkecil = paling direkomendasikan)
     * - Filter karyawan yang overloaded
     *
     * Response JSON:
     * {
     *   "project": { "id": 1, "required_skill": "Laravel", ... },
     *   "candidates": [
     *     {
     *       "id": 5, "name": "Budi", "workload_score": 15,
     *       "max_workload": 40, "capacity_pct": 37.5,
     *       "skill_match": true, "is_overloaded": false, ...
     *     }
     *   ],
     *   "total_candidates": 3,
     *   "required_skill": "Laravel"
     * }
     *
     * @param Project $project  Route model binding
     * @return JsonResponse
     */
    public function suggest(Project $project): JsonResponse
    {
        $candidates = $this->assignmentService->getCandidatesWithWorkload($project);

        return response()->json([
            'project' => [
                'id'             => $project->id,
                'required_skill' => $project->required_skill,
                'difficulty'     => $project->difficulty,
                'estimated_hours'=> $project->estimated_hours,
                'status'         => $project->status,
            ],
            'candidates'        => $candidates,
            'total_candidates'  => count($candidates),
            'required_skill'    => $project->required_skill,
        ]);
    }

    /**
     * POST /manager/projects/{project}/assignment/auto
     *
     * Lakukan auto-assignment: sistem memilih karyawan terbaik secara otomatis.
     * Algoritma memilih karyawan dengan:
     * 1. Skill sesuai required_skill (fallback: semua karyawan)
     * 2. Tidak overloaded
     * 3. Workload terkecil
     *
     * Request body (opsional):
     * {
     *   "limit": 1    // Jumlah karyawan yang akan diassign, default 1
     * }
     *
     * Response success (JSON):
     * {
     *   "success": true,
     *   "message": "...",
     *   "assigned": [...],
     *   "fallback_used": false
     * }
     *
     * Response error (JSON, status 422):
     * {
     *   "success": false,
     *   "message": "Tidak ada karyawan yang tersedia..."
     * }
     *
     * @param Request $request
     * @param Project $project  Route model binding
     * @return JsonResponse
     */
    public function autoAssign(Request $request, Project $project): JsonResponse
    {
        // Validasi parameter limit (opsional, default 1)
        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:10',
        ]);

        $limit = $validated['limit'] ?? 1;

        try {
            $result = $this->assignmentService->autoAssign($project, $limit);

            return response()->json([
                'success'      => true,
                'message'      => $result['message'],
                'assigned'     => $result['assigned'],
                'fallback_used'=> $result['fallback_used'],
            ]);

        } catch (\RuntimeException $e) {
            // RuntimeException dilempar jika tidak ada karyawan tersedia
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Throwable $e) {
            // Tangkap error lain (DB error, dll) dan kembalikan pesan generik
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /manager/projects/{project}/assignment/manual
     *
     * Lakukan manual assignment: manager memilih karyawan secara eksplisit.
     * Sistem tetap memvalidasi workload kecuali force_assign = true.
     *
     * Request body:
     * {
     *   "karyawan_ids": [1, 3, 5],    // Wajib: array ID karyawan
     *   "force_assign": false          // Opsional: paksa assign meski overloaded
     * }
     *
     * Response (JSON):
     * {
     *   "success": true,
     *   "message": "2 karyawan berhasil diassign. 1 karyawan ditolak.",
     *   "assigned": [...],
     *   "rejected": [
     *     { "id": 3, "name": "Ani", "reason": "Karyawan sudah overloaded.",
     *       "workload_score": 45, "max_workload": 40 }
     *   ]
     * }
     *
     * @param Request $request
     * @param Project $project  Route model binding
     * @return JsonResponse
     */
    public function manualAssign(Request $request, Project $project): JsonResponse
    {
        // Validasi input wajib
        $validated = $request->validate([
            'karyawan_ids'   => 'required|array|min:1',
            'karyawan_ids.*' => 'integer|exists:karyawans,id',
            'force_assign'   => 'nullable|boolean',
        ]);

        $forceAssign = $validated['force_assign'] ?? false;

        try {
            $result = $this->assignmentService->manualAssign(
                $project,
                $validated['karyawan_ids'],
                $forceAssign
            );

            $success = count($result['assigned']) > 0;

            return response()->json([
                'success'  => $success,
                'message'  => $result['message'],
                'assigned' => $result['assigned'],
                'rejected' => $result['rejected'],
            ], $success ? 200 : 422);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /manager/projects/{project}/workload
     *
     * Tampilkan workload semua karyawan (untuk dashboard monitoring).
     * Tidak ada filter skill, menampilkan seluruh karyawan beserta score-nya.
     * Berguna untuk melihat distribusi beban kerja tim secara keseluruhan.
     *
     * @param Project $project
     * @return JsonResponse
     */
    public function workloadOverview(Project $project): JsonResponse
    {
        // Ambil semua karyawan (tanpa filter skill)
        $allKaryawan = $this->karyawanRepository->getAvailableBySkill(null);

        $overview = $allKaryawan->map(
            fn($karyawan) => $this->workloadService->getWorkloadSummary($karyawan)
        )->sortBy('workload_score')->values();

        return response()->json([
            'total_karyawan'   => $allKaryawan->count(),
            'overloaded_count' => $overview->where('is_overloaded', true)->count(),
            'available_count'  => $overview->where('is_overloaded', false)->count(),
            'karyawans'        => $overview,
        ]);
    }
}

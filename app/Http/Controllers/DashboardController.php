<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Task;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectRequest;
use Illuminate\Support\Facades\DB;
use App\Services\DashboardService;

class DashboardController extends Controller
{

    /**
     * Summary of dashboardService
     * simpan dashboardService di property
     * @var 
     */
    protected $dashboardService;

    /**
     * inject dashboardService ke controller
     * @param \App\Services\DashboardService $dashboardService
     */
    public function __construct(DashboardService $dashboardService)
    {
        // simpen service ke property
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        // ambil semua data yg dibutuhkan dashboard dari service
        $data = $this->dashboardService->getDashboardData();
        // dd($data);
        // cek kalo user request project tertentu dan ambil detail project dari service
        if ($request->has('project_id')) {
            $data['projectDetail'] = $this->dashboardService->getProjectDetail($request->project_id);
        }

        return view("manager.dashboard", $data);
    }

    /**
     * Summary of getProjectDetail
     * ambil detail project versi json
     * dipkae untuk ajax
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProjectDetail($id)
    {
        return response()->json(
            $this->dashboardService->getProjectDetail($id)
        );
    }

    /**
     * Summary of showKaryawanProject
     * nampilin project apa saja yg dikerjakan oleh karyawan
     * @param mixed $id
     * @return \Illuminate\Contracts\View\View
     */
    public function showKaryawanProject($id)
    {
        // ambil data karyawan dan project yg dikerjakannya
        $data = $this->dashboardService->getKaryawanProjects($id);

        return view("manager.karyawans.project", $data);
    }

}
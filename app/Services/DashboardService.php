<?php 
namespace App\Services;

use App\Repositories\Contracts\DashboardRepositoryInterface;

class DashboardService{
    protected DashboardRepositoryInterface $repository;

    public function __construct(DashboardRepositoryInterface $repository){
        $this->repository = $repository;
    }

    /**
     * ambil semua data dashboard
     * -entity karyawan,project,task dan client
     * -data karyawan
     * -progres project per role
     * -daftar semua project
     * @return array
     */
    public function getDashboardData(){
        // ambil entity karyawan,project,task,client
        $countEntity = $this->repository->getCounts();

        // ambil data karyawan
        $karyawanProjectInfo = $this->repository->getKaryawanProjectInfo();

        // ambil progress project per role
        $projectData = $this->repository->getProjectData();

        // ambil semua data project 
        $projects = $this->repository->getAllProjects();

        // gabung semua data utk dikiirm ke controller
          return $countEntity
        + $karyawanProjectInfo
        + ['projectData' => $projectData]
        + ['projects' => $projects];

//         return array_merge(
//     $counts,
//     $karyawanProjectInfo,
//     compact('projectData', 'projects')
// );

    }

    /**
     * ambil data project detail 
     */
    public function getProjectDetail($id){
        return $this->repository->getProjectDetail($id);
    }

    /**
     * ambil project berdasarkan id karyawan
     */
    public function getKaryawanProjects($id){
        return $this->repository->getKaryawanProjects($id);
    }
}
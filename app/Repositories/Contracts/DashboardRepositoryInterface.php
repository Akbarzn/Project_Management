<?php 

namespace App\Repositories\Contracts;

interface DashboardRepositoryInterface{
    /**
     * ambil data jumlah total karyawan,client,project dan task
     * jumlah karyawan yg sudah dan belum memiliki task
     * @return array
     */
    public function getCounts();

    /**
     * ambil data karyawan dan jumlah project yg dikerjakan
     * @return  array
     */
    public function getKaryawanProjectInfo();

    /**
     * ambil data project dan progress per role
     */
    public function getProjectData();

    public function getProjectDetail(int $id);

    /**
     * ambil semua data project
     */
    public function getAllProjects();

    /**
     * ambil data project berdasarkan id karyawan
     */
    public function getKaryawanProjects($id);

    /**
     * ambil data
     */
}
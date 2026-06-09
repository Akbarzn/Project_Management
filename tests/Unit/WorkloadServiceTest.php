<?php

namespace Tests\Unit;

use App\Models\Karyawan;
use App\Repositories\Contracts\WorkloadRepositoryInterface;
use App\Services\WorkloadService;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * WorkloadServiceTest (Unit Test)
 *
 * Menguji logika bisnis WorkloadService secara terisolasi.
 * Tidak menggunakan database – semua dependency di-MOCK.
 *
 * Formula yang divalidasi:
 *   Workload = Jumlah Active Projects (Ongoing)
 */
class WorkloadServiceTest extends TestCase
{
    protected WorkloadService $workloadService;
    protected $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = Mockery::mock(WorkloadRepositoryInterface::class);
        $this->workloadService = new WorkloadService($this->repositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // =========================================================================
    // TEST GRUP 1: calculateWorkload()
    // =========================================================================

    /**
     * TEST 1.1 – Formula Normal
     *
     * Input  : active_projects=2
     * Expected: 2
     */
    public function test_formula_workload_normal(): void
    {
        $karyawan = new Karyawan();
        $karyawan->id = 1;

        $this->repositoryMock->shouldReceive('getActiveProjectCount')->with(1)->andReturn(2);

        $result = $this->workloadService->calculateWorkload($karyawan);

        $this->assertEquals(2, $result, 'Workload harus sama dengan jumlah project active (2)');
    }

    /**
     * TEST 1.2 – Karyawan Baru (Workload = 0)
     *
     * Input  : active_projects=0
     * Expected: 0
     */
    public function test_formula_workload_fresh_employee(): void
    {
        $karyawan = new Karyawan();
        $karyawan->id = 2;

        $this->repositoryMock->shouldReceive('getActiveProjectCount')->with(2)->andReturn(0);

        $result = $this->workloadService->calculateWorkload($karyawan);

        $this->assertEquals(0, $result, 'Karyawan baru harus workload = 0');
    }

    /**
     * TEST 1.3 – Hanya ada task aktif, tanpa project aktif
     *
     * Input  : active_tasks=8, active_projects=0
     * Expected: 0 (task tidak dihitung)
     */
    public function test_formula_workload_tasks_only(): void
    {
        $karyawan = new Karyawan();
        $karyawan->id = 3;

        $this->repositoryMock->shouldReceive('getActiveProjectCount')->with(3)->andReturn(0);

        $result = $this->workloadService->calculateWorkload($karyawan);

        $this->assertEquals(0, $result, 'Task tidak boleh masuk perhitungan workload');
    }

    /**
     * TEST 1.4 – Hanya ada project aktif
     *
     * Input  : active_projects=3
     * Expected: 3
     */
    public function test_formula_workload_projects_only(): void
    {
        $karyawan = new Karyawan();
        $karyawan->id = 4;

        $this->repositoryMock->shouldReceive('getActiveProjectCount')->with(4)->andReturn(3);

        $result = $this->workloadService->calculateWorkload($karyawan);

        $this->assertEquals(3, $result, '3 project = workload 3');
    }

    // =========================================================================
    // TEST GRUP 2: isOverloaded()
    // =========================================================================

    /**
     * TEST 2.1 – Tidak Overload (di bawah batas 4)
     *
     * Workload=2 -> false
     */
    public function test_not_overloaded_when_below_threshold(): void
    {
        $karyawan = new Karyawan();
        $karyawan->id = 5;

        $this->repositoryMock->shouldReceive('getActiveProjectCount')->with(5)->andReturn(2);

        $this->assertFalse(
            $this->workloadService->isOverloaded($karyawan),
            'Workload 2 < 4 → TIDAK overloaded'
        );
    }

    /**
     * TEST 2.2 – Overload (mencapai batas 4 atau lebih)
     *
     * Workload=4 -> true
     */
    public function test_overloaded_when_exceeds_threshold(): void
    {
        $karyawan = new Karyawan();
        $karyawan->id = 6;

        $this->repositoryMock->shouldReceive('getActiveProjectCount')->with(6)->andReturn(4);

        $this->assertTrue(
            $this->workloadService->isOverloaded($karyawan),
            'Workload 4 >= 4 → OVERLOADED'
        );
    }

    /**
     * TEST 2.3 – Tepat di bawah batas (3) = Tidak Overload
     *
     * Input  : workload=3 → tidak overload
     */
    public function test_not_overloaded_when_exactly_at_threshold(): void
    {
        $karyawan = new Karyawan();
        $karyawan->id = 7;

        $this->repositoryMock->shouldReceive('getActiveProjectCount')->with(7)->andReturn(3);

        $this->assertFalse(
            $this->workloadService->isOverloaded($karyawan),
            'Workload == 3 tidak boleh OVERLOADED'
        );
    }

    // =========================================================================
    // TEST GRUP 3: getWorkloadSummary()
    // =========================================================================

    /**
     * TEST 3.1 – Struktur dan Nilai Summary Benar
     *
     * Input  : active_projects=2
     * Expected:
     *   workload_score  = 2
     *   workload_status = 'Normal'
     *   is_overloaded   = false
     */
    public function test_summary_returns_correct_values(): void
    {
        $karyawan = new Karyawan([
            'name'         => 'Budi Test',
            'job_title'    => 'Developer',
            'skills'       => ['Laravel', 'PHP'],
        ]);
        $karyawan->id = 8;

        $this->repositoryMock->shouldReceive('getActiveProjectCount')->with(8)->andReturn(2);

        $summary = $this->workloadService->getWorkloadSummary($karyawan);

        // Validasi semua key ada
        foreach (['id','name','job_title','skills','active_projects',
                  'workload_score','workload_status','is_overloaded'] as $key) {
            $this->assertArrayHasKey($key, $summary, "Key '$key' harus ada di summary");
        }

        $this->assertEquals(2,  $summary['workload_score'], 'workload_score = 2');
        $this->assertEquals('Normal',  $summary['workload_status'],   'status = Normal');
        $this->assertFalse($summary['is_overloaded'],           '2 < 4, belum overloaded');
    }

    /**
     * TEST 3.2 – status Overload saat >= 4
     *
     * workload=4 → status = Overload, is_overloaded = true
     */
    public function test_capacity_exceeds_threshold(): void
    {
        $karyawan = new Karyawan();
        $karyawan->id = 9;

        $this->repositoryMock->shouldReceive('getActiveProjectCount')->with(9)->andReturn(4);

        $summary = $this->workloadService->getWorkloadSummary($karyawan);

        $this->assertEquals(4, $summary['workload_score']);
        $this->assertEquals('Overload', $summary['workload_status']);
        $this->assertTrue($summary['is_overloaded']);
    }

    /**
     * TEST 3.3 – status Ringan saat 1
     */
    public function test_summary_status_normal(): void
    {
        $karyawan = new Karyawan();
        $karyawan->id = 10;

        $this->repositoryMock->shouldReceive('getActiveProjectCount')->with(10)->andReturn(1);

        $summary = $this->workloadService->getWorkloadSummary($karyawan);

        $this->assertEquals(1, $summary['workload_score']);
        $this->assertEquals('Ringan', $summary['workload_status']);
        $this->assertFalse($summary['is_overloaded']);
    }
}

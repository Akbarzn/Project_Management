<?php

namespace Tests\Unit;

use App\Models\Karyawan;
use App\Models\Project;
use App\Repositories\Contracts\KaryawanRepositoryInterface;
use App\Services\ProjectAssignmentService;
use App\Services\WorkloadService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * ProjectAssignmentServiceTest (Unit Test)
 *
 * Menguji logika bisnis ProjectAssignmentService secara terisolasi.
 * Menggunakan mock untuk KaryawanRepository dan WorkloadService.
 *
 * Skenario yang diuji:
 * 1. Kandidat difilter berdasarkan skill
 * 2. Fallback ke semua karyawan jika tidak ada yang skill-match
 * 3. Karyawan overload dibuang dari kandidat
 * 4. Kandidat diurutkan berdasarkan workload terkecil
 */
class ProjectAssignmentServiceTest extends TestCase
{
    protected ProjectAssignmentService $service;
    protected $karyawanRepoMock;
    protected $workloadServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->karyawanRepoMock   = Mockery::mock(KaryawanRepositoryInterface::class);
        $this->workloadServiceMock = Mockery::mock(WorkloadService::class);

        $this->service = new ProjectAssignmentService(
            $this->karyawanRepoMock,
            $this->workloadServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // Helper: buat mock karyawan dengan id dan skill tertentu
    private function makeMockKaryawan(int $id, array $skills = []): Karyawan
    {
        $karyawan = Mockery::mock(Karyawan::class)->makePartial();
        $karyawan->id     = $id;
        $karyawan->skills = $skills;
        $karyawan->shouldReceive('hasSkill')->andReturnUsing(
            fn($skill) => in_array($skill, $skills)
        );
        return $karyawan;
    }

    // =========================================================================
    // TEST GRUP 1: getSuggestedCandidates()
    // Algoritma seleksi + filter + sorting kandidat
    // =========================================================================

    /**
     * TEST 1.1 – Karyawan Skill-Match dan Tidak Overload → Masuk Kandidat
     *
     * Skenario: 2 karyawan dengan skill Laravel, keduanya tidak overloaded.
     * Expected: keduanya masuk sebagai kandidat, diurutkan workload terkecil.
     */
    public function test_candidates_with_matching_skill_returned(): void
    {
        $project = Mockery::mock(Project::class)->makePartial();
        $project->required_skill = 'Laravel';

        $k1 = $this->makeMockKaryawan(1, ['Laravel']);
        $k2 = $this->makeMockKaryawan(2, ['Laravel']);

        $this->karyawanRepoMock
            ->shouldReceive('getAvailableBySkill')
            ->with('Laravel')
            ->once()
            ->andReturn(new Collection([$k1, $k2]));

        // Keduanya tidak overload
        $this->workloadServiceMock->shouldReceive('isOverloaded')->with($k1)->andReturn(false);
        $this->workloadServiceMock->shouldReceive('isOverloaded')->with($k2)->andReturn(false);

        // Workload untuk sorting: k1=25, k2=10 → k2 seharusnya di depan
        $this->workloadServiceMock->shouldReceive('calculateWorkload')->with($k1)->andReturn(25.0);
        $this->workloadServiceMock->shouldReceive('calculateWorkload')->with($k2)->andReturn(10.0);

        $result = $this->service->getSuggestedCandidates($project);

        $this->assertCount(2, $result, '2 karyawan skill-match harus masuk kandidat');
        // Karyawan dengan workload terkecil harus di posisi pertama
        $this->assertEquals(2, $result->first()->id, 'k2 (workload=10) harus di posisi pertama');
    }

    /**
     * TEST 1.2 – Karyawan Overload Dibuang dari Kandidat
     *
     * Skenario: 2 karyawan skill-match, tapi 1 overloaded.
     * Expected: Hanya 1 kandidat yang lolos.
     */
    public function test_overloaded_candidates_are_excluded(): void
    {
        $project = Mockery::mock(Project::class)->makePartial();
        $project->required_skill = 'Laravel';

        $k1 = $this->makeMockKaryawan(1, ['Laravel']); // Tidak overload
        $k2 = $this->makeMockKaryawan(2, ['Laravel']); // OVERLOADED

        $this->karyawanRepoMock
            ->shouldReceive('getAvailableBySkill')
            ->with('Laravel')
            ->andReturn(new Collection([$k1, $k2]));

        $this->workloadServiceMock->shouldReceive('isOverloaded')->with($k1)->andReturn(false);
        $this->workloadServiceMock->shouldReceive('isOverloaded')->with($k2)->andReturn(true);

        $this->workloadServiceMock->shouldReceive('calculateWorkload')->with($k1)->andReturn(20.0);

        $result = $this->service->getSuggestedCandidates($project);

        $this->assertCount(1, $result, 'Hanya 1 karyawan yang lolos (k2 dibuang karena overload)');
        $this->assertEquals(1, $result->first()->id);
    }

    /**
     * TEST 1.3 – Fallback: Tidak Ada Skill-Match → Pakai Semua Karyawan
     *
     * Skenario: Project butuh skill 'Python', tapi tidak ada yang punya.
     * Expected: Sistem fallback ke semua karyawan (getAvailableBySkill(null)).
     *
     * Ini memvalidasi mekanisme fallback yang memastikan project tetap bisa
     * diassign meski tidak ada karyawan dengan skill persis.
     */
    public function test_fallback_to_all_employees_when_no_skill_match(): void
    {
        $project = Mockery::mock(Project::class)->makePartial();
        $project->required_skill = 'Python';

        $k1 = $this->makeMockKaryawan(1, ['Laravel']); // Bukan Python

        // Pertama: cari dengan skill 'Python' → hasilnya kosong
        $this->karyawanRepoMock
            ->shouldReceive('getAvailableBySkill')
            ->with('Python')
            ->once()
            ->andReturn(new Collection([]));

        // Fallback: ambil semua karyawan (skill=null)
        $this->karyawanRepoMock
            ->shouldReceive('getAvailableBySkill')
            ->with(null)
            ->once()
            ->andReturn(new Collection([$k1]));

        $this->workloadServiceMock->shouldReceive('isOverloaded')->with($k1)->andReturn(false);
        $this->workloadServiceMock->shouldReceive('calculateWorkload')->with($k1)->andReturn(15.0);

        $result = $this->service->getSuggestedCandidates($project);

        // Meski skill tidak cocok, fallback memastikan ada kandidat
        $this->assertCount(1, $result, 'Fallback harus mengembalikan karyawan meski skill tidak cocok');
    }

    /**
     * TEST 1.4 – Semua Overloaded → Kandidat Kosong
     *
     * Skenario: Semua karyawan overloaded.
     * Expected: Collection kosong (tidak ada kandidat).
     */
    public function test_empty_candidates_when_all_overloaded(): void
    {
        $project = Mockery::mock(Project::class)->makePartial();
        $project->required_skill = null;

        $k1 = $this->makeMockKaryawan(1, []);
        $k2 = $this->makeMockKaryawan(2, []);

        $this->karyawanRepoMock
            ->shouldReceive('getAvailableBySkill')
            ->with(null)
            ->once()
            ->andReturn(new Collection([$k1, $k2]));

        // Semua overloaded
        $this->workloadServiceMock->shouldReceive('isOverloaded')->with($k1)->andReturn(true);
        $this->workloadServiceMock->shouldReceive('isOverloaded')->with($k2)->andReturn(true);

        $result = $this->service->getSuggestedCandidates($project);

        $this->assertCount(0, $result, 'Jika semua overloaded, kandidat harus kosong');
    }

    /**
     * TEST 1.5 – Sorting: Karyawan Workload Terkecil di Posisi Pertama
     *
     * Skenario: 3 karyawan dengan workload berbeda.
     * Expected: Diurutkan ascending (terkecil di depan).
     *
     * Ini inti dari Workload Balancing Algorithm:
     * karyawan dengan beban paling ringan mendapat prioritas.
     */
    public function test_candidates_sorted_by_workload_ascending(): void
    {
        $project = Mockery::mock(Project::class)->makePartial();
        $project->required_skill = null;

        $k1 = $this->makeMockKaryawan(1, []); // workload = 30
        $k2 = $this->makeMockKaryawan(2, []); // workload = 10 ← terkecil
        $k3 = $this->makeMockKaryawan(3, []); // workload = 20

        $this->karyawanRepoMock
            ->shouldReceive('getAvailableBySkill')
            ->with(null)
            ->andReturn(new Collection([$k1, $k2, $k3]));

        $this->workloadServiceMock->shouldReceive('isOverloaded')->andReturn(false);

        $this->workloadServiceMock->shouldReceive('calculateWorkload')->with($k1)->andReturn(30.0);
        $this->workloadServiceMock->shouldReceive('calculateWorkload')->with($k2)->andReturn(10.0);
        $this->workloadServiceMock->shouldReceive('calculateWorkload')->with($k3)->andReturn(20.0);

        $result = $this->service->getSuggestedCandidates($project);

        $ids = $result->pluck('id')->toArray();

        // Urutan yang benar: k2 (10) → k3 (20) → k1 (30)
        $this->assertEquals([2, 3, 1], $ids,
            'Kandidat harus diurutkan berdasarkan workload terkecil (workload balancing)'
        );
    }
}

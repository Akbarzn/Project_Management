<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Karyawan\StoreKaryawanRequest;
use App\Http\Requests\Karyawan\UpdateKaryawanRequest;
use App\Services\KaryawanService;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    /**
     * Summary of karyawanService
     * simpan karyawnservice ke property
     * @var KaryawanService
     */
    protected KaryawanService $karyawanService;

    /**
     * Summary of __construct
     * inject karyawanservice ke controller dgn contructor
     * @param KaryawanService $karyawanService
     */
    public function __construct(KaryawanService $karyawanService)
    {
        $this->karyawanService = $karyawanService;
    }

    /**
     * Summary of index
     * nampilin daftar karyawan
     * ambil search dan filter dari request
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filter = $request->query('filter');

        // ambil data karyawan dari service
        $karyawans = $this->karyawanService->listKaryawan($search,$filter);

        return view('manager.karyawans.index', compact('karyawans', 'search', 'filter'));
    }

    public function create()
    {
        return view('manager.karyawans.create');
    }

    public function store(StoreKaryawanRequest $request)
    {
        $this->karyawanService->createKaryawan($request->validated());
        return redirect()->route('manager.karyawans.index')->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function edit(Karyawan $karyawan)
    {
        return view('manager.karyawans.edit', compact('karyawan'));
    }

    public function update(UpdateKaryawanRequest $request, Karyawan $karyawan)
    {
        $this->karyawanService->updateKaryawan($karyawan, $request->validated());
        return redirect()->route('manager.karyawans.index')->with('success', 'Data karyawan berhasil diperbarui');
    }

    public function destroy(Karyawan $karyawan)
    {
        $this->karyawanService->deleteKaryawan($karyawan);
        return redirect()->route('manager.karyawans.index')->with('success', 'Karyawan berhasil dihapus');
    }
}

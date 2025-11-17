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
    protected KaryawanService $karyawanService;

    public function __construct(KaryawanService $karyawanService)
    {
        $this->karyawanService = $karyawanService;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $karyawans = $this->karyawanService->listKaryawan($search);
        return view('manager.karyawans.index', compact('karyawans','search'));
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

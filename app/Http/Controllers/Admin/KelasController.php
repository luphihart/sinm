<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\KelasRequest;
use App\Repositories\Contracts\KelasRepositoryInterface;
use App\Repositories\Contracts\JurusanRepositoryInterface;

class KelasController extends Controller
{
    protected $kelasRepo;
    protected $jurusanRepo;

    public function __construct(KelasRepositoryInterface $kelasRepo, JurusanRepositoryInterface $jurusanRepo)
    {
        $this->kelasRepo = $kelasRepo;
        $this->jurusanRepo = $jurusanRepo;
    }

    public function index()
    {
        $kelas = $this->kelasRepo->getWithJurusan();
        $jurusans = $this->jurusanRepo->all();
        return view('admin.kelas.index', compact('kelas', 'jurusans'));
    }

    public function store(KelasRequest $request)
    {
        $this->kelasRepo->create($request->validated());
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(KelasRequest $request, $id)
    {
        $this->kelasRepo->update($id, $request->validated());
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $this->kelasRepo->delete($id);
            return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.kelas.index')->with('error', 'Kelas gagal dihapus karena memiliki keterkaitan data.');
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\JurusanRequest;
use App\Repositories\Contracts\JurusanRepositoryInterface;

class JurusanController extends Controller
{
    protected $jurusanRepo;

    public function __construct(JurusanRepositoryInterface $jurusanRepo)
    {
        $this->jurusanRepo = $jurusanRepo;
    }

    public function index()
    {
        $jurusans = $this->jurusanRepo->all();
        return view('admin.jurusan.index', compact('jurusans'));
    }

    public function store(JurusanRequest $request)
    {
        $this->jurusanRepo->create($request->validated());
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function update(JurusanRequest $request, $id)
    {
        $this->jurusanRepo->update($id, $request->validated());
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $this->jurusanRepo->delete($id);
            return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.jurusan.index')->with('error', 'Jurusan gagal dihapus karena memiliki keterkaitan data.');
        }
    }
}

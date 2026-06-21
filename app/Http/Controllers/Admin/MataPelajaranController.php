<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\MataPelajaranRepositoryInterface;

class MataPelajaranController extends Controller
{
    protected $mapelRepo;

    public function __construct(MataPelajaranRepositoryInterface $mapelRepo)
    {
        $this->mapelRepo = $mapelRepo;
    }

    public function index()
    {
        $mapels = $this->mapelRepo->all();
        return view('admin.mapel.index', compact('mapels'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_mapel' => 'required|string|unique:mata_pelajaran,kode_mapel',
            'nama_mapel' => 'required|string|max:255',
            'urutan' => 'required|integer|min:0',
        ]);

        $this->mapelRepo->create($data);
        return redirect()->route('admin.mapel.index')->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'kode_mapel' => 'required|string|unique:mata_pelajaran,kode_mapel,' . $id,
            'nama_mapel' => 'required|string|max:255',
            'urutan' => 'required|integer|min:0',
        ]);

        $this->mapelRepo->update($id, $data);
        return redirect()->route('admin.mapel.index')->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $this->mapelRepo->delete($id);
            return redirect()->route('admin.mapel.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.mapel.index')->with('error', 'Mata Pelajaran gagal dihapus karena keterkaitan data.');
        }
    }
}

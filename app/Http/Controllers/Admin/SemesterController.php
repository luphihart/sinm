<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\SemesterRepositoryInterface;

class SemesterController extends Controller
{
    protected $semesterRepo;

    public function __construct(SemesterRepositoryInterface $semesterRepo)
    {
        $this->semesterRepo = $semesterRepo;
    }

    public function index()
    {
        $semesters = $this->semesterRepo->all();
        return view('admin.semester.index', compact('semesters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'semester_ke' => 'required|integer|min:1|max:6',
            'tahun_ajaran' => 'required|string|max:50',
        ]);

        $this->semesterRepo->create($data);
        return redirect()->route('admin.semester.index')->with('success', 'Semester berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'semester_ke' => 'required|integer|min:1|max:6',
            'tahun_ajaran' => 'required|string|max:50',
        ]);

        $this->semesterRepo->update($id, $data);
        return redirect()->route('admin.semester.index')->with('success', 'Semester berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $this->semesterRepo->delete($id);
            return redirect()->route('admin.semester.index')->with('success', 'Semester berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.semester.index')->with('error', 'Semester gagal dihapus karena keterkaitan data.');
        }
    }
}

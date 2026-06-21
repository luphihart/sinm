<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NilaiRequest;
use App\Models\Nilai;
use App\Models\Murid;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Jurusan;
use App\Models\MataPelajaran;
use App\Repositories\Contracts\NilaiRepositoryInterface;
use App\Repositories\Contracts\MuridRepositoryInterface;
use App\Repositories\Contracts\KelasRepositoryInterface;
use App\Repositories\Contracts\SemesterRepositoryInterface;
use App\Repositories\Contracts\MataPelajaranRepositoryInterface;
use App\Repositories\Contracts\JurusanRepositoryInterface;
use App\Services\RankingEngine;
use App\Services\ImportNilaiService;
use App\Services\ReportExportService;
use App\Exports\RankingKelasExport;
use App\Exports\RankingJurusanExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class NilaiController extends Controller
{
    protected $nilaiRepo;
    protected $muridRepo;
    protected $kelasRepo;
    protected $semesterRepo;
    protected $mapelRepo;
    protected $jurusanRepo;
    protected $rankingEngine;
    protected $importService;
    protected $exportService;

    public function __construct(
        NilaiRepositoryInterface $nilaiRepo,
        MuridRepositoryInterface $muridRepo,
        KelasRepositoryInterface $kelasRepo,
        SemesterRepositoryInterface $semesterRepo,
        MataPelajaranRepositoryInterface $mapelRepo,
        JurusanRepositoryInterface $jurusanRepo,
        RankingEngine $rankingEngine,
        ImportNilaiService $importService,
        ReportExportService $exportService
    ) {
        $this->nilaiRepo = $nilaiRepo;
        $this->muridRepo = $muridRepo;
        $this->kelasRepo = $kelasRepo;
        $this->semesterRepo = $semesterRepo;
        $this->mapelRepo = $mapelRepo;
        $this->jurusanRepo = $jurusanRepo;
        $this->rankingEngine = $rankingEngine;
        $this->importService = $importService;
        $this->exportService = $exportService;
    }

    public function index(Request $request)
    {
        $kelas = $this->kelasRepo->all();
        $semesters = $this->semesterRepo->all();
        $mapels = $this->mapelRepo->all();
        $jurusans = $this->jurusanRepo->all();

        $selectedKelasId = $request->input('kelas_id');
        $selectedSemesterId = $request->input('semester_id');
        $selectedMuridId = $request->input('murid_id');

        $murids = [];
        $grades = [];
        $rankings = null;

        if ($selectedKelasId) {
            $murids = Murid::where('kelas_id', $selectedKelasId)->get();
        }

        if ($selectedMuridId && $selectedSemesterId) {
            $grades = Nilai::where('murid_id', $selectedMuridId)
                ->where('semester_id', $selectedSemesterId)
                ->with('mataPelajaran')
                ->get();

            $rankings = $this->rankingEngine->getStudentRankings($selectedMuridId, $selectedSemesterId);
        }

        // Untuk ranking leaderboard
        $rankingKelasList = [];
        $rankingJurusanList = [];
        
        $selectedRankKelasId = $request->input('rank_kelas_id');
        $selectedRankSemesterId = $request->input('rank_semester_id') ?? $selectedSemesterId;
        
        if ($selectedRankKelasId && $selectedRankSemesterId) {
            $rankingKelasList = $this->rankingEngine->getClassRankingList($selectedRankKelasId, $selectedRankSemesterId);
        }

        $selectedRankJurusanId = $request->input('rank_jurusan_id');
        $selectedRankTingkat = $request->input('rank_tingkat');
        $selectedRankSemId = $request->input('rank_sem_id') ?? $selectedSemesterId;

        if ($selectedRankJurusanId && $selectedRankTingkat && $selectedRankSemId) {
            $rankingJurusanList = $this->rankingEngine->getParallelRankingList($selectedRankJurusanId, $selectedRankTingkat, $selectedRankSemId);
        }

        return view('admin.nilai.index', compact(
            'kelas',
            'semesters',
            'mapels',
            'jurusans',
            'murids',
            'grades',
            'rankings',
            'rankingKelasList',
            'rankingJurusanList',
            'selectedKelasId',
            'selectedSemesterId',
            'selectedMuridId',
            'selectedRankKelasId',
            'selectedRankSemesterId',
            'selectedRankJurusanId',
            'selectedRankTingkat',
            'selectedRankSemId'
        ));
    }

    public function store(NilaiRequest $request)
    {
        $data = $request->validated();

        // Cek duplikasi
        $duplikat = $this->nilaiRepo->checkDuplicate(
            $data['murid_id'],
            $data['semester_id'],
            $data['mata_pelajaran_id']
        );

        if ($duplikat) {
            return back()->with('error', 'Nilai untuk siswa pada semester dan mata pelajaran ini sudah diinput. Silakan edit nilai yang ada.');
        }

        $this->nilaiRepo->create($data);
        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function update(NilaiRequest $request, $id)
    {
        $data = $request->validated();

        // Cek duplikasi kecuali id sekarang
        $duplikat = $this->nilaiRepo->checkDuplicate(
            $data['murid_id'],
            $data['semester_id'],
            $data['mata_pelajaran_id'],
            $id
        );

        if ($duplikat) {
            return back()->with('error', 'Duplikasi data terdeteksi untuk semester dan mata pelajaran ini.');
        }

        $this->nilaiRepo->update($id, $data);
        return back()->with('success', 'Nilai berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->nilaiRepo->delete($id);
        return back()->with('success', 'Nilai berhasil dihapus.');
    }

    /**
     * Preview Excel Import
     */
    public function importPreview(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file_excel.required' => 'File Excel wajib diunggah.',
            'file_excel.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
        ]);

        $file = $request->file('file_excel');
        $previewResult = $this->importService->preview($file);

        if (!$previewResult['isValid'] && !empty($previewResult['errors'])) {
            return back()->withErrors($previewResult['errors']);
        }

        // Simpan data di session untuk konfirmasi import
        $request->session()->put('import_preview_data', $previewResult['data']);

        return view('admin.nilai.import_preview', [
            'previewData' => $previewResult['data'],
            'isValidFile' => $previewResult['isValid']
        ]);
    }

    /**
     * Confirm Excel Import
     */
    public function importConfirm(Request $request)
    {
        if ($request->has('cancel')) {
            $request->session()->forget('import_preview_data');
            return redirect()->route('admin.nilai.index')->with('info', 'Import dibatalkan.');
        }

        $data = $request->session()->get('import_preview_data');

        if (!$data) {
            return redirect()->route('admin.nilai.index')->with('error', 'Data preview tidak ditemukan. Silakan upload ulang file Excel.');
        }

        try {
            $this->importService->import($data);
            $request->session()->forget('import_preview_data');
            return redirect()->route('admin.nilai.index')->with('success', 'Data nilai berhasil diimport ke database.');
        } catch (\Exception $e) {
            return redirect()->route('admin.nilai.index')->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * Export PDF Rapor Semester
     */
    public function exportPdfRapor($muridId, $semesterId)
    {
        $murid = Murid::findOrFail($muridId);
        $semester = Semester::findOrFail($semesterId);
        return $this->exportService->exportRaporSemester($murid, $semester);
    }

    /**
     * Export PDF Transkrip Lengkap
     */
    public function exportPdfTranskrip($muridId)
    {
        $murid = Murid::findOrFail($muridId);
        return $this->exportService->exportTranskripLengkap($murid);
    }

    /**
     * Export PDF Ranking Kelas
     */
    public function exportPdfRankingKelas(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $semesterId = $request->input('semester_id');

        if (!$kelasId || !$semesterId) {
            return back()->with('error', 'Kelas dan Semester harus dipilih.');
        }

        $kelas = Kelas::findOrFail($kelasId);
        $semester = Semester::findOrFail($semesterId);

        return $this->exportService->exportRankingKelas($kelas, $semester);
    }

    /**
     * Export PDF Ranking Jurusan (Paralel)
     */
    public function exportPdfRankingJurusan(Request $request)
    {
        $jurusanId = $request->input('jurusan_id');
        $tingkat = $request->input('tingkat');
        $semesterId = $request->input('semester_id');

        if (!$jurusanId || !$tingkat || !$semesterId) {
            return back()->with('error', 'Jurusan, Tingkat, dan Semester harus dipilih.');
        }

        $jurusan = Jurusan::findOrFail($jurusanId);
        $semester = Semester::findOrFail($semesterId);

        return $this->exportService->exportRankingJurusan($jurusan, $tingkat, $semester);
    }

    /**
     * Export Excel Ranking Kelas
     */
    public function exportExcelRankingKelas(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $semesterId = $request->input('semester_id');

        if (!$kelasId || !$semesterId) {
            return back()->with('error', 'Kelas dan Semester harus dipilih.');
        }

        $kelas = Kelas::findOrFail($kelasId);
        $semester = Semester::findOrFail($semesterId);
        $rankingList = $this->rankingEngine->getClassRankingList($kelasId, $semesterId);

        return Excel::download(new RankingKelasExport($rankingList), "ranking_kelas_{$kelas->nama_kelas}_sem_{$semester->semester_ke}.xlsx");
    }

    /**
     * Export Excel Ranking Jurusan (Paralel)
     */
    public function exportExcelRankingJurusan(Request $request)
    {
        $jurusanId = $request->input('jurusan_id');
        $tingkat = $request->input('tingkat');
        $semesterId = $request->input('semester_id');

        if (!$jurusanId || !$tingkat || !$semesterId) {
            return back()->with('error', 'Jurusan, Tingkat, dan Semester harus dipilih.');
        }

        $jurusan = Jurusan::findOrFail($jurusanId);
        $semester = Semester::findOrFail($semesterId);
        $rankingList = $this->rankingEngine->getParallelRankingList($jurusanId, $tingkat, $semesterId);

        return Excel::download(new RankingJurusanExport($rankingList), "ranking_paralel_{$jurusan->kode_jurusan}_{$tingkat}_sem_{$semester->semester_ke}.xlsx");
    }

    /**
     * Download Excel grid template for a class and semester.
     */
    public function downloadClassTemplate(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $semesterId = $request->input('semester_id');

        if (!$kelasId || !$semesterId) {
            return back()->with('error', 'Silakan pilih Kelas dan Semester terlebih dahulu untuk mengunduh template.');
        }

        $kelas = Kelas::findOrFail($kelasId);
        $semester = Semester::findOrFail($semesterId);

        return Excel::download(new \App\Exports\ClassGradeTemplateExport($kelasId, $semesterId), "template_nilai_kelas_{$kelas->nama_kelas}_semester_{$semester->semester_ke}.xlsx");
    }

    /**
     * Preview Class Grid Excel Import.
     */
    public function importClassPreview(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file_excel.required' => 'File Excel wajib diunggah.',
            'file_excel.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
        ]);

        $file = $request->file('file_excel');
        $previewResult = $this->importService->previewClassGrid($file);

        if (!$previewResult['isValid'] && !empty($previewResult['errors'])) {
            return back()->withErrors($previewResult['errors']);
        }

        // Store preview data in session
        $request->session()->put('class_import_preview_data', $previewResult['data']);

        return view('admin.nilai.import_class_preview', [
            'previewData' => $previewResult['data'],
            'isValidFile' => $previewResult['isValid']
        ]);
    }

    /**
     * Confirm Class Grid Excel Import.
     */
    public function importClassConfirm(Request $request)
    {
        if ($request->has('cancel')) {
            $request->session()->forget('class_import_preview_data');
            return redirect()->route('admin.nilai.index')->with('info', 'Import dibatalkan.');
        }

        $data = $request->session()->get('class_import_preview_data');

        if (!$data) {
            return redirect()->route('admin.nilai.index')->with('error', 'Data preview tidak ditemukan. Silakan upload ulang file Excel.');
        }

        try {
            $this->importService->importClassGrid($data);
            $request->session()->forget('class_import_preview_data');
            return redirect()->route('admin.nilai.index')->with('success', 'Data nilai satu kelas berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('admin.nilai.index')->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}

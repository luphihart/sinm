<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Services\ReportExportService;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    protected $exportService;

    public function __construct(ReportExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Download current student's Rapor Semester PDF.
     */
    public function exportRaporPdf($semesterId)
    {
        $murid = Auth::user()->murid;

        if (!$murid) {
            abort(404, 'Data profil siswa tidak ditemukan.');
        }

        $semester = Semester::findOrFail($semesterId);

        return $this->exportService->exportRaporSemester($murid, $semester);
    }

    /**
     * Download current student's complete Transkrip PDF.
     */
    public function exportTranskripPdf()
    {
        $murid = Auth::user()->murid;

        if (!$murid) {
            abort(404, 'Data profil siswa tidak ditemukan.');
        }

        return $this->exportService->exportTranskripLengkap($murid);
    }
}

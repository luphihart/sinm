<?php

namespace App\Services;

use App\Models\Murid;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Jurusan;
use App\Services\RankingEngine;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportExportService
{
    protected $rankingEngine;

    public function __construct(RankingEngine $rankingEngine)
    {
        $this->rankingEngine = $rankingEngine;
    }

    /**
     * Download Rapor Semester PDF for a Student.
     */
    public function exportRaporSemester(Murid $murid, Semester $semester)
    {
        $grades = $murid->nilai()
            ->where('semester_id', $semester->id)
            ->with('mataPelajaran')
            ->get()
            ->sortBy(function ($n) {
                return $n->mataPelajaran->urutan ?? 999;
            })
            ->values();

        $rankings = $this->rankingEngine->getStudentRankings($murid->id, $semester->id);

        $pdf = Pdf::loadView('reports.rapor_pdf', [
            'murid' => $murid,
            'semester' => $semester,
            'grades' => $grades,
            'rankings' => $rankings
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("rapor_{$murid->nis}_semester_{$semester->semester_ke}.pdf");
    }

    /**
     * Download Transkrip Nilai Lengkap (Semesters 1-6) PDF for a Student.
     */
    public function exportTranskripLengkap(Murid $murid)
    {
        $semesters = Semester::orderBy('semester_ke', 'asc')->get();
        
        $transkrip = [];
        $totalSKS = 0;
        $totalBobot = 0.0;
        $activeSemestersCount = 0;

        foreach ($semesters as $semester) {
            $grades = $murid->nilai()
                ->where('semester_id', $semester->id)
                ->with('mataPelajaran')
                ->get()
                ->sortBy(function ($n) {
                    return $n->mataPelajaran->urutan ?? 999;
                })
                ->values();

            if ($grades->isNotEmpty()) {
                $rankings = $this->rankingEngine->getStudentRankings($murid->id, $semester->id);
                $transkrip[] = [
                    'semester' => $semester,
                    'grades' => $grades,
                    'rankings' => $rankings
                ];
                $totalSKS += $grades->count();
                $totalBobot += $grades->avg('nilai');
                $activeSemestersCount++;
            }
        }

        $gpa = $activeSemestersCount > 0 ? ($totalBobot / $activeSemestersCount) : 0.0;

        $pdf = Pdf::loadView('reports.transkrip_pdf', [
            'murid' => $murid,
            'transkrip' => $transkrip,
            'totalSKS' => $totalSKS,
            'gpa' => $gpa
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("transkrip_{$murid->nis}.pdf");
    }

    /**
     * Download Ranking Kelas PDF for a Class and Semester.
     */
    public function exportRankingKelas(Kelas $kelas, Semester $semester)
    {
        $rankingList = $this->rankingEngine->getClassRankingList($kelas->id, $semester->id);

        $pdf = Pdf::loadView('reports.ranking_kelas_pdf', [
            'kelas' => $kelas,
            'semester' => $semester,
            'rankingList' => $rankingList
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("ranking_kelas_{$kelas->nama_kelas}_sem_{$semester->semester_ke}.pdf");
    }

    /**
     * Download Parallel Department Ranking PDF for a Jurusan, Tingkat and Semester.
     */
    public function exportRankingJurusan(Jurusan $jurusan, string $tingkat, Semester $semester)
    {
        $rankingList = $this->rankingEngine->getParallelRankingList($jurusan->id, $tingkat, $semester->id);

        $pdf = Pdf::loadView('reports.ranking_jurusan_pdf', [
            'jurusan' => $jurusan,
            'tingkat' => $tingkat,
            'semester' => $semester,
            'rankingList' => $rankingList
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("ranking_paralel_{$jurusan->kode_jurusan}_{$tingkat}_sem_{$semester->semester_ke}.pdf");
    }
}

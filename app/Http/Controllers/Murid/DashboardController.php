<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Services\RankingEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $rankingEngine;

    public function __construct(RankingEngine $rankingEngine)
    {
        $this->rankingEngine = $rankingEngine;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $murid = $user->murid;

        if (!$murid) {
            abort(404, 'Data murid tidak ditemukan untuk akun ini.');
        }

        // Pilihan Semester
        $semesters = Semester::orderBy('semester_ke', 'asc')->get();
        
        // Cari semester terakhir yang terisi nilai untuk murid ini
        $latestGradedSemesterId = DB::table('nilai')
            ->where('murid_id', $murid->id)
            ->orderBy('semester_id', 'desc')
            ->value('semester_id');

        $selectedSemesterId = $request->input('semester_id') ?? $latestGradedSemesterId ?? ($semesters->first()->id ?? null);
        $selectedSemester = Semester::find($selectedSemesterId);

        $grades = [];
        $rankings = [
            'total_nilai' => 0,
            'avg_nilai' => 0,
            'rank_kelas' => '-',
            'total_murid_kelas' => 0,
            'rank_paralel' => '-',
            'total_murid_paralel' => 0
        ];

        if ($selectedSemesterId) {
            $grades = $murid->nilai()
                ->where('semester_id', $selectedSemesterId)
                ->with('mataPelajaran')
                ->get()
                ->sortBy(function ($n) {
                    return $n->mataPelajaran->urutan ?? 999;
                })
                ->values();

            $rankings = $this->rankingEngine->getStudentRankings($murid->id, $selectedSemesterId);
        }

        // Ambil tren nilai semester 1-6 untuk Chart.js
        $chartData = DB::table('nilai')
            ->join('semester', 'nilai.semester_id', '=', 'semester.id')
            ->where('nilai.murid_id', $murid->id)
            ->select('semester.semester_ke', DB::raw('ROUND(AVG(nilai.nilai), 2) as avg_nilai'))
            ->groupBy('semester.id', 'semester.semester_ke')
            ->orderBy('semester.semester_ke', 'asc')
            ->get();

        return view('murid.dashboard', compact(
            'murid',
            'semesters',
            'selectedSemesterId',
            'selectedSemester',
            'grades',
            'rankings',
            'chartData'
        ));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Murid;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Pilihan Semester
        $semesters = Semester::orderBy('semester_ke', 'asc')->get();
        $selectedSemesterId = $request->input('semester_id') ?? ($semesters->last()->id ?? null);

        // 1. Statistik Kartu
        $totalMurid = Murid::count();
        $totalKelas = Kelas::count();
        $totalJurusan = Jurusan::count();
        $totalMapel = MataPelajaran::count();
        $totalNilai = Nilai::count();

        // 2. Top 10 Ranking Sekolah
        $topSekolah = [];
        if ($selectedSemesterId) {
            $topSekolah = DB::select("
                SELECT 
                    sub.murid_id,
                    sub.nama_lengkap,
                    sub.nis,
                    sub.nama_kelas,
                    sub.avg_nilai,
                    DENSE_RANK() OVER (ORDER BY sub.avg_nilai DESC) as ranking
                FROM (
                    SELECT 
                        m.id as murid_id,
                        m.nama_lengkap,
                        m.nis,
                        k.nama_kelas,
                        ROUND(AVG(n.nilai), 2) as avg_nilai
                    FROM murid m
                    JOIN kelas k ON m.kelas_id = k.id
                    JOIN nilai n ON n.murid_id = m.id
                    WHERE n.semester_id = :semester_id
                    GROUP BY m.id, m.nama_lengkap, m.nis, k.nama_kelas
                ) sub
                LIMIT 10
            ", ['semester_id' => $selectedSemesterId]);
        }

        // 3. Pilihan Jurusan untuk Filter Top 10 Jurusan
        $jurusans = Jurusan::all();
        $selectedJurusanId = $request->input('jurusan_id') ?? ($jurusans->first()->id ?? null);
        
        $topJurusan = [];
        if ($selectedSemesterId && $selectedJurusanId) {
            $topJurusan = DB::select("
                SELECT 
                    sub.murid_id,
                    sub.nama_lengkap,
                    sub.nis,
                    sub.nama_kelas,
                    sub.avg_nilai,
                    DENSE_RANK() OVER (ORDER BY sub.avg_nilai DESC) as ranking
                FROM (
                    SELECT 
                        m.id as murid_id,
                        m.nama_lengkap,
                        m.nis,
                        k.nama_kelas,
                        ROUND(AVG(n.nilai), 2) as avg_nilai
                    FROM murid m
                    JOIN kelas k ON m.kelas_id = k.id
                    JOIN nilai n ON n.murid_id = m.id
                    WHERE n.semester_id = :semester_id AND k.jurusan_id = :jurusan_id
                    GROUP BY m.id, m.nama_lengkap, m.nis, k.nama_kelas
                ) sub
                LIMIT 10
            ", [
                'semester_id' => $selectedSemesterId,
                'jurusan_id' => $selectedJurusanId
            ]);
        }

        // 4. Grafik 1: Distribusi Nilai
        $distribution = [
            'kurang_dari_60' => 0,
            'antara_60_74' => 0,
            'antara_75_84' => 0,
            'antara_85_100' => 0,
        ];

        if ($selectedSemesterId) {
            $distData = DB::selectOne("
                SELECT 
                    COUNT(CASE WHEN nilai < 60 THEN 1 END) as range_1,
                    COUNT(CASE WHEN nilai >= 60 AND nilai < 75 THEN 1 END) as range_2,
                    COUNT(CASE WHEN nilai >= 75 AND nilai < 85 THEN 1 END) as range_3,
                    COUNT(CASE WHEN nilai >= 85 THEN 1 END) as range_4
                FROM nilai
                WHERE semester_id = :semester_id
            ", ['semester_id' => $selectedSemesterId]);

            if ($distData) {
                $distribution = [
                    'kurang_dari_60' => $distData->range_1,
                    'antara_60_74' => $distData->range_2,
                    'antara_75_84' => $distData->range_3,
                    'antara_85_100' => $distData->range_4,
                ];
            }
        }

        // 5. Grafik 2: Rata-rata per Kelas
        $classAverages = [];
        if ($selectedSemesterId) {
            $classAverages = DB::select("
                SELECT 
                    k.nama_kelas,
                    ROUND(AVG(n.nilai), 2) as avg_nilai
                FROM nilai n
                JOIN murid m ON n.murid_id = m.id
                JOIN kelas k ON m.kelas_id = k.id
                WHERE n.semester_id = :semester_id
                GROUP BY k.id, k.nama_kelas
                ORDER BY k.nama_kelas ASC
            ", ['semester_id' => $selectedSemesterId]);
        }

        return view('admin.dashboard', compact(
            'semesters',
            'jurusans',
            'selectedSemesterId',
            'selectedJurusanId',
            'totalMurid',
            'totalKelas',
            'totalJurusan',
            'totalMapel',
            'totalNilai',
            'topSekolah',
            'topJurusan',
            'distribution',
            'classAverages'
        ));
    }
}

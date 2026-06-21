<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RankingEngine
{
    /**
     * Get class ranking leaderboard for a specific class and semester.
     */
    public function getClassRankingList(int $kelasId, int $semesterId)
    {
        $query = "
            SELECT 
                sub.murid_id,
                sub.nama_lengkap,
                sub.nis,
                sub.avg_nilai,
                sub.total_nilai,
                DENSE_RANK() OVER (ORDER BY sub.avg_nilai DESC) as rank_kelas
            FROM (
                SELECT 
                    m.id as murid_id,
                    m.nama_lengkap,
                    m.nis,
                    ROUND(AVG(n.nilai), 2) as avg_nilai,
                    ROUND(SUM(n.nilai), 2) as total_nilai
                FROM murid m
                JOIN nilai n ON n.murid_id = m.id
                WHERE m.kelas_id = :kelas_id AND n.semester_id = :semester_id
                GROUP BY m.id, m.nama_lengkap, m.nis
            ) sub
            ORDER BY rank_kelas ASC
        ";

        return DB::select($query, [
            'kelas_id' => $kelasId,
            'semester_id' => $semesterId
        ]);
    }

    /**
     * Get parallel department ranking leaderboard for a department, level, and semester.
     */
    public function getParallelRankingList(int $jurusanId, string $tingkat, int $semesterId)
    {
        $query = "
            SELECT 
                sub.murid_id,
                sub.nama_lengkap,
                sub.nis,
                sub.nama_kelas,
                sub.avg_nilai,
                sub.total_nilai,
                DENSE_RANK() OVER (ORDER BY sub.avg_nilai DESC) as rank_paralel
            FROM (
                SELECT 
                    m.id as murid_id,
                    m.nama_lengkap,
                    m.nis,
                    k.nama_kelas,
                    ROUND(AVG(n.nilai), 2) as avg_nilai,
                    ROUND(SUM(n.nilai), 2) as total_nilai
                FROM murid m
                JOIN kelas k ON m.kelas_id = k.id
                JOIN nilai n ON n.murid_id = m.id
                WHERE k.jurusan_id = :jurusan_id 
                  AND k.tingkat = :tingkat 
                  AND n.semester_id = :semester_id
                GROUP BY m.id, m.nama_lengkap, m.nis, k.nama_kelas
            ) sub
            ORDER BY rank_paralel ASC
        ";

        return DB::select($query, [
            'jurusan_id' => $jurusanId,
            'tingkat' => $tingkat,
            'semester_id' => $semesterId
        ]);
    }

    /**
     * Get single student rank and grades details for a specific semester.
     */
    public function getStudentRankings(int $muridId, int $semesterId)
    {
        // 1. Dapatkan info murid
        $murid = DB::table('murid')
            ->join('kelas', 'murid.kelas_id', '=', 'kelas.id')
            ->select('murid.id', 'murid.kelas_id', 'kelas.tingkat', 'kelas.jurusan_id')
            ->where('murid.id', $muridId)
            ->first();

        if (!$murid) {
            return [
                'total_nilai' => 0,
                'avg_nilai' => 0,
                'rank_kelas' => '-',
                'total_murid_kelas' => 0,
                'rank_paralel' => '-',
                'total_murid_paralel' => 0
            ];
        }

        // 2. Hitung ranking kelas
        $classQuery = "
            SELECT * FROM (
                SELECT 
                    sub.murid_id,
                    sub.avg_nilai,
                    sub.total_nilai,
                    DENSE_RANK() OVER (ORDER BY sub.avg_nilai DESC) as rank_kelas,
                    COUNT(*) OVER () as total_murid_kelas
                FROM (
                    SELECT 
                        m.id as murid_id,
                        ROUND(AVG(n.nilai), 2) as avg_nilai,
                        ROUND(SUM(n.nilai), 2) as total_nilai
                    FROM murid m
                    JOIN nilai n ON n.murid_id = m.id
                    WHERE m.kelas_id = :kelas_id AND n.semester_id = :semester_id
                    GROUP BY m.id
                ) sub
            ) rank_sub
            WHERE rank_sub.murid_id = :murid_id
        ";

        $classResult = DB::selectOne($classQuery, [
            'kelas_id' => $murid->kelas_id,
            'semester_id' => $semesterId,
            'murid_id' => $muridId
        ]);

        // 3. Hitung ranking paralel
        $parallelQuery = "
            SELECT * FROM (
                SELECT 
                    sub.murid_id,
                    sub.avg_nilai,
                    sub.total_nilai,
                    DENSE_RANK() OVER (ORDER BY sub.avg_nilai DESC) as rank_paralel,
                    COUNT(*) OVER () as total_murid_paralel
                FROM (
                    SELECT 
                        m.id as murid_id,
                        ROUND(AVG(n.nilai), 2) as avg_nilai,
                        ROUND(SUM(n.nilai), 2) as total_nilai
                    FROM murid m
                    JOIN kelas k ON m.kelas_id = k.id
                    JOIN nilai n ON n.murid_id = m.id
                    WHERE k.jurusan_id = :jurusan_id 
                      AND k.tingkat = :tingkat 
                      AND n.semester_id = :semester_id
                    GROUP BY m.id
                ) sub
            ) rank_sub
            WHERE rank_sub.murid_id = :murid_id
        ";

        $parallelResult = DB::selectOne($parallelQuery, [
            'jurusan_id' => $murid->jurusan_id,
            'tingkat' => $murid->tingkat,
            'semester_id' => $semesterId,
            'murid_id' => $muridId
        ]);

        return [
            'total_nilai' => $classResult->total_nilai ?? 0,
            'avg_nilai' => $classResult->avg_nilai ?? 0,
            'rank_kelas' => $classResult->rank_kelas ?? '-',
            'total_murid_kelas' => $classResult->total_murid_kelas ?? 0,
            'rank_paralel' => $parallelResult->rank_paralel ?? '-',
            'total_murid_paralel' => $parallelResult->total_murid_paralel ?? 0
        ];
    }
}

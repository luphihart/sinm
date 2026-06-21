<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Models\Semester;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClassGradeTemplateExport implements FromCollection, WithHeadings
{
    protected $kelasId;
    protected $semesterId;

    public function __construct($kelasId, $semesterId)
    {
        $this->kelasId = $kelasId;
        $this->semesterId = $semesterId;
    }

    public function collection()
    {
        $kelas = Kelas::findOrFail($this->kelasId);
        $semester = Semester::findOrFail($this->semesterId);
        $students = $kelas->murid()->where('status', 'aktif')->orderBy('nama_lengkap', 'asc')->get();
        $subjects = MataPelajaran::orderBy('urutan', 'asc')->get();

        $rows = [];
        foreach ($students as $student) {
            $row = [
                'nis' => $student->nis,
                'nama' => $student->nama_lengkap,
                'semester' => $semester->semester_ke,
            ];

            foreach ($subjects as $subject) {
                // Get existing grade if any
                $grade = Nilai::where('murid_id', $student->id)
                    ->where('semester_id', $semester->id)
                    ->where('mata_pelajaran_id', $subject->id)
                    ->first();
                $row[$subject->kode_mapel] = $grade ? $grade->nilai : '';
            }

            $rows[] = $row;
        }

        return collect($rows);
    }

    public function headings(): array
    {
        $subjects = MataPelajaran::orderBy('urutan', 'asc')->get();
        $headings = [
            'NIS',
            'Nama Siswa',
            'Semester Ke',
        ];

        foreach ($subjects as $subject) {
            $headings[] = $subject->kode_mapel;
        }

        return $headings;
    }
}

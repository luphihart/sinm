<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RankingKelasExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = [];
        foreach ($this->data as $item) {
            $rows[] = [
                'Rank' => $item->rank_kelas,
                'NIS' => $item->nis,
                'Nama Lengkap' => $item->nama_lengkap,
                'Rata-rata Nilai' => $item->avg_nilai,
                'Total Nilai' => $item->total_nilai,
            ];
        }
        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Ranking',
            'NIS',
            'Nama Lengkap',
            'Rata-rata Nilai',
            'Total Nilai',
        ];
    }
}

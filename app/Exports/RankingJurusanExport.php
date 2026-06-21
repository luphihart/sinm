<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RankingJurusanExport implements FromCollection, WithHeadings
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
                'Rank Paralel' => $item->rank_paralel,
                'NIS' => $item->nis,
                'Nama Lengkap' => $item->nama_lengkap,
                'Kelas' => $item->nama_kelas,
                'Rata-rata Nilai' => $item->avg_nilai,
                'Total Nilai' => $item->total_nilai,
            ];
        }
        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Ranking Paralel',
            'NIS',
            'Nama Lengkap',
            'Kelas',
            'Rata-rata Nilai',
            'Total Nilai',
        ];
    }
}

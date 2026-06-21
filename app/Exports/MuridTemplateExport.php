<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MuridTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([
            [
                '22001',
                '0061234501',
                'Ahmad Fauzi',
                'L',
                'XII RPL 1',
                '2024'
            ],
            [
                '22002',
                '0075432102',
                'Siti Aminah',
                'P',
                'XII RPL 1',
                '2024'
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'NIS',
            'NISN',
            'Nama Lengkap',
            'Jenis Kelamin (L/P)',
            'Nama Kelas',
            'Angkatan'
        ];
    }
}

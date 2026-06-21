<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Ranking Kelas - {{ $kelas->nama_kelas }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #1e293b;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 8.5pt;
            color: #475569;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-label {
            width: 15%;
            font-weight: bold;
        }
        .info-colon {
            width: 2%;
        }
        .info-value {
            width: 83%;
        }
        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .leaderboard-table th, .leaderboard-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        .leaderboard-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center !important;
        }
        .text-left {
            text-align: left !important;
        }
        .text-right {
            text-align: right !important;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
        }
        .signature-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>{{ \App\Models\Setting::get('school_name', 'Sistem Informasi Nilai Murid') }}</h2>
        <p>
            {{ \App\Models\Setting::get('school_address', 'Jl. Pendidikan No. 45') }}
            @if(\App\Models\Setting::get('school_phone')) | Telp: {{ \App\Models\Setting::get('school_phone') }} @endif
            @if(\App\Models\Setting::get('school_website')) | Website: {{ \App\Models\Setting::get('school_website') }} @endif
        </p>
    </div>

    <h3 class="text-center" style="margin-bottom: 15px; text-transform: uppercase; font-size: 11pt;">Laporan Ranking Kelas (Leaderboard)</h3>

    <table class="info-table">
        <tr>
            <td class="info-label">Kelas</td>
            <td class="info-colon">:</td>
            <td class="info-value"><strong>{{ $kelas->nama_kelas }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Jurusan</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $kelas->jurusan->nama_jurusan }} ({{ $kelas->jurusan->kode_jurusan }})</td>
        </tr>
        <tr>
            <td class="info-label">Semester</td>
            <td class="info-colon">:</td>
            <td class="info-value">Semester {{ $semester->semester_ke }} (Tahun Ajaran {{ $semester->tahun_ajaran }})</td>
        </tr>
    </table>

    <table class="leaderboard-table">
        <thead>
            <tr>
                <th width="80px">Peringkat</th>
                <th width="120px">NIS</th>
                <th class="text-left">Nama Lengkap</th>
                <th width="120px">Total Nilai</th>
                <th width="120px">Rata-rata Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rankingList as $row)
                <tr>
                    <td class="text-center" style="font-weight: bold;">{{ $row->rank_kelas }}</td>
                    <td class="text-center">{{ $row->nis }}</td>
                    <td class="text-left">{{ $row->nama_lengkap }}</td>
                    <td class="text-center">{{ number_format($row->total_nilai, 2) }}</td>
                    <td class="text-center" style="font-weight: bold; color: #2563eb;">{{ number_format($row->avg_nilai, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 15px; color: #64748b;">Belum ada data nilai kelas pada semester ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                <p>Mengetahui,</p>
                <p>Kepala Sekolah,</p>
                <div class="signature-space"></div>
                <p><strong><u>{{ \App\Models\Setting::get('headmaster_name', 'Nama Kepala Sekolah') }}</u></strong></p>
                <p>NIP. {{ \App\Models\Setting::get('headmaster_nip', '-') }}</p>
            </td>
            <td>
                <p>{{ \App\Models\Setting::get('school_address') ? explode(',', \App\Models\Setting::get('school_address'))[0] : 'Kota Belajar' }}, {{ date('d F Y') }}</p>
                <p>Wali Kelas,</p>
                <div class="signature-space"></div>
                <p><strong>__________________________</strong></p>
            </td>
        </tr>
    </table>

</body>
</html>

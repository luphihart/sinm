<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Belajar - {{ $murid->nama_lengkap }}</title>
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
            width: 33%;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .grades-table th, .grades-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }
        .grades-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center !important;
        }
        .text-right {
            text-align: right !important;
        }
        .stats-box {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .stats-box td {
            border: 1px solid #94a3b8;
            padding: 8px;
            background-color: #f8fafc;
        }
        .stats-title {
            font-size: 8pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .stats-value {
            font-size: 12pt;
            font-weight: bold;
            color: #2563eb;
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

    <h3 class="text-center" style="margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 11pt;">Laporan Hasil Belajar Siswa (Rapor)</h3>

    <table class="info-table">
        <tr>
            <td class="info-label">Nama Siswa</td>
            <td class="info-colon">:</td>
            <td class="info-value"><strong>{{ $murid->nama_lengkap }}</strong></td>
            <td class="info-label">Kelas</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $murid->kelas->nama_kelas }}</td>
        </tr>
        <tr>
            <td class="info-label">NIS / NISN</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $murid->nis }} / {{ $murid->nisn ?? '-' }}</td>
            <td class="info-label">Jurusan</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $murid->kelas->jurusan->nama_jurusan }}</td>
        </tr>
        <tr>
            <td class="info-label">Semester</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $semester->semester_ke }} (Tahun Ajaran {{ $semester->tahun_ajaran }})</td>
            <td class="info-label">Status</td>
            <td class="info-colon">:</td>
            <td class="info-value" style="text-transform: capitalize;">{{ $murid->status }}</td>
        </tr>
    </table>

    <table class="grades-table">
        <thead>
            <tr>
                <th width="40px">No</th>
                <th width="120px">Kode Mapel</th>
                <th>Mata Pelajaran</th>
                <th width="80px">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($grades as $idx => $g)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ $g->mataPelajaran->kode_mapel }}</td>
                    <td>{{ $g->mataPelajaran->nama_mapel }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ number_format($g->nilai, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: 15px; color: #64748b;">Belum ada data nilai semester ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="stats-box">
        <tr>
            <td width="25%">
                <div class="stats-title">Total Nilai</div>
                <div class="stats-value">{{ $rankings['total_nilai'] }}</div>
            </td>
            <td width="25%">
                <div class="stats-title">Rata-rata Nilai</div>
                <div class="stats-value">{{ $rankings['avg_nilai'] }}</div>
            </td>
            <td width="25%">
                <div class="stats-title">Ranking Kelas</div>
                <div class="stats-value">{{ $rankings['rank_kelas'] }} <span style="font-size: 8pt; font-weight: normal; color: #64748b;">dari {{ $rankings['total_murid_kelas'] }}</span></div>
            </td>
            <td width="25%">
                <div class="stats-title">Ranking Paralel</div>
                <div class="stats-value">{{ $rankings['rank_paralel'] }} <span style="font-size: 8pt; font-weight: normal; color: #64748b;">dari {{ $rankings['total_murid_paralel'] }}</span></div>
            </td>
        </tr>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                <p>Mengetahui,</p>
                <p>Orang Tua / Wali Murid</p>
                <div class="signature-space"></div>
                <p>__________________________</p>
            </td>
            <td>
                <p>{{ \App\Models\Setting::get('school_address') ? explode(',', \App\Models\Setting::get('school_address'))[0] : 'Kota Belajar' }}, {{ date('d F Y') }}</p>
                <p>Kepala Sekolah,</p>
                <div class="signature-space"></div>
                <p><strong><u>{{ \App\Models\Setting::get('headmaster_name', 'Nama Kepala Sekolah') }}</u></strong></p>
                <p>NIP. {{ \App\Models\Setting::get('headmaster_nip', '-') }}</p>
            </td>
        </tr>
    </table>

</body>
</html>

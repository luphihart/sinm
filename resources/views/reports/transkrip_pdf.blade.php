<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transkrip Nilai Akademik - {{ $murid->nama_lengkap }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h2 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 8pt;
            color: #475569;
        }
        .info-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2px 0;
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
        .semester-section {
            margin-bottom: 12px;
        }
        .semester-title {
            background-color: #e2e8f0;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 9.5pt;
            border: 1px solid #94a3b8;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .grades-table th, .grades-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            text-align: left;
        }
        .grades-table th {
            background-color: #f8fafc;
            font-weight: bold;
            text-align: center;
            font-size: 8.5pt;
        }
        .text-center {
            text-align: center !important;
        }
        .summary-box {
            width: 100%;
            border: 2px solid #000;
            background-color: #f1f5f9;
            padding: 10px;
            margin-top: 12px;
            margin-bottom: 20px;
        }
        .summary-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #94a3b8;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .summary-item {
            font-size: 9.5pt;
            margin-bottom: 3px;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
        }
        .signature-space {
            height: 50px;
        }
        .page-break {
            page-break-after: always;
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

    <h3 class="text-center" style="margin-bottom: 12px; text-transform: uppercase; font-size: 11pt;">Transkrip Nilai Akademik Lengkap</h3>

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
            <td class="info-label">Angkatan</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $murid->angkatan }}</td>
            <td class="info-label">Status Murid</td>
            <td class="info-colon">:</td>
            <td class="info-value" style="text-transform: capitalize;">{{ $murid->status }}</td>
        </tr>
    </table>

    <!-- Loop Semester -->
    @php $semCount = 0; @endphp
    @foreach($transkrip as $item)
        @php $semCount++; @endphp
        
        <!-- Setiap 3 semester, berikan page break agar layout tetap rapi jika terpotong -->
        @if($semCount == 4)
            <div class="page-break"></div>
        @endif

        <div class="semester-section">
            <div class="semester-title">Semester {{ $item['semester']->semester_ke }} (Tahun Ajaran {{ $item['semester']->tahun_ajaran }})</div>
            <table class="grades-table">
                <thead>
                    <tr>
                        <th width="40px">No</th>
                        <th width="100px">Kode Mapel</th>
                        <th>Mata Pelajaran</th>
                        <th width="80px">Nilai</th>
                        <th width="80px">Rank Kls</th>
                        <th width="80px">Rank Prl</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item['grades'] as $idx => $g)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td class="text-center">{{ $g->mataPelajaran->kode_mapel }}</td>
                            <td>{{ $g->mataPelajaran->nama_mapel }}</td>
                            <td class="text-center" style="font-weight: bold;">{{ number_format($g->nilai, 2) }}</td>
                            @if($idx == 0)
                                <td class="text-center" rowspan="{{ $item['grades']->count() }}" style="vertical-align: middle; font-weight: bold;">
                                    {{ $item['rankings']['rank_kelas'] }} <br>
                                    <span style="font-size: 7pt; font-weight: normal; color: #64748b;">/{{ $item['rankings']['total_murid_kelas'] }}</span>
                                </td>
                                <td class="text-center" rowspan="{{ $item['grades']->count() }}" style="vertical-align: middle; font-weight: bold;">
                                    {{ $item['rankings']['rank_paralel'] }} <br>
                                    <span style="font-size: 7pt; font-weight: normal; color: #64748b;">/{{ $item['rankings']['total_murid_paralel'] }}</span>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="summary-box">
        <div class="summary-title">Ringkasan Nilai Kumulatif</div>
        <div class="summary-item">Total Mata Pelajaran Terdata: <strong>{{ $totalSKS }}</strong> Mapel</div>
        <div class="summary-item">Indeks Rata-rata Kumulatif (GPA): <strong style="color: #2563eb; font-size: 10.5pt;">{{ number_format($gpa, 2) }}</strong> (Skala 0 - 100)</div>
    </div>

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

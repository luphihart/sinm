@extends('layouts.app')

@section('title', 'Dashboard Murid')
@section('page_title', 'Rapor Akademik Murid')

@section('content')
<!-- Profil & Ringkasan Akademik -->
<div class="row g-4 mb-4">
    <div class="col-lg-12">
        <div class="glass-card p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary rounded-3 p-3 me-3" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-user-graduate fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-800 m-0">{{ $murid->nama_lengkap }}</h4>
                        <span class="text-muted small">NIS: {{ $murid->nis }} | NISN: {{ $murid->nisn ?? '-' }} | Angkatan: {{ $murid->angkatan }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('murid.nilai.export.transkrip') }}" target="_blank" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-receipt me-2"></i> Cetak Transkrip Lengkap
                    </a>
                </div>
            </div>
            
            <div class="row g-3 mt-3 pt-3 border-top">
                <div class="col-6 col-md-3">
                    <span class="text-muted small d-block">Kelas</span>
                    <span class="fw-600">{{ $murid->kelas->nama_kelas }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted small d-block">Jurusan</span>
                    <span class="fw-600">{{ $murid->kelas->jurusan->nama_jurusan }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted small d-block">Tingkat Akademik</span>
                    <span class="fw-600">Kelas {{ $murid->kelas->tingkat }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted small d-block">Status Murid</span>
                    <span class="badge bg-success">Aktif</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Semester Filter & Papan Nilai -->
<div class="row g-4 mb-4">
    <!-- Kolom Kiri: Pilih Semester & Stats -->
    <div class="col-lg-4">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-700 mb-3"><i class="fa-solid fa-filter text-primary me-2"></i>Pilih Semester</h5>
                <form action="{{ route('murid.dashboard') }}" method="GET" id="semesterFilterForm" class="mb-4">
                    <select name="semester_id" id="semester_id" class="form-select form-select-lg" onchange="document.getElementById('semesterFilterForm').submit()">
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ $selectedSemesterId == $sem->id ? 'selected' : '' }}>
                                Semester {{ $sem->semester_ke }} ({{ $sem->tahun_ajaran }})
                            </option>
                        @endforeach
                    </select>
                </form>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="p-3 bg-primary-subtle text-primary rounded-3 text-center">
                            <small class="text-muted d-block" style="font-size: 0.7rem;">Total Nilai</small>
                            <span class="fs-4 fw-800">{{ $rankings['total_nilai'] }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-success-subtle text-success rounded-3 text-center">
                            <small class="text-muted d-block" style="font-size: 0.7rem;">Rata-rata</small>
                            <span class="fs-4 fw-800">{{ $rankings['avg_nilai'] }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-warning-subtle text-warning rounded-3 text-center">
                            <small class="text-muted d-block" style="font-size: 0.7rem;">Ranking Kelas</small>
                            <span class="fs-4 fw-800">{{ $rankings['rank_kelas'] }} <small class="text-muted" style="font-size: 0.75rem;">/{{ $rankings['total_murid_kelas'] }}</small></span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-danger-subtle text-danger rounded-3 text-center">
                            <small class="text-muted d-block" style="font-size: 0.7rem;">Rank Paralel</small>
                            <span class="fs-4 fw-800">{{ $rankings['rank_paralel'] }} <small class="text-muted" style="font-size: 0.75rem;">/{{ $rankings['total_murid_paralel'] }}</small></span>
                        </div>
                    </div>
                </div>
            </div>

            @if($grades->isNotEmpty())
                <div class="pt-3 border-top">
                    <a href="{{ route('murid.nilai.export.rapor', $selectedSemesterId) }}" target="_blank" class="btn btn-primary-custom w-100 py-3">
                        <i class="fa-solid fa-file-pdf me-2"></i> Unduh Rapor Semester PDF
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Kolom Kanan: Grafik Tren Nilai -->
    <div class="col-lg-8">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-700 mb-4"><i class="fa-solid fa-chart-line text-primary me-2"></i>Grafik Perkembangan Rata-rata Nilai</h5>
            <div style="height: 280px; display: flex; align-items: center; justify-content: center;">
                <canvas id="progressChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Nilai Detail -->
<div class="row g-4">
    <div class="col-12">
        <div class="glass-card p-4">
            <h5 class="fw-700 mb-4"><i class="fa-solid fa-clipboard-list text-primary me-2"></i>Rincian Nilai Semester</h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="80px">No</th>
                            <th class="d-none d-sm-table-cell">Kode Mapel</th>
                            <th>Mata Pelajaran</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grades as $idx => $g)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td class="d-none d-sm-table-cell"><span class="badge bg-light text-dark border">{{ $g->mataPelajaran->kode_mapel }}</span></td>
                                <td>
                                    <span class="fw-600 d-block">{{ $g->mataPelajaran->nama_mapel }}</span>
                                    <span class="text-muted small d-block d-sm-none">Kode: {{ $g->mataPelajaran->kode_mapel }}</span>
                                </td>
                                <td class="fw-bold text-primary">{{ $g->nilai }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted small">
                                    <i class="fa-solid fa-ban d-block fs-3 mb-2"></i> Belum ada data nilai pada semester ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Grafik Perkembangan Nilai Siswa (Line Chart)
    const progressCtx = document.getElementById('progressChart').getContext('2d');
    const semLabels = [@foreach($chartData as $cd) 'Sem {{ $cd->semester_ke }}', @endforeach];
    const semData = [@foreach($chartData as $cd) {{ $cd->avg_nilai }}, @endforeach];

    new Chart(progressCtx, {
        type: 'line',
        data: {
            labels: semLabels,
            datasets: [{
                label: 'Rata-rata Nilai',
                data: semData,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#2563eb',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    ticks: {
                        color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#f8fafc' : '#1e293b'
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#f8fafc' : '#1e293b'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endsection

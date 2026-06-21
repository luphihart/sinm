@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Dashboard Administrasi')

@section('content')
<div class="row g-4 mb-4">
    <!-- Filter Semester Global -->
    <div class="col-12">
        <div class="glass-card p-4">
            <form action="{{ route('admin.dashboard') }}" method="GET" id="dashboardFilterForm" class="row align-items-center g-3">
                <div class="col-md-4">
                    <label for="semester_id" class="form-label fw-600 small">Pilih Semester Aktif</label>
                    <select name="semester_id" id="semester_id" class="form-select" onchange="document.getElementById('dashboardFilterForm').submit()">
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ $selectedSemesterId == $sem->id ? 'selected' : '' }}>
                                Semester {{ $sem->semester_ke }} (Tahun Ajaran {{ $sem->tahun_ajaran }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="jurusan_id" class="form-label fw-600 small">Filter Jurusan Leaderboard</label>
                    <select name="jurusan_id" id="jurusan_id" class="form-select" onchange="document.getElementById('dashboardFilterForm').submit()">
                        @foreach($jurusans as $jur)
                            <option value="{{ $jur->id }}" {{ $selectedJurusanId == $jur->id ? 'selected' : '' }}>
                                {{ $jur->nama_jurusan }} ({{ $jur->kode_jurusan }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Statistik Kartu -->
<div class="row g-4 mb-5">
    <div class="col-md-4 col-lg-2-4 col-sm-6">
        <div class="glass-card p-4 d-flex align-items-center">
            <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3" style="width: 54px; height: 54px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-users fs-4"></i>
            </div>
            <div>
                <h5 class="fw-800 m-0">{{ $totalMurid }}</h5>
                <span class="text-muted small">Total Murid</span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2-4 col-sm-6">
        <div class="glass-card p-4 d-flex align-items-center">
            <div class="rounded-circle bg-success-subtle text-success p-3 me-3" style="width: 54px; height: 54px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-school fs-4"></i>
            </div>
            <div>
                <h5 class="fw-800 m-0">{{ $totalKelas }}</h5>
                <span class="text-muted small">Total Kelas</span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2-4 col-sm-6">
        <div class="glass-card p-4 d-flex align-items-center">
            <div class="rounded-circle bg-warning-subtle text-warning p-3 me-3" style="width: 54px; height: 54px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-network-wired fs-4"></i>
            </div>
            <div>
                <h5 class="fw-800 m-0">{{ $totalJurusan }}</h5>
                <span class="text-muted small">Total Jurusan</span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2-4 col-sm-6">
        <div class="glass-card p-4 d-flex align-items-center">
            <div class="rounded-circle bg-info-subtle text-info p-3 me-3" style="width: 54px; height: 54px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-book fs-4"></i>
            </div>
            <div>
                <h5 class="fw-800 m-0">{{ $totalMapel }}</h5>
                <span class="text-muted small">Mata Pelajaran</span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2-4 col-sm-12">
        <div class="glass-card p-4 d-flex align-items-center">
            <div class="rounded-circle bg-danger-subtle text-danger p-3 me-3" style="width: 54px; height: 54px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-database fs-4"></i>
            </div>
            <div>
                <h5 class="fw-800 m-0">{{ $totalNilai }}</h5>
                <span class="text-muted small">Data Nilai</span>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-5">
    <div class="col-lg-6">
        <div class="glass-card p-4">
            <h5 class="fw-700 mb-4"><i class="fa-solid fa-chart-pie me-2 text-primary"></i>Distribusi Nilai Siswa</h5>
            <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                <canvas id="distribusiChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="glass-card p-4">
            <h5 class="fw-700 mb-4"><i class="fa-solid fa-chart-bar me-2 text-teal" style="color: var(--accent-color);"></i>Rata-rata Nilai per Kelas</h5>
            <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                <canvas id="kelasChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Leaderboards Row -->
<div class="row g-4">
    <!-- Top 10 Ranking Sekolah -->
    <div class="col-lg-6">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-700 mb-3"><i class="fa-solid fa-award text-warning me-2"></i>Top 10 Ranking Sekolah</h5>
            <p class="text-muted small mb-4">Peringkat 10 murid dengan rata-rata nilai tertinggi di sekolah pada semester terpilih.</p>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="80px">Rank</th>
                            <th class="d-none d-md-table-cell">NIS</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th class="text-end">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topSekolah as $row)
                            <tr>
                                <td>
                                    @if($row->ranking == 1)
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-trophy"></i> 1</span>
                                    @elseif($row->ranking == 2)
                                        <span class="badge bg-secondary"><i class="fa-solid fa-medal"></i> 2</span>
                                    @elseif($row->ranking == 3)
                                        <span class="badge bg-bronze" style="background-color: #cd7f32;"><i class="fa-solid fa-medal"></i> 3</span>
                                    @else
                                        <span class="fw-bold px-2 text-muted">{{ $row->ranking }}</span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell small fw-600 text-muted">{{ $row->nis }}</td>
                                <td class="fw-600">
                                    <span class="d-block">{{ $row->nama_lengkap }}</span>
                                    <span class="text-muted small d-block d-md-none">NIS: {{ $row->nis }}</span>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $row->nama_kelas }}</span></td>
                                <td class="text-end fw-bold text-primary">{{ $row->avg_nilai }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">
                                    <i class="fa-solid fa-ban d-block fs-3 mb-2"></i> Tidak ada data nilai pada semester ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top 10 Ranking Jurusan -->
    <div class="col-lg-6">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-700 mb-3"><i class="fa-solid fa-trophy text-info me-2"></i>Top 10 Ranking Jurusan</h5>
            <p class="text-muted small mb-4">Peringkat 10 murid dengan rata-rata nilai tertinggi pada jurusan terpilih.</p>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="80px">Rank</th>
                            <th class="d-none d-md-table-cell">NIS</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th class="text-end">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topJurusan as $row)
                            <tr>
                                <td>
                                    @if($row->ranking == 1)
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-trophy"></i> 1</span>
                                    @elseif($row->ranking == 2)
                                        <span class="badge bg-secondary"><i class="fa-solid fa-medal"></i> 2</span>
                                    @elseif($row->ranking == 3)
                                        <span class="badge bg-bronze" style="background-color: #cd7f32;"><i class="fa-solid fa-medal"></i> 3</span>
                                    @else
                                        <span class="fw-bold px-2 text-muted">{{ $row->ranking }}</span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell small fw-600 text-muted">{{ $row->nis }}</td>
                                <td class="fw-600">
                                    <span class="d-block">{{ $row->nama_lengkap }}</span>
                                    <span class="text-muted small d-block d-md-none">NIS: {{ $row->nis }}</span>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $row->nama_kelas }}</span></td>
                                <td class="text-end fw-bold text-teal" style="color: var(--accent-color);">{{ $row->avg_nilai }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">
                                    <i class="fa-solid fa-ban d-block fs-3 mb-2"></i> Tidak ada data nilai pada jurusan/semester ini.
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

@section('styles')
<style>
    .col-lg-2-4 {
        flex: 0 0 auto;
        width: 20%;
    }
    @media (max-width: 1199.98px) {
        .col-lg-2-4 {
            width: 33.3333%;
        }
    }
    @media (max-width: 767.98px) {
        .col-lg-2-4 {
            width: 50%;
        }
    }
    @media (max-width: 575.98px) {
        .col-lg-2-4 {
            width: 100%;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // 1. Chart Distribusi Nilai (Pie / Doughnut Chart)
    const distCtx = document.getElementById('distribusiChart').getContext('2d');
    new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: ['Sangat Baik (>=85)', 'Baik (75-84)', 'Cukup (60-74)', 'Kurang (<60)'],
            datasets: [{
                data: [
                    {{ $distribution['antara_85_100'] }},
                    {{ $distribution['antara_75_84'] }},
                    {{ $distribution['antara_60_74'] }},
                    {{ $distribution['kurang_dari_60'] }}
                ],
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                borderWidth: 1,
                borderColor: 'rgba(255, 255, 255, 0.1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#f8fafc' : '#1e293b'
                    }
                }
            }
        }
    });

    // 2. Chart Rata-rata per Kelas (Bar Chart)
    const kelasCtx = document.getElementById('kelasChart').getContext('2d');
    const classLabels = [@foreach($classAverages as $ca) '{{ $ca->nama_kelas }}', @endforeach];
    const classData = [@foreach($classAverages as $ca) {{ $ca->avg_nilai }}, @endforeach];
    
    new Chart(kelasCtx, {
        type: 'bar',
        data: {
            labels: classLabels,
            datasets: [{
                label: 'Rata-rata Nilai',
                data: classData,
                backgroundColor: '#0d9488',
                borderRadius: 8
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

@extends('layouts.app')

@section('title', 'Manajemen Nilai & Ranking')
@section('page_title', 'Manajemen Nilai & Ranking')

@section('content')
<div class="row g-4 mb-4">
    <!-- Filter Siswa & Input Nilai -->
    <div class="col-lg-6">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-700 mb-4"><i class="fa-solid fa-graduation-cap text-primary me-2"></i>Filter Akademik Murid</h5>
            <form action="{{ route('admin.nilai.index') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="kelas_id" class="form-label small fw-600">Pilih Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $kel)
                                <option value="{{ $kel->id }}" {{ $selectedKelasId == $kel->id ? 'selected' : '' }}>
                                    {{ $kel->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="murid_id" class="form-label small fw-600">Pilih Murid</label>
                        <select name="murid_id" id="murid_id" class="form-select" onchange="this.form.submit()" {{ empty($selectedKelasId) ? 'disabled' : '' }}>
                            <option value="">-- Pilih Murid --</option>
                            @foreach($murids as $m)
                                <option value="{{ $m->id }}" {{ $selectedMuridId == $m->id ? 'selected' : '' }}>
                                    {{ $m->nama_lengkap }} ({{ $m->nis }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="semester_id" class="form-label small fw-600">Pilih Semester</label>
                        <select name="semester_id" id="semester_id" class="form-select" onchange="this.form.submit()" {{ empty($selectedMuridId) ? 'disabled' : '' }}>
                            <option value="">-- Pilih Semester --</option>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}" {{ $selectedSemesterId == $sem->id ? 'selected' : '' }}>
                                    Semester {{ $sem->semester_ke }} (Tahun Ajaran {{ $sem->tahun_ajaran }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            @if($selectedMuridId && $selectedSemesterId)
                <div class="mt-4 pt-3 border-top d-flex gap-2">
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#inputGradeModal">
                        <i class="fa-solid fa-plus me-1"></i> Input Nilai Baru
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Import Excel Card -->
    <div class="col-lg-6">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-700 mb-3"><i class="fa-solid fa-file-excel text-success me-2"></i>Import Nilai via Excel</h5>
            
            <ul class="nav nav-pills mb-3" id="importTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-1 px-3 small" id="import-row-tab" data-bs-toggle="pill" data-bs-target="#import-row-panel" type="button" role="tab" aria-controls="import-row-panel" aria-selected="true">
                        Per Baris (Semua Kelas)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-1 px-3 small" id="import-grid-tab" data-bs-toggle="pill" data-bs-target="#import-grid-panel" type="button" role="tab" aria-controls="import-grid-panel" aria-selected="false">
                        Satu Kelas (Grid Mapel)
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="importTabsContent">
                <!-- PANEL IMPORT PER BARIS -->
                <div class="tab-pane fade show active" id="import-row-panel" role="tabpanel" aria-labelledby="import-row-tab">
                    <p class="text-muted small mb-3">Format kolom file: <strong>NIS | Semester | Mata Pelajaran | Nilai</strong>. Mata Pelajaran dapat diisi Kode atau Nama Mapel.</p>
                    <form action="{{ route('admin.nilai.import.preview') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file_excel_row" class="form-label small fw-600">Pilih File Excel / CSV</label>
                            <input class="form-control" type="file" id="file_excel_row" name="file_excel" accept=".xlsx,.xls,.csv" required>
                        </div>
                        <button type="submit" class="btn btn-outline-success w-100 fw-500">
                            <i class="fa-solid fa-upload me-2"></i> Unggah & Review Preview
                        </button>
                    </form>
                </div>

                <!-- PANEL IMPORT GRID KELAS -->
                <div class="tab-pane fade" id="import-grid-panel" role="tabpanel" aria-labelledby="import-grid-tab">
                    <p class="text-muted small mb-3">1. Unduh template kelas & semester terlebih dahulu:</p>
                    <form action="{{ route('admin.nilai.import-class.template') }}" method="GET" class="mb-3 p-2 bg-light rounded border">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <select name="kelas_id" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $kel)
                                        <option value="{{ $kel->id }}">{{ $kel->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <select name="semester_id" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Semester --</option>
                                    @foreach($semesters as $sem)
                                        <option value="{{ $sem->id }}">Semester {{ $sem->semester_ke }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-primary w-100 fw-600">
                            <i class="fa-solid fa-download me-1"></i> Unduh Template Grid (.xlsx)
                        </button>
                    </form>

                    <p class="text-muted small mb-2">2. Isi nilai pada kolom mapel, lalu unggah file:</p>
                    <form action="{{ route('admin.nilai.import-class.preview') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file_excel_grid" class="form-label small fw-600">Pilih File Excel Hasil Pengisian</label>
                            <input class="form-control" type="file" id="file_excel_grid" name="file_excel" accept=".xlsx,.xls,.csv" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 fw-500">
                            <i class="fa-solid fa-upload me-2"></i> Unggah & Review Nilai Kelas
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tampilan Nilai & Hasil Siswa Terpilih -->
@if($selectedMuridId && $selectedSemesterId)
    <div class="row g-4 mb-5">
        <div class="col-12">
            <div class="glass-card p-4">
                @php $activeMurid = \App\Models\Murid::find($selectedMuridId); @endphp
                <div class="d-flex flex-wrap align-items-center justify-content-between border-bottom pb-4 mb-4 gap-3">
                    <div>
                        <h4 class="fw-800 m-0">{{ $activeMurid->nama_lengkap }}</h4>
                        <span class="text-muted small">NIS: {{ $activeMurid->nis }} | Kelas: {{ $activeMurid->kelas->nama_kelas }} | Jurusan: {{ $activeMurid->kelas->jurusan->nama_jurusan }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.nilai.export.rapor', [$selectedMuridId, $selectedSemesterId]) }}" target="_blank" class="btn btn-outline-danger">
                            <i class="fa-solid fa-file-pdf me-2"></i> Cetak Rapor PDF
                        </a>
                        <a href="{{ route('admin.nilai.export.transkrip', $selectedMuridId) }}" target="_blank" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-receipt me-2"></i> Cetak Transkrip Lengkap
                        </a>
                    </div>
                </div>

                <!-- Papan Statistik Siswa Real-time -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card bg-primary-subtle text-primary border-0 p-3" style="border-radius: 12px;">
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Total Nilai</small>
                            <span class="fs-4 fw-800">{{ $rankings['total_nilai'] }}</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-success-subtle text-success border-0 p-3" style="border-radius: 12px;">
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Rata-rata Nilai</small>
                            <span class="fs-4 fw-800">{{ $rankings['avg_nilai'] }}</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-warning-subtle text-warning border-0 p-3" style="border-radius: 12px;">
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Ranking Kelas</small>
                            <span class="fs-4 fw-800">{{ $rankings['rank_kelas'] }} <small class="text-muted" style="font-size: 0.8rem;">dari {{ $rankings['total_murid_kelas'] }}</small></span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-danger-subtle text-danger border-0 p-3" style="border-radius: 12px;">
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Ranking Paralel Jurusan</small>
                            <span class="fs-4 fw-800">{{ $rankings['rank_paralel'] }} <small class="text-muted" style="font-size: 0.8rem;">dari {{ $rankings['total_murid_paralel'] }}</small></span>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="80px">No</th>
                                <th class="d-none d-sm-table-cell">Kode Mapel</th>
                                <th>Nama Mata Pelajaran</th>
                                <th>Nilai</th>
                                <th width="120px" class="text-end">Aksi</th>
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
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editGradeModal{{ $g->id }}">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" onclick="confirmDeleteGrade('{{ $g->id }}')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-grade-form-{{ $g->id }}" action="{{ route('admin.nilai.destroy', $g->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Grade Modal -->
                                <div class="modal fade" id="editGradeModal{{ $g->id }}" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content glass-card">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title fw-700"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit Nilai</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.nilai.update', $g->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="murid_id" value="{{ $selectedMuridId }}">
                                                <input type="hidden" name="semester_id" value="{{ $selectedSemesterId }}">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-600">Mata Pelajaran</label>
                                                        <select name="mata_pelajaran_id" class="form-select" required>
                                                            @foreach($mapels as $map)
                                                                <option value="{{ $map->id }}" {{ $g->mata_pelajaran_id == $map->id ? 'selected' : '' }}>
                                                                    {{ $map->nama_mapel }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-600">Nilai (0.00 - 100.00)</label>
                                                        <input type="number" class="form-control" name="nilai" step="0.01" min="0" max="100" value="{{ $g->nilai }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary-custom">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small">
                                        <i class="fa-solid fa-ban d-block fs-3 mb-2"></i> Murid belum memiliki nilai di semester ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Leaderboards Lists (Tabs Kelas & Paralel) -->
<div class="row g-4">
    <div class="col-12">
        <div class="glass-card p-4">
            <h5 class="fw-700 mb-4"><i class="fa-solid fa-chart-column text-primary me-2"></i>Ranking & Papan Peringkat (Leaderboard)</h5>
            
            <ul class="nav nav-tabs border-bottom mb-4" id="rankingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-600 active" id="class-tab" data-bs-toggle="tab" data-bs-target="#class-panel" type="button" role="tab" aria-controls="class-panel" aria-selected="true">
                        <i class="fa-solid fa-school me-2"></i> Ranking Kelas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-600" id="parallel-tab" data-bs-toggle="tab" data-bs-target="#parallel-panel" type="button" role="tab" aria-controls="parallel-panel" aria-selected="false">
                        <i class="fa-solid fa-network-wired me-2"></i> Ranking Paralel Jurusan
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="rankingTabsContent">
                <!-- PANEL RANKING KELAS -->
                <div class="tab-pane fade show active" id="class-panel" role="tabpanel" aria-labelledby="class-tab">
                    <form action="{{ route('admin.nilai.index') }}" method="GET" class="row g-3 align-items-end mb-4">
                        <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                        <input type="hidden" name="murid_id" value="{{ $selectedMuridId }}">
                        <input type="hidden" name="semester_id" value="{{ $selectedSemesterId }}">
                        
                        <div class="col-md-4">
                            <label for="rank_kelas_id" class="form-label small fw-600">Pilih Kelas</label>
                            <select name="rank_kelas_id" id="rank_kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $kel)
                                    <option value="{{ $kel->id }}" {{ $selectedRankKelasId == $kel->id ? 'selected' : '' }}>{{ $kel->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="rank_semester_id" class="form-label small fw-600">Pilih Semester</label>
                            <select name="rank_semester_id" id="rank_semester_id" class="form-select" required>
                                <option value="">-- Pilih Semester --</option>
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ $selectedRankSemesterId == $sem->id ? 'selected' : '' }}>
                                        Semester {{ $sem->semester_ke }} ({{ $sem->tahun_ajaran }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary-custom w-100">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Tampilkan
                            </button>
                            @if(!empty($rankingKelasList))
                                <a href="{{ route('admin.nilai.export.pdf.kelas', ['kelas_id' => $selectedRankKelasId, 'semester_id' => $selectedRankSemesterId]) }}" target="_blank" class="btn btn-outline-danger" title="Cetak PDF">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </a>
                                <a href="{{ route('admin.nilai.export.excel.kelas', ['kelas_id' => $selectedRankKelasId, 'semester_id' => $selectedRankSemesterId]) }}" class="btn btn-outline-success" title="Export Excel">
                                    <i class="fa-solid fa-file-excel"></i>
                                </a>
                            @endif
                        </div>
                    </form>

                    @if(!empty($rankingKelasList))
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="80px">Rank</th>
                                        <th class="d-none d-md-table-cell">NIS</th>
                                        <th>Nama Lengkap</th>
                                        <th>Total Nilai</th>
                                        <th>Rata-rata Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rankingKelasList as $row)
                                        <tr class="{{ $row->murid_id == $selectedMuridId ? 'table-primary' : '' }}">
                                            <td>
                                                @if($row->rank_kelas == 1)
                                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-trophy"></i> 1</span>
                                                @elseif($row->rank_kelas == 2)
                                                    <span class="badge bg-secondary"><i class="fa-solid fa-medal"></i> 2</span>
                                                @elseif($row->rank_kelas == 3)
                                                    <span class="badge bg-bronze" style="background-color: #cd7f32;"><i class="fa-solid fa-medal"></i> 3</span>
                                                @else
                                                    <span class="fw-bold px-2 text-muted">{{ $row->rank_kelas }}</span>
                                                @endif
                                            </td>
                                            <td class="d-none d-md-table-cell small fw-600 text-muted">{{ $row->nis }}</td>
                                            <td>
                                                <span class="fw-600 d-block">{{ $row->nama_lengkap }}</span>
                                                <span class="text-muted small d-block d-md-none">NIS: {{ $row->nis }}</span>
                                            </td>
                                            <td class="fw-600">{{ $row->total_nilai }}</td>
                                            <td class="fw-bold text-primary">{{ $row->avg_nilai }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <p class="m-0">Pilih Kelas dan Semester untuk memuat ranking kelas.</p>
                        </div>
                    @endif
                </div>

                <!-- PANEL RANKING PARALEL JURUSAN -->
                <div class="tab-pane fade" id="parallel-panel" role="tabpanel" aria-labelledby="parallel-tab">
                    <form action="{{ route('admin.nilai.index') }}" method="GET" class="row g-3 align-items-end mb-4">
                        <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                        <input type="hidden" name="murid_id" value="{{ $selectedMuridId }}">
                        <input type="hidden" name="semester_id" value="{{ $selectedSemesterId }}">
                        
                        <div class="col-md-3">
                            <label for="rank_jurusan_id" class="form-label small fw-600">Pilih Jurusan</label>
                            <select name="rank_jurusan_id" id="rank_jurusan_id" class="form-select" required>
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach($jurusans as $jur)
                                    <option value="{{ $jur->id }}" {{ $selectedRankJurusanId == $jur->id ? 'selected' : '' }}>{{ $jur->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="rank_tingkat" class="form-label small fw-600">Pilih Tingkat</label>
                            <select name="rank_tingkat" id="rank_tingkat" class="form-select" required>
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="X" {{ $selectedRankTingkat == 'X' ? 'selected' : '' }}>X (10)</option>
                                <option value="XI" {{ $selectedRankTingkat == 'XI' ? 'selected' : '' }}>XI (11)</option>
                                <option value="XII" {{ $selectedRankTingkat == 'XII' ? 'selected' : '' }}>XII (12)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="rank_sem_id" class="form-label small fw-600">Pilih Semester</label>
                            <select name="rank_sem_id" id="rank_sem_id" class="form-select" required>
                                <option value="">-- Pilih Semester --</option>
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ $selectedRankSemId == $sem->id ? 'selected' : '' }}>
                                        Semester {{ $sem->semester_ke }} ({{ $sem->tahun_ajaran }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary-custom w-100">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Tampilkan
                            </button>
                            @if(!empty($rankingJurusanList))
                                <a href="{{ route('admin.nilai.export.pdf.jurusan', ['jurusan_id' => $selectedRankJurusanId, 'tingkat' => $selectedRankTingkat, 'semester_id' => $selectedRankSemId]) }}" target="_blank" class="btn btn-outline-danger" title="Cetak PDF">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </a>
                                <a href="{{ route('admin.nilai.export.excel.jurusan', ['jurusan_id' => $selectedRankJurusanId, 'tingkat' => $selectedRankTingkat, 'semester_id' => $selectedRankSemId]) }}" class="btn btn-outline-success" title="Export Excel">
                                    <i class="fa-solid fa-file-excel"></i>
                                </a>
                            @endif
                        </div>
                    </form>

                    @if(!empty($rankingJurusanList))
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="80px">Rank</th>
                                        <th class="d-none d-md-table-cell">NIS</th>
                                        <th>Nama Lengkap</th>
                                        <th class="d-none d-md-table-cell">Kelas</th>
                                        <th>Total Nilai</th>
                                        <th>Rata-rata Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rankingJurusanList as $row)
                                        <tr class="{{ $row->murid_id == $selectedMuridId ? 'table-primary' : '' }}">
                                            <td>
                                                @if($row->rank_paralel == 1)
                                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-trophy"></i> 1</span>
                                                @elseif($row->rank_paralel == 2)
                                                    <span class="badge bg-secondary"><i class="fa-solid fa-medal"></i> 2</span>
                                                @elseif($row->rank_paralel == 3)
                                                    <span class="badge bg-bronze" style="background-color: #cd7f32;"><i class="fa-solid fa-medal"></i> 3</span>
                                                @else
                                                    <span class="fw-bold px-2 text-muted">{{ $row->rank_paralel }}</span>
                                                @endif
                                            </td>
                                            <td class="d-none d-md-table-cell small fw-600 text-muted">{{ $row->nis }}</td>
                                            <td>
                                                <span class="fw-600 d-block">{{ $row->nama_lengkap }}</span>
                                                <span class="text-muted small d-block d-md-none">NIS: {{ $row->nis }} | Kelas: {{ $row->nama_kelas }}</span>
                                            </td>
                                            <td class="d-none d-md-table-cell"><span class="badge bg-light text-dark border">{{ $row->nama_kelas }}</span></td>
                                            <td class="fw-600">{{ $row->total_nilai }}</td>
                                            <td class="fw-bold text-teal" style="color: var(--accent-color);">{{ $row->avg_nilai }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <p class="m-0">Pilih Jurusan, Tingkat dan Semester untuk memuat ranking paralel.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Input Grade Modal (Conditional) -->
@if($selectedMuridId && $selectedSemesterId)
    <div class="modal fade" id="inputGradeModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-700"><i class="fa-solid fa-plus me-2 text-primary"></i>Input Nilai Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.nilai.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="murid_id" value="{{ $selectedMuridId }}">
                    <input type="hidden" name="semester_id" value="{{ $selectedSemesterId }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="mata_pelajaran_id" class="form-label small fw-600">Mata Pelajaran</label>
                            <select name="mata_pelajaran_id" id="mata_pelajaran_id" class="form-select @error('mata_pelajaran_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Mapel --</option>
                                @foreach($mapels as $map)
                                    <option value="{{ $map->id }}" {{ old('mata_pelajaran_id') == $map->id ? 'selected' : '' }}>
                                        {{ $map->nama_mapel }} ({{ $map->kode_mapel }})
                                    </option>
                                @endforeach
                            </select>
                            @error('mata_pelajaran_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nilai" class="form-label small fw-600">Nilai (0.00 - 100.00)</label>
                            <input type="number" class="form-control @error('nilai') is-invalid @enderror" name="nilai" id="nilai" step="0.01" min="0" max="100" placeholder="e.g. 85.50" value="{{ old('nilai') }}" required>
                            @error('nilai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-custom">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
    function confirmDeleteGrade(id) {
        if (confirm('Apakah Anda yakin ingin menghapus nilai mata pelajaran ini?')) {
            document.getElementById(`delete-grade-form-${id}`).submit();
        }
    }

    // Restore selected tab on leaderboard reload
    $(document).ready(function() {
        // Cek parameter request untuk mengaktifkan tab yang sesuai
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('rank_jurusan_id')) {
            $('#rankingTabs button[data-bs-target="#parallel-panel"]').tab('show');
        } else if (urlParams.has('rank_kelas_id')) {
            $('#rankingTabs button[data-bs-target="#class-panel"]').tab('show');
        }
    });
</script>
@endsection

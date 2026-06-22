@extends('layouts.app')

@section('title', 'Seleksi Eligible SNBP')
@section('page_title', 'Manajemen Seleksi Eligible SNBP')

@section('content')
<div class="row g-4">
    <!-- Settings Column -->
    <div class="col-lg-4">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-700 mb-4"><i class="fa-solid fa-sliders text-primary me-2"></i>Konfigurasi SNBP</h5>
            
            <form action="{{ route('admin.snbp.settings') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="snbp_menu_status" class="form-label small fw-600">Status Menu di Halaman Murid</label>
                    <select name="snbp_menu_status" id="snbp_menu_status" class="form-select @error('snbp_menu_status') is-invalid @enderror" required>
                        <option value="aktif" {{ $settings['snbp_menu_status'] == 'aktif' ? 'selected' : '' }}>Tampilkan Menu</option>
                        <option value="nonaktif" {{ $settings['snbp_menu_status'] == 'nonaktif' ? 'selected' : '' }}>Sembunyikan Menu</option>
                    </select>
                    @error('snbp_menu_status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="snbp_deadline" class="form-label small fw-600">Batas Waktu Pendaftaran (Deadline)</label>
                    <input type="datetime-local" name="snbp_deadline" id="snbp_deadline" 
                           class="form-control @error('snbp_deadline') is-invalid @enderror" 
                           value="{{ $settings['snbp_deadline'] ? \Carbon\Carbon::parse($settings['snbp_deadline'])->format('Y-m-d\TH:i') : '' }}" required>
                    <div class="form-text text-muted small"><i class="fa-solid fa-circle-info me-1"></i>Zona Waktu Server: Asia/Jakarta</div>
                    @error('snbp_deadline')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary-custom w-100">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Konfigurasi
                </button>
            </form>
        </div>
    </div>

    <!-- Jurusan Column -->
    <div class="col-lg-8">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-700 mb-4"><i class="fa-solid fa-award text-success me-2"></i>Kuota Eligible Per Jurusan</h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Jurusan</th>
                            <th class="text-center">Kuota Eligible</th>
                            <th class="text-center">Jumlah Pendaftar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jurusans as $j)
                            <tr class="{{ $selectedJurusanId == $j->id ? 'table-primary-light' : '' }}">
                                <td><span class="fw-bold">{{ $j->kode_jurusan }}</span></td>
                                <td>{{ $j->nama_jurusan }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info px-3 py-2 fs-6" style="border-radius: 8px;">{{ $j->kuota_snbp }} Siswa</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary px-3 py-2 fs-6" style="border-radius: 8px;">{{ $j->pendaftar_count }} Pendaftar</span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-outline-secondary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editQuotaModal"
                                                data-id="{{ $j->id }}"
                                                data-nama="{{ $j->nama_jurusan }}"
                                                data-kuota="{{ $j->kuota_snbp }}"
                                                title="Ubah Kuota Eligible">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <a href="{{ route('admin.snbp.index', ['jurusan_id' => $j->id]) }}" 
                                           class="btn btn-outline-primary btn-sm"
                                           title="Lihat Rangking Pendaftar">
                                            <i class="fa-solid fa-list-ol"></i> Lihat Hasil
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada data jurusan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Leaderboard Section -->
@if($selectedJurusan)
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="glass-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h5 class="fw-700 m-0"><i class="fa-solid fa-trophy text-warning me-2"></i>Peringkat Pendaftar SNBP: {{ $selectedJurusan->nama_jurusan }}</h5>
                    <span class="text-muted small">Kuota Eligible Jurusan: <strong>{{ $selectedJurusan->kuota_snbp }} Siswa</strong></span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle datatable-custom">
                    <thead>
                        <tr>
                            <th width="80px">Peringkat</th>
                            <th>NIS</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th class="text-center">Rata-Rata Nilai (Smt 1-5)</th>
                            <th class="text-center" width="180px">Status Kelolosan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rankingList as $index => $rank)
                            @php
                                $isEligible = $rank->rank_snbp <= $selectedJurusan->kuota_snbp;
                            @endphp
                            <tr class="{{ $isEligible ? 'table-success-light' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center justify-content-center fw-bold rounded-circle border bg-white" 
                                         style="width: 32px; height: 32px; color: var(--text-light);">
                                        {{ $rank->rank_snbp }}
                                    </div>
                                </td>
                                <td><span class="fw-600">{{ $rank->nis }}</span></td>
                                <td><span class="fw-600">{{ $rank->nama_lengkap }}</span></td>
                                <td>{{ $rank->nama_kelas }}</td>
                                <td class="text-center"><span class="badge bg-primary-subtle text-primary px-3 py-2 fs-6 font-bold">{{ number_format($rank->avg_nilai, 2) }}</span></td>
                                <td class="text-center">
                                    @if($isEligible)
                                        <span class="badge bg-success px-3 py-2 w-100"><i class="fa-solid fa-circle-check me-1"></i> Eligible</span>
                                    @else
                                        <span class="badge bg-secondary px-3 py-2 w-100"><i class="fa-solid fa-circle-minus me-1"></i> Cadangan</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Edit Quota Modal -->
<div class="modal fade" id="editQuotaModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-700"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Ubah Kuota Eligible SNBP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.snbp.quota') }}" method="POST">
                @csrf
                <input type="hidden" name="jurusan_id" id="edit_quota_jurusan_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-600">Nama Jurusan</label>
                        <input type="text" id="edit_quota_jurusan_name" class="form-control" readonly style="background-color: rgba(0,0,0,0.03);">
                    </div>
                    <div class="mb-3">
                        <label for="edit_kuota_snbp" class="form-label small fw-600">Kuota Eligible SNBP (Siswa)</label>
                        <input type="number" name="kuota_snbp" id="edit_kuota_snbp" class="form-control" min="0" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('#editQuotaModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nama = button.data('nama');
        var kuota = button.data('kuota');
        
        var modal = $(this);
        modal.find('#edit_quota_jurusan_id').val(id);
        modal.find('#edit_quota_jurusan_name').val(nama);
        modal.find('#edit_kuota_snbp').val(kuota);
    });
</script>
@endsection

@section('styles')
<style>
    .table-primary-light {
        background-color: rgba(37, 99, 235, 0.08) !important;
    }
    .table-success-light {
        background-color: rgba(22, 163, 74, 0.08) !important;
    }
    [data-bs-theme="dark"] .table-primary-light {
        background-color: rgba(59, 130, 246, 0.15) !important;
    }
    [data-bs-theme="dark"] .table-success-light {
        background-color: rgba(34, 197, 94, 0.15) !important;
    }
</style>
@endsection

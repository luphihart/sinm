@extends('layouts.app')

@section('title', 'Seleksi Eligible SNBP')
@section('page_title', 'Manajemen Seleksi Eligible SNBP')

@section('content')
<div class="row g-3 g-lg-4">
    {{-- Settings Column --}}
    <div class="col-12 col-lg-4">
        <div class="glass-card p-3 p-lg-4 h-100">
            <h5 class="fw-700 mb-3"><i class="fa-solid fa-sliders text-primary me-2"></i>Konfigurasi SNBP</h5>

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

                <div class="mb-3">
                    <label for="snbp_deadline" class="form-label small fw-600">Batas Waktu Pendaftaran</label>
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

    {{-- Jurusan Column --}}
    <div class="col-12 col-lg-8">
        <div class="glass-card p-3 p-lg-4 h-100">
            <h5 class="fw-700 mb-3"><i class="fa-solid fa-award text-success me-2"></i>Kuota Eligible Per Jurusan</h5>

            {{-- Desktop Table --}}
            <div class="d-none d-md-block table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Jurusan</th>
                            <th class="text-center">Kuota</th>
                            <th class="text-center">Pendaftar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jurusans as $j)
                            <tr class="{{ $selectedJurusanId == $j->id ? 'table-primary-light' : '' }}">
                                <td><span class="fw-bold">{{ $j->kode_jurusan }}</span></td>
                                <td>{{ $j->nama_jurusan }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info px-3 py-2" style="border-radius: 8px;">{{ $j->kuota_snbp }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary px-3 py-2" style="border-radius: 8px;">{{ $j->pendaftar_count }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-outline-secondary btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editQuotaModal"
                                                data-id="{{ $j->id }}"
                                                data-nama="{{ $j->nama_jurusan }}"
                                                data-kuota="{{ $j->kuota_snbp }}"
                                                title="Ubah Kuota">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <a href="{{ route('admin.snbp.index', ['jurusan_id' => $j->id]) }}"
                                           class="btn btn-outline-primary btn-sm"
                                           title="Lihat Rangking Pendaftar">
                                            <i class="fa-solid fa-list-ol"></i> Lihat
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">Belum ada data jurusan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card List --}}
            <div class="d-md-none">
                @forelse($jurusans as $j)
                    <div class="snbp-jurusan-card {{ $selectedJurusanId == $j->id ? 'snbp-jurusan-active' : '' }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="fw-bold text-primary">{{ $j->kode_jurusan }}</span>
                                <span class="d-block fw-600" style="font-size: 0.9rem;">{{ $j->nama_jurusan }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-info-subtle text-info px-2 py-1" style="font-size: 0.7rem;">Kuota: {{ $j->kuota_snbp }}</span>
                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="font-size: 0.7rem;">Pendaftar: {{ $j->pendaftar_count }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm flex-fill"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editQuotaModal"
                                    data-id="{{ $j->id }}"
                                    data-nama="{{ $j->nama_jurusan }}"
                                    data-kuota="{{ $j->kuota_snbp }}">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Kuota
                            </button>
                            <a href="{{ route('admin.snbp.index', ['jurusan_id' => $j->id]) }}"
                               class="btn btn-outline-primary btn-sm flex-fill">
                                <i class="fa-solid fa-list-ol me-1"></i> Lihat Hasil
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-3">Belum ada data jurusan.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Leaderboard Section --}}
@if($selectedJurusan)
<div class="row g-3 g-lg-4 mt-1">
    <div class="col-12">
        <div class="glass-card p-3 p-lg-4">
            <div class="mb-3">
                <h5 class="fw-700 m-0"><i class="fa-solid fa-trophy text-warning me-2"></i>Peringkat: {{ $selectedJurusan->nama_jurusan }}</h5>
                <span class="text-muted small">Kuota Eligible: <strong>{{ $selectedJurusan->kuota_snbp }} Siswa</strong></span>
            </div>

            {{-- Desktop Table --}}
            <div class="d-none d-md-block table-responsive">
                <table class="table table-hover align-middle datatable-custom mb-0">
                    <thead>
                        <tr>
                            <th width="70">Peringkat</th>
                            <th>NIS</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th class="text-center">Rata-Rata (Smt 1-5)</th>
                            <th class="text-center" width="150">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rankingList as $index => $rank)
                            @php $isEligible = $rank->rank_snbp <= $selectedJurusan->kuota_snbp; @endphp
                            <tr class="{{ $isEligible ? 'table-success-light' : '' }}">
                                <td>
                                    <div class="snbp-rank-circle">{{ $rank->rank_snbp }}</div>
                                </td>
                                <td><span class="fw-600">{{ $rank->nis }}</span></td>
                                <td><span class="fw-600">{{ $rank->nama_lengkap }}</span></td>
                                <td>{{ $rank->nama_kelas }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2">{{ number_format($rank->avg_nilai, 2) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($isEligible)
                                        <span class="badge bg-success px-2 py-2 w-100"><i class="fa-solid fa-circle-check me-1"></i>Eligible</span>
                                    @else
                                        <span class="badge bg-secondary px-2 py-2 w-100"><i class="fa-solid fa-circle-minus me-1"></i>Cadangan</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card List --}}
            <div class="d-md-none">
                @forelse($rankingList as $rank)
                    @php $isEligible = $rank->rank_snbp <= $selectedJurusan->kuota_snbp; @endphp
                    <div class="snbp-mobile-card {{ $isEligible ? 'snbp-mobile-eligible' : '' }}">
                        <div class="d-flex align-items-center gap-2">
                            <div class="snbp-rank-circle" style="flex-shrink: 0;">{{ $rank->rank_snbp }}</div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="fw-600 text-truncate">{{ $rank->nama_lengkap }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">NIS: {{ $rank->nis }} · {{ $rank->nama_kelas }}</div>
                            </div>
                            <div class="text-end" style="flex-shrink: 0;">
                                <div class="fw-bold" style="font-size: 0.95rem;">{{ number_format($rank->avg_nilai, 2) }}</div>
                                @if($isEligible)
                                    <span class="badge bg-success" style="font-size: 0.6rem;">Eligible</span>
                                @else
                                    <span class="badge bg-secondary" style="font-size: 0.6rem;">Cadangan</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">Belum ada pendaftar.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endif

{{-- Edit Quota Modal --}}
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
        var modal = $(this);
        modal.find('#edit_quota_jurusan_id').val(button.data('id'));
        modal.find('#edit_quota_jurusan_name').val(button.data('nama'));
        modal.find('#edit_kuota_snbp').val(button.data('kuota'));
    });
</script>
@endsection

@section('styles')
<style>
    .snbp-rank-circle {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        border-radius: 50%;
        border: 2px solid #dee2e6;
        background: #fff;
        color: var(--text-light, #64748b);
        font-size: 0.85rem;
    }
    .table-primary-light {
        background-color: rgba(37, 99, 235, 0.08) !important;
    }
    .table-success-light {
        background-color: rgba(22, 163, 74, 0.08) !important;
    }
    /* Mobile jurusan card */
    .snbp-jurusan-card {
        padding: 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        margin-bottom: 0.5rem;
        background: #fff;
    }
    .snbp-jurusan-active {
        border-left: 4px solid var(--primary-color, #2563eb) !important;
        background: rgba(37,99,235,0.04) !important;
    }
    /* Mobile ranking card */
    .snbp-mobile-card {
        padding: 0.75rem;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        margin-bottom: 0.5rem;
        background: #fff;
    }
    .snbp-mobile-eligible {
        background: rgba(22,163,74,0.04);
    }
    .min-width-0 { min-width: 0; }

    /* Dark mode */
    [data-bs-theme="dark"] .table-primary-light { background-color: rgba(59, 130, 246, 0.15) !important; }
    [data-bs-theme="dark"] .table-success-light { background-color: rgba(34, 197, 94, 0.15) !important; }
    [data-bs-theme="dark"] .snbp-rank-circle { background: #1e293b; border-color: #475569; }
    [data-bs-theme="dark"] .snbp-jurusan-card { background: #1e293b; border-color: #334155; }
    [data-bs-theme="dark"] .snbp-jurusan-active { background: rgba(59,130,246,0.1) !important; border-left-color: #3b82f6 !important; }
    [data-bs-theme="dark"] .snbp-mobile-card { background: #1e293b; border-color: #334155; }
    [data-bs-theme="dark"] .snbp-mobile-eligible { background: rgba(34,197,94,0.08); }
</style>
@endsection

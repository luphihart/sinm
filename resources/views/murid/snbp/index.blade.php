@extends('layouts.app')

@section('title', 'Seleksi Eligible SNBP')
@section('page_title', 'Seleksi Eligible SNBP')

@section('content')
<div class="row g-3 g-lg-4">
    {{-- Status Card --}}
    <div class="col-12 col-lg-5">
        <div class="glass-card p-3 p-lg-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-700 mb-3"><i class="fa-solid fa-user-tag text-primary me-2"></i>Status Keikutsertaan Anda</h5>
                <p class="text-muted small mb-3">Pendaftaran seleksi ini bersifat opsional. Siswa eligible akan dirangking secara otomatis berdasarkan nilai Semester 1 s.d. 5.</p>

                <div class="mb-3 text-center">
                    @if($isRegistered)
                        <div class="p-3 rounded-4 bg-success-subtle text-success border border-success border-opacity-25">
                            <i class="fa-solid fa-circle-check fs-2 mb-1"></i>
                            <h6 class="fw-bold m-0">Terdaftar dalam Seleksi</h6>
                            <span class="small opacity-75">Nama Anda aktif dalam perhitungan pemeringkatan.</span>
                        </div>
                    @else
                        <div class="p-3 rounded-4 bg-secondary-subtle text-secondary border border-secondary border-opacity-25">
                            <i class="fa-solid fa-circle-minus fs-2 mb-1"></i>
                            <h6 class="fw-bold m-0">Belum Terdaftar</h6>
                            <span class="small opacity-75">Nama Anda tidak diikutkan dalam perhitungan eligible.</span>
                        </div>
                    @endif
                </div>

                {{-- Countdown --}}
                <div class="p-3 rounded-3 bg-light border mb-3">
                    <span class="d-block small text-muted fw-semibold"><i class="fa-solid fa-calendar me-1"></i> Batas Waktu Pendaftaran:</span>
                    <span class="fw-bold text-dark">{{ $deadline ? $deadline->translatedFormat('d F Y - H:i') : '-' }} WIB</span>

                    <span class="d-block mt-2 small text-muted fw-semibold"><i class="fa-solid fa-stopwatch me-1"></i> Hitung Mundur:</span>
                    @if($isExpired)
                        <span class="fw-bold text-danger"><i class="fa-solid fa-lock me-1"></i> Pendaftaran telah ditutup.</span>
                    @else
                        <span class="fw-bold text-warning" id="countdown-timer">Memuat...</span>
                    @endif
                </div>
            </div>

            <div>
                @if(!$isExpired)
                    @if($isRegistered)
                        <form action="{{ route('murid.snbp.batal') }}" method="POST"
                              onclick="return confirm('Apakah Anda yakin ingin mengundurkan diri / membatalkan pendaftaran Seleksi SNBP?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100 py-2 rounded-3 fw-600">
                                <i class="fa-solid fa-circle-xmark me-2"></i> Batalkan / Mundur
                            </button>
                        </form>
                    @else
                        <form action="{{ route('murid.snbp.daftar') }}" method="POST"
                              onclick="return confirm('Apakah Anda yakin ingin mendaftarkan diri dalam seleksi Eligible SNBP?')">
                            @csrf
                            <button type="submit" class="btn btn-primary-custom w-100 py-2 rounded-3 fw-600">
                                <i class="fa-solid fa-paper-plane me-2"></i> Daftar Seleksi SNBP
                            </button>
                        </form>
                    @endif
                @else
                    <button class="btn btn-secondary w-100 py-2 rounded-3 fw-600" disabled>
                        <i class="fa-solid fa-lock me-2"></i> Aksi Dikunci
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="col-12 col-lg-7">
        <div class="glass-card p-3 p-lg-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-700 mb-3"><i class="fa-solid fa-circle-info text-info me-2"></i>Ketentuan & Informasi</h5>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 p-lg-3 border rounded-3 text-center">
                            <span class="d-block text-muted" style="font-size: 0.72rem;">Kompetensi Keahlian</span>
                            <span class="fw-bold text-primary" style="font-size: 1.1rem;">{{ $jurusan->kode_jurusan }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 p-lg-3 border rounded-3 text-center">
                            <span class="d-block text-muted" style="font-size: 0.72rem;">Kuota Eligible</span>
                            <span class="fw-bold text-success" style="font-size: 1.1rem;">{{ $kuota }} Siswa</span>
                        </div>
                    </div>
                </div>

                <div class="small text-muted" style="line-height: 1.6;">
                    <h6 class="fw-bold text-dark mb-2" style="font-size: 0.85rem;">Aturan Seleksi Eligible Sekolah:</h6>
                    <ul class="ps-3 mb-0">
                        <li class="mb-1">Hanya siswa yang berstatus <strong>"Terdaftar"</strong> yang akan dirangking oleh sistem.</li>
                        <li class="mb-1">Pemeringkatan didasarkan pada <strong>rata-rata nilai rapor Semester 1 s.d. 5</strong>.</li>
                        <li class="mb-1">Peringkat <strong>1 s.d. {{ $kuota }}</strong> otomatis diklasifikasikan <strong>Eligible</strong>.</li>
                        <li class="mb-1">Peringkat {{ $kuota + 1 }}+ diklasifikasikan <strong>Cadangan</strong>. Jika ada yang mundur, peringkat di bawahnya otomatis naik.</li>
                    </ul>
                </div>
            </div>

            <div class="mt-3 pt-2 border-top">
                <span class="small text-muted d-block"><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Keputusan akhir merupakan wewenang penuh pihak Sekolah sesuai regulasi SNPMB resmi.</span>
            </div>
        </div>
    </div>
</div>

{{-- Leaderboard Section --}}
<div class="row g-3 g-lg-4 mt-1">
    <div class="col-12">
        <div class="glass-card p-3 p-lg-4">
            <h5 class="fw-700 mb-3"><i class="fa-solid fa-trophy text-warning me-2"></i>Peta Persaingan — {{ $jurusan->nama_jurusan }}</h5>

            {{-- Desktop Table --}}
            <div class="d-none d-md-block table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="70" class="text-center">Peringkat</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th class="text-center">Rata-Rata (Smt 1-5)</th>
                            <th class="text-center" width="150">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rankingList as $rank)
                            @php
                                $isSelf = $rank->murid_id == $murid->id;
                                $isEligible = $rank->rank_snbp <= $kuota;
                            @endphp
                            <tr class="{{ $isSelf ? 'table-self-highlight' : ($isEligible ? 'table-success-light' : '') }}">
                                <td class="text-center">
                                    <div class="snbp-rank-circle {{ $isSelf ? 'snbp-rank-self' : '' }}">{{ $rank->rank_snbp }}</div>
                                </td>
                                <td>
                                    <span class="fw-600 {{ $isSelf ? 'text-primary' : '' }}">
                                        {{ $rank->nama_lengkap }}
                                        @if($isSelf) <span class="badge bg-primary ms-1" style="font-size: 0.65rem; padding: 2px 5px;">Anda</span> @endif
                                    </span>
                                </td>
                                <td>{{ $rank->nama_kelas }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $isSelf ? 'bg-primary' : 'bg-primary-subtle text-primary' }} px-3 py-2">{{ number_format($rank->avg_nilai, 2) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($isEligible)
                                        <span class="badge bg-success px-2 py-2 w-100"><i class="fa-solid fa-circle-check me-1"></i>Eligible</span>
                                    @else
                                        <span class="badge bg-secondary px-2 py-2 w-100"><i class="fa-solid fa-circle-minus me-1"></i>Cadangan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">Belum ada murid yang mendaftar di jurusan ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card List --}}
            <div class="d-md-none">
                @forelse($rankingList as $rank)
                    @php
                        $isSelf = $rank->murid_id == $murid->id;
                        $isEligible = $rank->rank_snbp <= $kuota;
                    @endphp
                    <div class="snbp-mobile-card {{ $isSelf ? 'snbp-mobile-self' : '' }} {{ $isEligible ? 'snbp-mobile-eligible' : '' }}">
                        <div class="d-flex align-items-center gap-2">
                            <div class="snbp-rank-circle {{ $isSelf ? 'snbp-rank-self' : '' }}" style="flex-shrink: 0;">{{ $rank->rank_snbp }}</div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="fw-600 text-truncate {{ $isSelf ? 'text-primary' : '' }}">
                                    {{ $rank->nama_lengkap }}
                                    @if($isSelf) <span class="badge bg-primary" style="font-size: 0.6rem; padding: 1px 4px;">Anda</span> @endif
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $rank->nama_kelas }}</div>
                            </div>
                            <div class="text-end" style="flex-shrink: 0;">
                                <div class="fw-bold {{ $isSelf ? 'text-primary' : '' }}" style="font-size: 0.95rem;">{{ number_format($rank->avg_nilai, 2) }}</div>
                                @if($isEligible)
                                    <span class="badge bg-success" style="font-size: 0.6rem;">Eligible</span>
                                @else
                                    <span class="badge bg-secondary" style="font-size: 0.6rem;">Cadangan</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">Belum ada murid yang mendaftar di jurusan ini.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    @if($deadline && !$isExpired)
    (function() {
        var deadlineTime = new Date("{{ $deadline->toIso8601String() }}").getTime();
        var timerElement = document.getElementById("countdown-timer");

        var interval = setInterval(function() {
            var now = new Date().getTime();
            var distance = deadlineTime - now;

            if (distance < 0) {
                clearInterval(interval);
                timerElement.innerHTML = "Batas waktu pendaftaran telah berakhir.";
                setTimeout(function() { location.reload(); }, 1000);
                return;
            }

            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            timerElement.innerHTML = days + "h " + hours + "j " + minutes + "m " + seconds + "d";
        }, 1000);
    })();
    @endif
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
    .snbp-rank-self {
        border-color: var(--primary-color, #2563eb) !important;
        color: var(--primary-color, #2563eb) !important;
        box-shadow: 0 0 6px rgba(37,99,235,0.25);
    }
    .table-success-light {
        background-color: rgba(22, 163, 74, 0.05) !important;
    }
    .table-self-highlight {
        background-color: rgba(37, 99, 235, 0.08) !important;
        border-left: 4px solid var(--primary-color) !important;
    }
    /* Mobile card list */
    .snbp-mobile-card {
        padding: 0.75rem;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        margin-bottom: 0.5rem;
        background: #fff;
    }
    .snbp-mobile-self {
        border-left: 4px solid var(--primary-color, #2563eb) !important;
        background: rgba(37,99,235,0.05) !important;
    }
    .snbp-mobile-eligible {
        background: rgba(22,163,74,0.04);
    }
    .snbp-mobile-self.snbp-mobile-eligible {
        background: rgba(37,99,235,0.05) !important;
    }
    .min-width-0 { min-width: 0; }

    /* Dark mode */
    [data-bs-theme="dark"] .snbp-rank-circle { background: #1e293b; border-color: #475569; }
    [data-bs-theme="dark"] .snbp-mobile-card { background: #1e293b; border-color: #334155; }
    [data-bs-theme="dark"] .snbp-mobile-self { background: rgba(59,130,246,0.12) !important; border-left-color: #3b82f6 !important; }
    [data-bs-theme="dark"] .snbp-mobile-eligible { background: rgba(34,197,94,0.08); }
    [data-bs-theme="dark"] .table-success-light { background-color: rgba(34, 197, 94, 0.1) !important; }
    [data-bs-theme="dark"] .table-self-highlight { background-color: rgba(59, 130, 246, 0.15) !important; border-left: 4px solid #3b82f6 !important; }
    [data-bs-theme="dark"] .bg-light { background-color: rgba(255,255,255,0.05) !important; }
</style>
@endsection

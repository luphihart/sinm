@extends('layouts.app')

@section('title', 'Seleksi Eligible SNBP')
@section('page_title', 'Seleksi Eligible SNBP')

@section('content')
<div class="row g-4">
    <!-- Status Card -->
    <div class="col-lg-5">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-700 mb-3"><i class="fa-solid fa-user-tag text-primary me-2"></i>Status Keikutsertaan Anda</h5>
                <p class="text-muted small">Pendaftaran seleksi ini bersifat opsional. Siswa eligible akan dirangking secara otomatis berdasarkan nilai Semester 1 s.d. 5.</p>
                
                <div class="my-4 text-center">
                    @if($isRegistered)
                        <div class="p-3 rounded-4 bg-success-subtle text-success border border-success border-opacity-25 mb-3">
                            <i class="fa-solid fa-circle-check fs-1 mb-2"></i>
                            <h5 class="fw-bold m-0">Terdaftar dalam Seleksi</h5>
                            <span class="small opacity-75">Nama Anda aktif dalam perhitungan pemeringkatan.</span>
                        </div>
                    @else
                        <div class="p-3 rounded-4 bg-secondary-subtle text-secondary border border-secondary border-opacity-25 mb-3">
                            <i class="fa-solid fa-circle-minus fs-1 mb-2"></i>
                            <h5 class="fw-bold m-0">Belum Terdaftar</h5>
                            <span class="small opacity-75">Nama Anda tidak diikutkan dalam perhitungan eligible.</span>
                        </div>
                    @endif
                </div>

                <!-- Info Batas Waktu & Countdown -->
                <div class="p-3 rounded-3 bg-light border mb-4">
                    <span class="d-block small text-muted font-semibold"><i class="fa-solid fa-calendar me-1"></i> Batas Waktu Pendaftaran:</span>
                    <span class="fw-bold text-dark">{{ $deadline ? $deadline->translatedFormat('d F Y - H:i') : '-' }} WIB</span>
                    
                    <span class="d-block mt-3 small text-muted font-semibold"><i class="fa-solid fa-stopwatch me-1"></i> Hitung Mundur:</span>
                    @if($isExpired)
                        <span class="fw-bold text-danger"><i class="fa-solid fa-lock me-1"></i> Pendaftaran telah ditutup.</span>
                    @else
                        <span class="fw-bold text-warning fs-5" id="countdown-timer">Memuat...</span>
                    @endif
                </div>
            </div>

            <div>
                @if(!$isExpired)
                    @if($isRegistered)
                        <form action="{{ route('murid.snbp.batal') }}" method="POST" 
                              onclick="return confirm('Apakah Anda yakin ingin mengundurkan diri / membatalkan pendaftaran Seleksi SNBP?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100 py-2.5 rounded-3 fw-600">
                                <i class="fa-solid fa-circle-xmark me-2"></i> Batalkan Pendaftaran / Mundur
                            </button>
                        </form>
                    @else
                        <form action="{{ route('murid.snbp.daftar') }}" method="POST" 
                              onclick="return confirm('Apakah Anda yakin ingin mendaftarkan diri dalam seleksi Eligible SNBP?')">
                            @csrf
                            <button type="submit" class="btn btn-primary-custom w-100 py-2.5 rounded-3 fw-600">
                                <i class="fa-solid fa-paper-plane me-2"></i> Daftar Seleksi SNBP
                            </button>
                        </form>
                    @endif
                @else
                    <button class="btn btn-secondary w-100 py-2.5 rounded-3 fw-600" disabled>
                        <i class="fa-solid fa-lock me-2"></i> Aksi Dikunci (Batas Waktu Habis)
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Jurusan Info Card -->
    <div class="col-lg-7">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-700 mb-3"><i class="fa-solid fa-circle-info text-info me-2"></i>Ketentuan & Informasi</h5>
                <div class="mb-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 border rounded-3 text-center">
                                <span class="d-block text-muted small">Kompetensi Keahlian</span>
                                <span class="fw-bold text-primary fs-5">{{ $jurusan->kode_jurusan }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded-3 text-center">
                                <span class="d-block text-muted small">Kuota Eligible Jurusan</span>
                                <span class="fw-bold text-success fs-5">{{ $kuota }} Siswa</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="small text-muted" style="line-height: 1.6;">
                    <h6 class="fw-bold text-dark mb-2">Aturan Seleksi Eligible Sekolah:</h6>
                    <ul class="ps-3 mb-0">
                        <li class="mb-2">Hanya siswa yang berstatus **"Terdaftar"** secara mandiri yang akan dirangking oleh sistem.</li>
                        <li class="mb-2">Pemeringkatan didasarkan pada **rata-rata nilai rapor seluruh mata pelajaran Semester 1 s.d. Semester 5**.</li>
                        <li class="mb-2">Siswa dengan peringkat paralel jurusan **1 sampai {{ $kuota }}** secara otomatis diklasifikasikan sebagai **Eligible** untuk SNBP.</li>
                        <li class="mb-2">Siswa dengan peringkat di luar kuota (peringkat {{ $kuota + 1 }} ke bawah) diklasifikasikan sebagai **Cadangan**. Jika ada siswa eligible yang mundur sebelum deadline, peringkat di bawahnya akan otomatis naik mengisi kekosongan kuota.</li>
                    </ul>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top">
                <span class="small text-muted d-block"><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Keputusan penentuan akhir merupakan wewenang penuh pihak Sekolah sesuai regulasi SNPMB resmi.</span>
            </div>
        </div>
    </div>
</div>

<!-- Leaderboard Section -->
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="glass-card p-4">
            <h5 class="fw-700 mb-4"><i class="fa-solid fa-trophy text-warning me-2"></i>Peta Persaingan Sementara - Jurusan {{ $jurusan->nama_jurusan }}</h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="80px" class="text-center">Peringkat</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th class="text-center">Rata-Rata Nilai (Smt 1-5)</th>
                            <th class="text-center" width="180px">Status Kelolosan</th>
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
                                    <div class="d-flex align-items-center justify-content-center fw-bold rounded-circle border mx-auto bg-white" 
                                         style="width: 32px; height: 32px; color: var(--text-light); {{ $isSelf ? 'border-color: var(--primary-color) !important; color: var(--primary-color) !important; box-shadow: 0 0 5px rgba(37,99,235,0.3);' : '' }}">
                                        {{ $rank->rank_snbp }}
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-600 {{ $isSelf ? 'text-primary fs-6 font-bold' : '' }}">
                                        {{ $rank->nama_lengkap }}
                                        @if($isSelf) <span class="badge bg-primary ms-2" style="font-size: 0.7rem; padding: 2px 6px;">Anda</span> @endif
                                    </span>
                                </td>
                                <td>{{ $rank->nama_kelas }}</td>
                                <td class="text-center"><span class="badge {{ $isSelf ? 'bg-primary' : 'bg-primary-subtle text-primary' }} px-3 py-2 fs-6 font-bold">{{ number_format($rank->avg_nilai, 2) }}</span></td>
                                <td class="text-center">
                                    @if($isEligible)
                                        <span class="badge bg-success px-3 py-2 w-100"><i class="fa-solid fa-circle-check me-1"></i> Eligible</span>
                                    @else
                                        <span class="badge bg-secondary px-3 py-2 w-100"><i class="fa-solid fa-circle-minus me-1"></i> Cadangan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada murid yang mendaftar di jurusan ini.</td>
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
                setTimeout(function() {
                    location.reload();
                }, 1000);
                return;
            }
            
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            timerElement.innerHTML = days + " hari " + hours + " jam " + minutes + " menit " + seconds + " detik";
        }, 1000);
    })();
    @endif
</script>
@endsection

@section('styles')
<style>
    .table-success-light {
        background-color: rgba(22, 163, 74, 0.05) !important;
    }
    .table-self-highlight {
        background-color: rgba(37, 99, 235, 0.08) !important;
        border-left: 4px solid var(--primary-color) !important;
    }
    [data-bs-theme="dark"] .table-success-light {
        background-color: rgba(34, 197, 94, 0.1) !important;
    }
    [data-bs-theme="dark"] .table-self-highlight {
        background-color: rgba(59, 130, 246, 0.15) !important;
        border-left: 4px solid #3b82f6 !important;
    }
</style>
@endsection

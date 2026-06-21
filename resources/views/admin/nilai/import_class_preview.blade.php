@extends('layouts.app')

@section('title', 'Preview Import Nilai Kelas')
@section('page_title', 'Preview Import Nilai Satu Kelas')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="glass-card p-4">
            <h5 class="fw-700 mb-3"><i class="fa-solid fa-file-invoice me-2 text-primary"></i>Preview Data Import Nilai Satu Kelas</h5>
            <p class="text-muted small mb-4">Mohon periksa data nilai di bawah ini sebelum disimpan ke database. Jika terdapat baris berwarna merah, proses import tidak dapat dilanjutkan.</p>

            @if(!$isValidFile)
                <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center" role="alert" style="border-radius: 12px;">
                    <i class="fa-solid fa-triangle-exclamation fs-4 me-3"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1">Terjadi Kesalahan Validasi Data!</h6>
                        <span>Beberapa data nilai tidak valid atau tidak memenuhi syarat (nilai bukan angka, di luar rentang 0-100, atau siswa tidak ditemukan). Perbaiki file Excel Anda dan upload kembali.</span>
                    </div>
                </div>
            @else
                <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center" role="alert" style="border-radius: 12px;">
                    <i class="fa-solid fa-circle-check fs-4 me-3"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1">Semua Data Valid!</h6>
                        <span>Seluruh nilai siswa di kelas ini valid dan siap diimport ke database. Nilai lama siswa pada mapel yang bersangkutan akan diperbarui secara otomatis.</span>
                    </div>
                </div>
            @endif

            <div class="table-responsive mb-4" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-hover align-middle table-bordered">
                    <thead class="sticky-top bg-light">
                        <tr>
                            <th width="80px">Baris</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Semester</th>
                            <th>Nilai Mapel</th>
                            <th>Status Validasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previewData as $item)
                            <tr class="{{ $item['is_valid'] ? '' : 'table-danger' }}">
                                <td class="fw-bold">{{ $item['row_number'] }}</td>
                                <td class="small fw-600 text-muted">{{ $item['nis'] }}</td>
                                <td class="fw-600">{{ $item['nama_lengkap'] }}</td>
                                <td>Semester {{ $item['semester_ke'] }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        @forelse($item['grades'] as $grade)
                                            <span class="badge bg-primary-subtle text-primary border px-2 py-1">
                                                <strong>{{ $grade['kode_mapel'] }}</strong>: {{ $grade['nilai'] }}
                                            </span>
                                        @empty
                                            <span class="text-muted small italic">Tidak ada nilai diinput</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    @if($item['is_valid'])
                                        <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> OK</span>
                                    @else
                                        <div class="text-danger small">
                                            @foreach($item['errors'] as $err)
                                                <div class="d-flex align-items-start mb-1">
                                                    <i class="fa-solid fa-circle-xmark me-1 mt-1" style="font-size: 0.8rem;"></i>
                                                    <span>{{ $err }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Form Aksi Konfirmasi -->
            <form action="{{ route('admin.nilai.import-class.confirm') }}" method="POST">
                @csrf
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" name="cancel" value="1" class="btn btn-light">
                        <i class="fa-solid fa-ban me-1"></i> Batal & Kembali
                    </button>
                    <button type="submit" name="import" value="1" class="btn btn-primary-custom" {{ $isValidFile ? '' : 'disabled' }}>
                        <i class="fa-solid fa-circle-check me-1"></i> Konfirmasi & Simpan ke Database
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

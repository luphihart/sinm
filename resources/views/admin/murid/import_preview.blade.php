@extends('layouts.app')

@section('title', 'Preview Import Murid')
@section('page_title', 'Preview Import Murid Excel')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="glass-card p-4">
            <h5 class="fw-700 mb-3"><i class="fa-solid fa-file-invoice me-2 text-primary"></i>Preview Data Import Murid</h5>
            <p class="text-muted small mb-4">Mohon periksa data di bawah ini sebelum dimasukkan ke database. Akun login murid akan otomatis dibuat dengan username NIS dan password default NIS murid. Jika terdapat baris berwarna merah, proses import tidak dapat dilanjutkan.</p>

            @if(!$isValidFile)
                <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center" role="alert" style="border-radius: 12px;">
                    <i class="fa-solid fa-triangle-exclamation fs-4 me-3"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1">Terjadi Kesalahan Validasi Data!</h6>
                        <span>Beberapa data tidak sesuai dengan record di sistem (NIS/NISN ganda, kelas tidak terdaftar, atau data wajib kosong). Perbaiki file Excel Anda dan upload kembali.</span>
                    </div>
                </div>
            @else
                <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center" role="alert" style="border-radius: 12px;">
                    <i class="fa-solid fa-circle-check fs-4 me-3"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1">Semua Data Valid!</h6>
                        <span>Data Excel siap diimport ke database. Sistem akan membuat data murid beserta akun pengguna untuk login.</span>
                    </div>
                </div>
            @endif

            <div class="table-responsive mb-4" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-hover align-middle table-bordered">
                    <thead class="sticky-top bg-light">
                        <tr>
                            <th width="80px">Baris</th>
                            <th>NIS</th>
                            <th>NISN</th>
                            <th>Nama Lengkap</th>
                            <th>L/P</th>
                            <th>Kelas</th>
                            <th>Angkatan</th>
                            <th>Status Validasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previewData as $item)
                            <tr class="{{ $item['is_valid'] ? '' : 'table-danger' }}">
                                <td class="fw-bold">{{ $item['row_number'] }}</td>
                                <td class="small fw-600 text-muted">{{ $item['nis'] }}</td>
                                <td class="small text-muted">{{ $item['nisn'] ?? '-' }}</td>
                                <td class="fw-600">{{ $item['nama_lengkap'] }}</td>
                                <td>
                                    @if($item['jenis_kelamin'] == 'L')
                                        <span class="badge bg-info-subtle text-info rounded-circle px-2 py-1" title="Laki-laki">L</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger rounded-circle px-2 py-1" title="Perempuan">P</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border me-2">{{ $item['nama_kelas'] }}</span>
                                </td>
                                <td class="text-muted small fw-600">{{ $item['angkatan'] }}</td>
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
            <form action="{{ route('admin.murid.import.confirm') }}" method="POST">
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

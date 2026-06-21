@extends('layouts.app')

@section('title', 'Data Murid')
@section('page_title', 'Manajemen Data Murid')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="glass-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <h5 class="fw-700 m-0"><i class="fa-solid fa-users-viewfinder me-2 text-primary"></i>Daftar Murid</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fa-solid fa-file-import me-2"></i> Import Murid
                    </button>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="fa-solid fa-plus me-2"></i> Tambah Murid
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle datatable-custom">
                    <thead>
                        <tr>
                            <th width="50px" class="d-none d-sm-table-cell">No</th>
                            <th class="d-none d-md-table-cell">NIS / NISN</th>
                            <th>Nama Lengkap</th>
                            <th class="d-none d-md-table-cell">L/P</th>
                            <th class="d-none d-md-table-cell">Kelas</th>
                            <th class="d-none d-md-table-cell">Angkatan</th>
                            <th class="d-none d-sm-table-cell">Status</th>
                            <th width="120px" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($murids as $index => $murid)
                            <tr>
                                <td class="d-none d-sm-table-cell">{{ $index + 1 }}</td>
                                <td class="d-none d-md-table-cell">
                                    <span class="fw-bold d-block">{{ $murid->nis }}</span>
                                    <span class="text-muted small">{{ $murid->nisn ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="fw-600 d-block">{{ $murid->nama_lengkap }}</span>
                                    <span class="text-muted small d-none d-md-block" style="font-size: 0.75rem;"><i class="fa-solid fa-user-lock me-1"></i>Username: {{ $murid->user->username ?? '-' }}</span>
                                    
                                    <!-- Mobile details stack -->
                                    <div class="d-md-none text-muted mt-1" style="font-size: 0.8rem; line-height: 1.4;">
                                        <span class="d-block"><i class="fa-solid fa-id-card me-1"></i>NIS: {{ $murid->nis }} | Kelas: {{ $murid->kelas->nama_kelas ?? 'Tanpa Kelas' }}</span>
                                        <span class="d-block"><i class="fa-solid fa-calendar me-1"></i>Angkatan: {{ $murid->angkatan }} ({{ $murid->jenis_kelamin }})</span>
                                        <div class="mt-1 d-sm-none">
                                            @if($murid->status == 'aktif')
                                                <span class="badge bg-success" style="font-size: 0.7rem; padding: 2px 6px;">Aktif</span>
                                            @elseif($murid->status == 'lulus')
                                                <span class="badge bg-secondary" style="font-size: 0.7rem; padding: 2px 6px;">Lulus</span>
                                            @elseif($murid->status == 'pindah')
                                                <span class="badge bg-warning text-dark" style="font-size: 0.7rem; padding: 2px 6px;">Pindah</span>
                                            @else
                                                <span class="badge bg-danger" style="font-size: 0.7rem; padding: 2px 6px;">Keluar</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    @if($murid->jenis_kelamin == 'L')
                                        <span class="badge bg-info-subtle text-info rounded-circle px-2 py-1" title="Laki-laki">L</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger rounded-circle px-2 py-1" title="Perempuan">P</span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell"><span class="badge bg-primary-subtle text-primary">{{ $murid->kelas->nama_kelas ?? 'Tanpa Kelas' }}</span></td>
                                <td class="d-none d-md-table-cell text-muted small fw-600">{{ $murid->angkatan }}</td>
                                <td class="d-none d-sm-table-cell">
                                    @if($murid->status == 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @elseif($murid->status == 'lulus')
                                        <span class="badge bg-secondary">Lulus</span>
                                    @elseif($murid->status == 'pindah')
                                        <span class="badge bg-warning text-dark">Pindah</span>
                                    @else
                                        <span class="badge bg-danger">Keluar</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-outline-secondary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal"
                                                data-id="{{ $murid->id }}"
                                                data-nis="{{ $murid->nis }}"
                                                data-nisn="{{ $murid->nisn }}"
                                                data-nama="{{ $murid->nama_lengkap }}"
                                                data-jk="{{ $murid->jenis_kelamin }}"
                                                data-kelas="{{ $murid->kelas_id }}"
                                                data-angkatan="{{ $murid->angkatan }}"
                                                data-status="{{ $murid->status }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="confirmDelete('{{ $murid->id }}', '{{ $murid->nama_lengkap }}')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $murid->id }}" action="{{ route('admin.murid.destroy', $murid->id) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-700"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Tambah Murid Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.murid.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nis" class="form-label small fw-600">NIS (Username Login)</label>
                            <input type="text" class="form-control @error('nis') is-invalid @enderror" name="nis" id="nis" placeholder="e.g. 22001" value="{{ old('nis') }}" required>
                            @error('nis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="nisn" class="form-label small fw-600">NISN (Alternatif Login)</label>
                            <input type="text" class="form-control @error('nisn') is-invalid @enderror" name="nisn" id="nisn" placeholder="e.g. 0061234501" value="{{ old('nisn') }}">
                            @error('nisn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="nama_lengkap" class="form-label small fw-600">Nama Lengkap</label>
                            <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" name="nama_lengkap" id="nama_lengkap" placeholder="e.g. Ahmad Fauzi" value="{{ old('nama_lengkap') }}" required>
                            @error('nama_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="jenis_kelamin" class="form-label small fw-600">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="kelas_id" class="form-label small fw-600">Kelas</label>
                            <select name="kelas_id" id="kelas_id" class="form-select @error('kelas_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $kel)
                                    <option value="{{ $kel->id }}" {{ old('kelas_id') == $kel->id ? 'selected' : '' }}>
                                        {{ $kel->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kelas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="angkatan" class="form-label small fw-600">Tahun Angkatan</label>
                            <input type="number" class="form-control @error('angkatan') is-invalid @enderror" name="angkatan" id="angkatan" placeholder="e.g. 2022" value="{{ old('angkatan', date('Y') - 2) }}" min="2000" max="2100" required>
                            @error('angkatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label small fw-600">Status</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="lulus" {{ old('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                <option value="pindah" {{ old('status') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                                <option value="keluar" {{ old('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="password" class="form-label small fw-600">Password Akun</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" placeholder="Masukkan password minimal 6 karakter" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
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


<!-- Edit Modal -->
<div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-700"><i class="fa-solid fa-user-pen me-2 text-primary"></i>Edit Data Murid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_nis" class="form-label small fw-600">NIS (Username Login)</label>
                            <input type="text" class="form-control" name="nis" id="edit_nis" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_nisn" class="form-label small fw-600">NISN (Alternatif Login)</label>
                            <input type="text" class="form-control" name="nisn" id="edit_nisn">
                        </div>
                        <div class="col-md-12">
                            <label for="edit_nama_lengkap" class="form-label small fw-600">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap" id="edit_nama_lengkap" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_jenis_kelamin" class="form-label small fw-600">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="edit_jenis_kelamin" class="form-select" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_kelas_id" class="form-label small fw-600">Kelas</label>
                            <select name="kelas_id" id="edit_kelas_id" class="form-select" required>
                                @foreach($kelas as $kel)
                                    <option value="{{ $kel->id }}">{{ $kel->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_angkatan" class="form-label small fw-600">Tahun Angkatan</label>
                            <input type="number" class="form-control" name="angkatan" id="edit_angkatan" min="2000" max="2100" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_status" class="form-label small fw-600">Status</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="aktif">Aktif</option>
                                <option value="lulus">Lulus</option>
                                <option value="pindah">Pindah</option>
                                <option value="keluar">Keluar</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="edit_password" class="form-label small fw-600">Password Baru</label>
                            <input type="password" class="form-control" name="password" id="edit_password" placeholder="Masukkan password jika ingin mengubah">
                            <small class="text-muted" style="font-size: 0.75rem;">* Biarkan kosong jika tidak ingin mengganti password akun.</small>
                        </div>
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

<!-- Import Modal -->
<div class="modal fade" id="importModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-700"><i class="fa-solid fa-file-excel me-2 text-success"></i>Import Murid via Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.murid.import.preview') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">Unduh template Excel terlebih dahulu, isi data murid, kemudian unggah kembali untuk ditinjau.</p>
                    <div class="mb-3">
                        <a href="{{ route('admin.murid.import.template') }}" class="btn btn-outline-primary btn-sm fw-600 w-100">
                            <i class="fa-solid fa-download me-2"></i> Unduh Template Excel (.xlsx)
                        </a>
                    </div>
                    <div class="mb-3">
                        <label for="import_file_excel" class="form-label small fw-600">Pilih File Excel (.xlsx)</label>
                        <input class="form-control" type="file" id="import_file_excel" name="file_excel" accept=".xlsx,.xls,.csv" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Unggah & Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete(id, name) {
        if (confirm(`Apakah Anda yakin ingin menghapus murid "${name}"? Seluruh data nilai murid tersebut juga akan dihapus.`)) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    }

    // Dynamic Edit Modal data pre-fill
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nis = button.data('nis');
        var nisn = button.data('nisn');
        var nama = button.data('nama');
        var jk = button.data('jk');
        var kelas = button.data('kelas');
        var angkatan = button.data('angkatan');
        var status = button.data('status');
        
        var modal = $(this);
        modal.find('#editForm').attr('action', '{{ url("/admin/murid") }}/' + id);
        modal.find('#edit_nis').val(nis);
        modal.find('#edit_nisn').val(nisn);
        modal.find('#edit_nama_lengkap').val(nama);
        modal.find('#edit_jenis_kelamin').val(jk);
        modal.find('#edit_kelas_id').val(kelas);
        modal.find('#edit_angkatan').val(angkatan);
        modal.find('#edit_status').val(status);
        modal.find('#edit_password').val('');
    });

    // Auto open modal on validation errors
    @if ($errors->any())
        $(document).ready(function() {
            var myModal = new bootstrap.Modal(document.getElementById('createModal'));
            myModal.show();
        });
    @endif
</script>
@endsection

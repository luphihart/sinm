@extends('layouts.app')

@section('title', 'Data Kelas')
@section('page_title', 'Manajemen Data Kelas')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="glass-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="fw-700 m-0"><i class="fa-solid fa-school me-2 text-primary"></i>Daftar Kelas</h5>
                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fa-solid fa-plus me-2"></i> Tambah Kelas
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle datatable-custom">
                    <thead>
                        <tr>
                            <th width="80px" class="d-none d-sm-table-cell">No</th>
                            <th>Nama Kelas</th>
                            <th class="d-none d-md-table-cell">Tingkat</th>
                            <th class="d-none d-md-table-cell">Jurusan</th>
                            <th class="d-none d-sm-table-cell">Jumlah Murid</th>
                            <th width="150px" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelas as $index => $kel)
                            <tr>
                                <td class="d-none d-sm-table-cell">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-600 d-block">{{ $kel->nama_kelas }}</span>
                                    
                                    <!-- Mobile details stack -->
                                    <div class="d-md-none text-muted mt-1" style="font-size: 0.8rem; line-height: 1.4;">
                                        <span class="d-block"><i class="fa-solid fa-graduation-cap me-1"></i>Jurusan: {{ $kel->jurusan->nama_jurusan }}</span>
                                        <span class="d-block"><i class="fa-solid fa-layer-group me-1"></i>Tingkat: {{ $kel->tingkat }} | <i class="fa-solid fa-users me-1"></i>{{ $kel->murid->count() }} Murid</span>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell"><span class="badge bg-secondary-subtle text-secondary fw-bold px-2 py-1">{{ $kel->tingkat }}</span></td>
                                <td class="d-none d-md-table-cell"><span class="badge bg-primary-subtle text-primary">{{ $kel->jurusan->nama_jurusan }}</span></td>
                                <td class="d-none d-sm-table-cell"><span class="badge bg-light text-dark border">{{ $kel->murid->count() }} Murid</span></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-outline-secondary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal"
                                                data-id="{{ $kel->id }}"
                                                data-nama="{{ $kel->nama_kelas }}"
                                                data-tingkat="{{ $kel->tingkat }}"
                                                data-jurusan="{{ $kel->jurusan_id }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="confirmDelete('{{ $kel->id }}', '{{ $kel->nama_kelas }}')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $kel->id }}" action="{{ route('admin.kelas.destroy', $kel->id) }}" method="POST" class="d-none">
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-700"><i class="fa-solid fa-plus me-2 text-primary"></i>Tambah Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_kelas" class="form-label small fw-600">Nama Kelas</label>
                        <input type="text" class="form-control @error('nama_kelas') is-invalid @enderror" name="nama_kelas" id="nama_kelas" placeholder="e.g. XII RPL 1" value="{{ old('nama_kelas') }}" required>
                        @error('nama_kelas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="tingkat" class="form-label small fw-600">Tingkat</label>
                        <select name="tingkat" id="tingkat" class="form-select @error('tingkat') is-invalid @enderror" required>
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="X" {{ old('tingkat') == 'X' ? 'selected' : '' }}>X (10)</option>
                            <option value="XI" {{ old('tingkat') == 'XI' ? 'selected' : '' }}>XI (11)</option>
                            <option value="XII" {{ old('tingkat') == 'XII' ? 'selected' : '' }}>XII (12)</option>
                        </select>
                        @error('tingkat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="jurusan_id" class="form-label small fw-600">Jurusan</label>
                        <select name="jurusan_id" id="jurusan_id" class="form-select @error('jurusan_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($jurusans as $jur)
                                <option value="{{ $jur->id }}" {{ old('jurusan_id') == $jur->id ? 'selected' : '' }}>
                                    {{ $jur->nama_jurusan }} ({{ $jur->kode_jurusan }})
                                </option>
                            @endforeach
                        </select>
                        @error('jurusan_id')
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

<!-- Edit Modal -->
<div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-700"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama_kelas" class="form-label small fw-600">Nama Kelas</label>
                        <input type="text" class="form-control" name="nama_kelas" id="edit_nama_kelas" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tingkat" class="form-label small fw-600">Tingkat</label>
                        <select name="tingkat" id="edit_tingkat" class="form-select" required>
                            <option value="X">X (10)</option>
                            <option value="XI">XI (11)</option>
                            <option value="XII">XII (12)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_jurusan_id" class="form-label small fw-600">Jurusan</label>
                        <select name="jurusan_id" id="edit_jurusan_id" class="form-select" required>
                            @foreach($jurusans as $jur)
                                <option value="{{ $jur->id }}">{{ $jur->nama_jurusan }}</option>
                            @endforeach
                        </select>
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
@endsection

@section('scripts')
<script>
    function confirmDelete(id, name) {
        if (confirm(`Apakah Anda yakin ingin menghapus kelas "${name}"? Tindakan ini tidak dapat dibatalkan.`)) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    }

    // Dynamic Edit Modal data pre-fill
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nama = button.data('nama');
        var tingkat = button.data('tingkat');
        var jurusan = button.data('jurusan');
        
        var modal = $(this);
        modal.find('#editForm').attr('action', '{{ url("/admin/kelas") }}/' + id);
        modal.find('#edit_nama_kelas').val(nama);
        modal.find('#edit_tingkat').val(tingkat);
        modal.find('#edit_jurusan_id').val(jurusan);
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

@extends('layouts.app')

@section('title', 'Data Jurusan')
@section('page_title', 'Manajemen Data Jurusan')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="glass-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="fw-700 m-0"><i class="fa-solid fa-network-wired me-2 text-primary"></i>Daftar Jurusan</h5>
                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fa-solid fa-plus me-2"></i> Tambah Jurusan
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle datatable-custom">
                    <thead>
                        <tr>
                            <th width="80px" class="d-none d-sm-table-cell">No</th>
                            <th class="d-none d-sm-table-cell">Kode Jurusan</th>
                            <th>Nama Jurusan</th>
                            <th class="d-none d-sm-table-cell">Jumlah Kelas</th>
                            <th width="150px" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jurusans as $index => $jur)
                            <tr>
                                <td class="d-none d-sm-table-cell">{{ $index + 1 }}</td>
                                <td class="d-none d-sm-table-cell"><span class="badge bg-primary-subtle text-primary fw-bold">{{ $jur->kode_jurusan }}</span></td>
                                <td>
                                    <span class="fw-600 d-block">{{ $jur->nama_jurusan }}</span>
                                    
                                    <!-- Mobile details stack -->
                                    <div class="d-sm-none text-muted mt-1" style="font-size: 0.8rem; line-height: 1.4;">
                                        <span class="d-block"><i class="fa-solid fa-tag me-1"></i>Kode: {{ $jur->kode_jurusan }} | <i class="fa-solid fa-school me-1"></i>{{ $jur->kelas->count() }} Kelas</span>
                                    </div>
                                </td>
                                <td class="d-none d-sm-table-cell"><span class="badge bg-light text-dark border">{{ $jur->kelas->count() }} Kelas</span></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-outline-secondary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal"
                                                data-id="{{ $jur->id }}"
                                                data-kode="{{ $jur->kode_jurusan }}"
                                                data-nama="{{ $jur->nama_jurusan }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="confirmDelete('{{ $jur->id }}', '{{ $jur->nama_lengkap ?? $jur->nama_jurusan }}')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $jur->id }}" action="{{ route('admin.jurusan.destroy', $jur->id) }}" method="POST" class="d-none">
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
                <h5 class="modal-title fw-700"><i class="fa-solid fa-plus me-2 text-primary"></i>Tambah Jurusan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.jurusan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kode_jurusan" class="form-label small fw-600">Kode Jurusan</label>
                        <input type="text" class="form-control @error('kode_jurusan') is-invalid @enderror" name="kode_jurusan" id="kode_jurusan" placeholder="e.g. RPL" value="{{ old('kode_jurusan') }}" required>
                        @error('kode_jurusan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nama_jurusan" class="form-label small fw-600">Nama Jurusan</label>
                        <input type="text" class="form-control @error('nama_jurusan') is-invalid @enderror" name="nama_jurusan" id="nama_jurusan" placeholder="e.g. Rekayasa Perangkat Lunak" value="{{ old('nama_jurusan') }}" required>
                        @error('nama_jurusan')
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
                <h5 class="modal-title fw-700"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit Jurusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_kode_jurusan" class="form-label small fw-600">Kode Jurusan</label>
                        <input type="text" class="form-control" name="kode_jurusan" id="edit_kode_jurusan" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama_jurusan" class="form-label small fw-600">Nama Jurusan</label>
                        <input type="text" class="form-control" name="nama_jurusan" id="edit_nama_jurusan" required>
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
        if (confirm(`Apakah Anda yakin ingin menghapus jurusan "${name}"? Tindakan ini tidak dapat dibatalkan.`)) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    }

    // Dynamic Edit Modal data pre-fill
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var kode = button.data('kode');
        var nama = button.data('nama');
        
        var modal = $(this);
        modal.find('#editForm').attr('action', '{{ url("/admin/jurusan") }}/' + id);
        modal.find('#edit_kode_jurusan').val(kode);
        modal.find('#edit_nama_jurusan').val(nama);
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

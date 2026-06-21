@extends('layouts.app')

@section('title', 'Data Mata Pelajaran')
@section('page_title', 'Manajemen Mata Pelajaran')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="glass-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="fw-700 m-0"><i class="fa-solid fa-book me-2 text-primary"></i>Daftar Mata Pelajaran</h5>
                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fa-solid fa-plus me-2"></i> Tambah Mapel
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle datatable-custom">
                    <thead>
                        <tr>
                            <th width="80px" class="d-none d-sm-table-cell">No</th>
                            <th class="d-none d-sm-table-cell">Kode Mapel</th>
                            <th>Nama Mata Pelajaran</th>
                            <th class="d-none d-md-table-cell">Urutan Tampil</th>
                            <th class="d-none d-md-table-cell">Tanggal Dibuat</th>
                            <th width="150px" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mapels as $index => $mapel)
                            <tr>
                                <td class="d-none d-sm-table-cell">{{ $index + 1 }}</td>
                                <td class="d-none d-sm-table-cell"><span class="badge bg-primary-subtle text-primary fw-bold">{{ $mapel->kode_mapel }}</span></td>
                                <td>
                                    <span class="fw-600 d-block">{{ $mapel->nama_mapel }}</span>
                                    
                                    <!-- Mobile details stack -->
                                    <div class="d-sm-none text-muted mt-1" style="font-size: 0.8rem; line-height: 1.4;">
                                        <span class="d-block"><i class="fa-solid fa-barcode me-1"></i>Kode: {{ $mapel->kode_mapel }} | Urutan: {{ $mapel->urutan }}</span>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell"><span class="badge bg-secondary-subtle text-secondary fw-bold px-2 py-1">{{ $mapel->urutan }}</span></td>
                                <td class="d-none d-md-table-cell text-muted small">{{ $mapel->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-outline-secondary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal"
                                                data-id="{{ $mapel->id }}"
                                                data-kode="{{ $mapel->kode_mapel }}"
                                                data-nama="{{ $mapel->nama_mapel }}"
                                                data-urutan="{{ $mapel->urutan }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="confirmDelete('{{ $mapel->id }}', '{{ $mapel->nama_mapel }}')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $mapel->id }}" action="{{ route('admin.mapel.destroy', $mapel->id) }}" method="POST" class="d-none">
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
                <h5 class="modal-title fw-700"><i class="fa-solid fa-plus me-2 text-primary"></i>Tambah Mata Pelajaran Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.mapel.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kode_mapel" class="form-label small fw-600">Kode Mapel</label>
                        <input type="text" class="form-control @error('kode_mapel') is-invalid @enderror" name="kode_mapel" id="kode_mapel" placeholder="e.g. MAPEL01" value="{{ old('kode_mapel') }}" required>
                        @error('kode_mapel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nama_mapel" class="form-label small fw-600">Nama Mata Pelajaran</label>
                        <input type="text" class="form-control @error('nama_mapel') is-invalid @enderror" name="nama_mapel" id="nama_mapel" placeholder="e.g. Matematika" value="{{ old('nama_mapel') }}" required>
                        @error('nama_mapel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="urutan" class="form-label small fw-600">Urutan Tampil</label>
                        <input type="number" class="form-control @error('urutan') is-invalid @enderror" name="urutan" id="urutan" placeholder="e.g. 1" value="{{ old('urutan', 0) }}" required>
                        @error('urutan')
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
                <h5 class="modal-title fw-700"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit Mata Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_kode_mapel" class="form-label small fw-600">Kode Mapel</label>
                        <input type="text" class="form-control" name="kode_mapel" id="edit_kode_mapel" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama_mapel" class="form-label small fw-600">Nama Mata Pelajaran</label>
                        <input type="text" class="form-control" name="nama_mapel" id="edit_nama_mapel" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_urutan" class="form-label small fw-600">Urutan Tampil</label>
                        <input type="number" class="form-control" name="urutan" id="edit_urutan" required>
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
        if (confirm(`Apakah Anda yakin ingin menghapus mata pelajaran "${name}"? Seluruh data nilai mata pelajaran ini juga akan dihapus.`)) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    }

    // Dynamic Edit Modal data pre-fill
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var kode = button.data('kode');
        var nama = button.data('nama');
        var urutan = button.data('urutan');
        
        var modal = $(this);
        modal.find('#editForm').attr('action', '{{ url("/admin/mapel") }}/' + id);
        modal.find('#edit_kode_mapel').val(kode);
        modal.find('#edit_nama_mapel').val(nama);
        modal.find('#edit_urutan').val(urutan);
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

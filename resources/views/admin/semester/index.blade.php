@extends('layouts.app')

@section('title', 'Data Semester')
@section('page_title', 'Manajemen Semester & Tahun Ajaran')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="glass-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="fw-700 m-0"><i class="fa-solid fa-calendar-days me-2 text-primary"></i>Daftar Semester</h5>
                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fa-solid fa-plus me-2"></i> Tambah Semester
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle datatable-custom">
                    <thead>
                        <tr>
                            <th width="80px" class="d-none d-sm-table-cell">No</th>
                            <th>Semester</th>
                            <th class="d-none d-sm-table-cell">Tahun Ajaran</th>
                            <th class="d-none d-sm-table-cell">Tanggal Dibuat</th>
                            <th width="150px" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($semesters as $index => $sem)
                            <tr>
                                <td class="d-none d-sm-table-cell">{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-primary fw-bold px-3 py-2">Semester {{ $sem->semester_ke }}</span>
                                    <span class="text-muted small d-block d-sm-none mt-1">Tahun Ajaran {{ $sem->tahun_ajaran }}</span>
                                </td>
                                <td class="d-none d-sm-table-cell fw-600">Tahun Ajaran {{ $sem->tahun_ajaran }}</td>
                                <td class="d-none d-sm-table-cell text-muted small">{{ $sem->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-outline-secondary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal"
                                                data-id="{{ $sem->id }}"
                                                data-ke="{{ $sem->semester_ke }}"
                                                data-tahun="{{ $sem->tahun_ajaran }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="confirmDelete('{{ $sem->id }}', '{{ $sem->semester_ke }}')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $sem->id }}" action="{{ route('admin.semester.destroy', $sem->id) }}" method="POST" class="d-none">
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
                <h5 class="modal-title fw-700"><i class="fa-solid fa-plus me-2 text-primary"></i>Tambah Semester Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.semester.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="semester_ke" class="form-label small fw-600">Semester Ke-</label>
                        <select name="semester_ke" id="semester_ke" class="form-select @error('semester_ke') is-invalid @enderror" required>
                            <option value="">-- Pilih Semester --</option>
                            @for($i=1; $i<=6; $i++)
                                <option value="{{ $i }}" {{ old('semester_ke') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                            @endfor
                        </select>
                        @error('semester_ke')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="tahun_ajaran" class="form-label small fw-600">Tahun Ajaran</label>
                        <input type="text" class="form-control @error('tahun_ajaran') is-invalid @enderror" name="tahun_ajaran" id="tahun_ajaran" placeholder="e.g. 2024/2025" value="{{ old('tahun_ajaran') }}" required>
                        @error('tahun_ajaran')
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
                <h5 class="modal-title fw-700"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit Semester</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_semester_ke" class="form-label small fw-600">Semester Ke-</label>
                        <select name="semester_ke" id="edit_semester_ke" class="form-select" required>
                            @for($i=1; $i<=6; $i++)
                                <option value="{{ $i }}">Semester {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tahun_ajaran" class="form-label small fw-600">Tahun Ajaran</label>
                        <input type="text" class="form-control" name="tahun_ajaran" id="edit_tahun_ajaran" required>
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
    function confirmDelete(id, semester_ke) {
        if (confirm(`Apakah Anda yakin ingin menghapus Semester ${semester_ke}? Seluruh data nilai pada semester ini juga akan dihapus.`)) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    }

    // Dynamic Edit Modal data pre-fill
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var ke = button.data('ke');
        var tahun = button.data('tahun');
        
        var modal = $(this);
        modal.find('#editForm').attr('action', '{{ url("/admin/semester") }}/' + id);
        modal.find('#edit_semester_ke').val(ke);
        modal.find('#edit_tahun_ajaran').val(tahun);
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

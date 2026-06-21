@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page_title', 'Pengaturan Identitas Sekolah & Sistem')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="glass-card p-4">
            <h5 class="fw-700 mb-4"><i class="fa-solid fa-sliders text-primary me-2"></i>Identitas Sekolah & Konfigurasi</h5>
            
            <form action="{{ route('admin.setting.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <!-- Nama Aplikasi -->
                    <div class="col-md-6">
                        <label for="app_name" class="form-label small fw-600">Nama Aplikasi (Global)</label>
                        <input type="text" class="form-control" name="app_name" id="app_name" value="{{ old('app_name', $settings['app_name']) }}" required>
                    </div>

                    <!-- Teks Footer -->
                    <div class="col-md-6">
                        <label for="footer_text" class="form-label small fw-600">Teks Footer (Global)</label>
                        <input type="text" class="form-control" name="footer_text" id="footer_text" value="{{ old('footer_text', $settings['footer_text']) }}" required>
                    </div>

                    <!-- Nama Sekolah -->
                    <div class="col-md-12">
                        <label for="school_name" class="form-label small fw-600">Nama Sekolah</label>
                        <input type="text" class="form-control" name="school_name" id="school_name" value="{{ old('school_name', $settings['school_name']) }}" required>
                    </div>

                    <!-- Alamat Sekolah -->
                    <div class="col-md-12">
                        <label for="school_address" class="form-label small fw-600">Alamat Sekolah</label>
                        <textarea class="form-control" name="school_address" id="school_address" rows="3" required>{{ old('school_address', $settings['school_address']) }}</textarea>
                    </div>

                    <!-- Telepon Sekolah -->
                    <div class="col-md-6">
                        <label for="school_phone" class="form-label small fw-600">Telepon / Fax</label>
                        <input type="text" class="form-control" name="school_phone" id="school_phone" value="{{ old('school_phone', $settings['school_phone']) }}">
                    </div>

                    <!-- Website Sekolah -->
                    <div class="col-md-6">
                        <label for="school_website" class="form-label small fw-600">Website Resmi</label>
                        <input type="text" class="form-control" name="school_website" id="school_website" value="{{ old('school_website', $settings['school_website']) }}">
                    </div>

                    <!-- Nama Kepala Sekolah -->
                    <div class="col-md-6">
                        <label for="headmaster_name" class="form-label small fw-600">Nama Kepala Sekolah</label>
                        <input type="text" class="form-control" name="headmaster_name" id="headmaster_name" value="{{ old('headmaster_name', $settings['headmaster_name']) }}" required>
                    </div>

                    <!-- NIP Kepala Sekolah -->
                    <div class="col-md-6">
                        <label for="headmaster_nip" class="form-label small fw-600">NIP Kepala Sekolah</label>
                        <input type="text" class="form-control" name="headmaster_nip" id="headmaster_nip" value="{{ old('headmaster_nip', $settings['headmaster_nip']) }}">
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary-custom px-4 py-2">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Box -->
    <div class="col-lg-4">
        <div class="glass-card p-4">
            <h5 class="fw-700 mb-3"><i class="fa-solid fa-circle-info text-info me-2"></i>Informasi Pengaturan</h5>
            <p class="text-muted small">Data pengaturan ini digunakan secara otomatis di seluruh sistem, termasuk:</p>
            <ul class="text-muted small ps-3">
                <li class="mb-2">Nama Aplikasi yang tertampil di Header, Title bar, dan halaman login secara global.</li>
                <li class="mb-2">Kop surat pada cetak **PDF Rapor Semester** dan **PDF Transkrip Lengkap**.</li>
                <li class="mb-2">Tanda tangan Kepala Sekolah di bagian bawah lembar laporan PDF.</li>
                <li class="mb-2">Teks Footer di bagian bawah halaman login dan dashboard sistem.</li>
            </ul>
        </div>
    </div>
</div>
@endsection

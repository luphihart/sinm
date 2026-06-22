<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MuridController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\NilaiController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Murid\DashboardController as MuridDashboardController;
use App\Http\Controllers\Murid\NilaiController as MuridNilaiController;
use App\Http\Controllers\Admin\SnbpController as AdminSnbpController;
use App\Http\Controllers\Murid\SnbpController as MuridSnbpController;

// 1. Redirect Root
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 3. Admin Routes Group
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // CRUD Jurusan
    Route::resource('jurusan', JurusanController::class)->except(['create', 'show', 'edit']);

    // CRUD Kelas
    Route::resource('kelas', KelasController::class)->except(['create', 'show', 'edit']);

    // CRUD Murid
    Route::post('/murid/import/preview', [MuridController::class, 'importPreview'])->name('murid.import.preview');
    Route::post('/murid/import/confirm', [MuridController::class, 'importConfirm'])->name('murid.import.confirm');
    Route::get('/murid/import/template', [MuridController::class, 'downloadTemplate'])->name('murid.import.template');
    Route::post('/murid/{id}/reset-password', [MuridController::class, 'resetPassword'])->name('murid.reset-password');
    Route::post('/murid/bulk-reset-password', [MuridController::class, 'bulkResetPassword'])->name('murid.bulk-reset-password');
    Route::resource('murid', MuridController::class)->except(['create', 'show', 'edit']);

    // CRUD Mata Pelajaran (Mapel)
    Route::resource('mapel', MataPelajaranController::class)->except(['create', 'show', 'edit']);

    // CRUD Semester
    Route::resource('semester', SemesterController::class)->except(['create', 'show', 'edit']);

    // CRUD Nilai & Excel/PDF Actions
    Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
    Route::post('/nilai', [NilaiController::class, 'store'])->name('nilai.store');
    Route::put('/nilai/{id}', [NilaiController::class, 'update'])->name('nilai.update');
    Route::delete('/nilai/{id}', [NilaiController::class, 'destroy'])->name('nilai.destroy');

    // Excel Import
    Route::post('/nilai/import/preview', [NilaiController::class, 'importPreview'])->name('nilai.import.preview');
    Route::post('/nilai/import/confirm', [NilaiController::class, 'importConfirm'])->name('nilai.import.confirm');

    // Class Grid Excel Import
    Route::get('/nilai/import-class/template', [NilaiController::class, 'downloadClassTemplate'])->name('nilai.import-class.template');
    Route::post('/nilai/import-class/preview', [NilaiController::class, 'importClassPreview'])->name('nilai.import-class.preview');
    Route::post('/nilai/import-class/confirm', [NilaiController::class, 'importClassConfirm'])->name('nilai.import-class.confirm');

    // PDF/Excel Exports
    Route::get('/nilai/export/rapor/{murid_id}/{semester_id}', [NilaiController::class, 'exportPdfRapor'])->name('nilai.export.rapor');
    Route::get('/nilai/export/transkrip/{murid_id}', [NilaiController::class, 'exportPdfTranskrip'])->name('nilai.export.transkrip');
    
    Route::get('/nilai/export/pdf/kelas', [NilaiController::class, 'exportPdfRankingKelas'])->name('nilai.export.pdf.kelas');
    Route::get('/nilai/export/pdf/jurusan', [NilaiController::class, 'exportPdfRankingJurusan'])->name('nilai.export.pdf.jurusan');
    
    Route::get('/nilai/export/excel/kelas', [NilaiController::class, 'exportExcelRankingKelas'])->name('nilai.export.excel.kelas');
    Route::get('/nilai/export/excel/jurusan', [NilaiController::class, 'exportExcelRankingJurusan'])->name('nilai.export.excel.jurusan');

    // SNBP Eligible Selection
    Route::get('/snbp', [AdminSnbpController::class, 'index'])->name('snbp.index');
    Route::post('/snbp/settings', [AdminSnbpController::class, 'updateSettings'])->name('snbp.settings');
    Route::post('/snbp/quota', [AdminSnbpController::class, 'updateQuota'])->name('snbp.quota');

    // Pengaturan
    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::put('/setting', [SettingController::class, 'update'])->name('setting.update');
});

// 4. Murid Routes Group
Route::middleware(['auth', 'role:murid'])->prefix('murid')->name('murid.')->group(function () {
    // Dashboard (Profile, Grades Table, Chart, Rankings)
    Route::get('/dashboard', [MuridDashboardController::class, 'index'])->name('dashboard');

    // Secure PDF Downloads (uses session user to avoid ID manipulation)
    Route::get('/nilai/rapor/{semester_id}/pdf', [MuridNilaiController::class, 'exportRaporPdf'])->name('nilai.export.rapor');
    Route::get('/nilai/transkrip/pdf', [MuridNilaiController::class, 'exportTranskripPdf'])->name('nilai.export.transkrip');

    // SNBP Eligible Selection
    Route::get('/snbp', [MuridSnbpController::class, 'index'])->name('snbp.index');
    Route::post('/snbp/daftar', [MuridSnbpController::class, 'register'])->name('snbp.daftar');
    Route::post('/snbp/batal', [MuridSnbpController::class, 'withdraw'])->name('snbp.batal');
});

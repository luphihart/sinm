<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Jurusan
        Schema::create('jurusan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jurusan')->unique();
            $table->string('nama_jurusan');
            $table->timestamps();
        });

        // 2. Tabel Kelas
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurusan_id')->constrained('jurusan')->onDelete('cascade');
            $table->string('nama_kelas');
            $table->string('tingkat'); // e.g. X, XI, XII
            $table->timestamps();
        });

        // 3. Tabel Murid
        Schema::create('murid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nis')->unique();
            $table->string('nisn')->unique()->nullable();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('restrict');
            $table->integer('angkatan');
            $table->enum('status', ['aktif', 'lulus', 'pindah', 'keluar'])->default('aktif');
            $table->timestamps();
        });

        // 4. Tabel Semester
        Schema::create('semester', function (Blueprint $table) {
            $table->id();
            $table->integer('semester_ke'); // 1 s.d. 6
            $table->string('tahun_ajaran'); // e.g. 2024/2025
            $table->timestamps();
        });

        // 5. Tabel Mata Pelajaran
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mapel')->unique();
            $table->string('nama_mapel');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // 6. Tabel Nilai
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('murid_id')->constrained('murid')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->decimal('nilai', 5, 2); // 0.00 - 100.00
            $table->timestamps();

            // Cegah duplikasi nilai untuk murid yang sama pada semester & mapel yang sama
            $table->unique(['murid_id', 'semester_id', 'mata_pelajaran_id'], 'murid_semester_mapel_unique');
        });

        // 7. Tabel Settings (Identitas Sekolah & Konfigurasi)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('nilai');
        Schema::dropIfExists('mata_pelajaran');
        Schema::dropIfExists('semester');
        Schema::dropIfExists('murid');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('jurusan');
    }
};

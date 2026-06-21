<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Murid;
use App\Models\Semester;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin
        User::create([
            'name' => 'Administrator Sekolah',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // 2. Seed Jurusan
        $jurusanRpl = Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
        ]);

        $jurusanDkv = Jurusan::create([
            'kode_jurusan' => 'DKV',
            'nama_jurusan' => 'Desain Komunikasi Visual',
        ]);

        $jurusanTkj = Jurusan::create([
            'kode_jurusan' => 'TKJ',
            'nama_jurusan' => 'Teknik Komputer dan Jaringan',
        ]);

        // 3. Seed Kelas
        $kelasRpl1 = Kelas::create([
            'jurusan_id' => $jurusanRpl->id,
            'nama_kelas' => 'XII RPL 1',
            'tingkat' => 'XII',
        ]);

        $kelasRpl2 = Kelas::create([
            'jurusan_id' => $jurusanRpl->id,
            'nama_kelas' => 'XII RPL 2',
            'tingkat' => 'XII',
        ]);

        $kelasDkv1 = Kelas::create([
            'jurusan_id' => $jurusanDkv->id,
            'nama_kelas' => 'XII DKV 1',
            'tingkat' => 'XII',
        ]);

        $kelasDkv2 = Kelas::create([
            'jurusan_id' => $jurusanDkv->id,
            'nama_kelas' => 'XII DKV 2',
            'tingkat' => 'XII',
        ]);

        $kelasTkj1 = Kelas::create([
            'jurusan_id' => $jurusanTkj->id,
            'nama_kelas' => 'XII TKJ 1',
            'tingkat' => 'XII',
        ]);

        // 4. Seed Semester (1 s.d. 6)
        $semesters = [];
        for ($i = 1; $i <= 6; $i++) {
            // Tahun ajaran bergeser setiap 2 semester
            $tahunAwal = 2021 + floor(($i - 1) / 2);
            $tahunAkhir = $tahunAwal + 1;
            
            $semesters[$i] = Semester::create([
                'semester_ke' => $i,
                'tahun_ajaran' => "$tahunAwal/$tahunAkhir",
            ]);
        }

        // 5. Seed Mata Pelajaran
        $mapels = [
            ['kode_mapel' => 'MAPEL01', 'nama_mapel' => 'Matematika', 'urutan' => 1],
            ['kode_mapel' => 'MAPEL02', 'nama_mapel' => 'Bahasa Indonesia', 'urutan' => 2],
            ['kode_mapel' => 'MAPEL03', 'nama_mapel' => 'Bahasa Inggris', 'urutan' => 3],
            ['kode_mapel' => 'MAPEL04', 'nama_mapel' => 'Fisika', 'urutan' => 4],
            ['kode_mapel' => 'MAPEL05', 'nama_mapel' => 'Pendidikan Pancasila', 'urutan' => 5],
            ['kode_mapel' => 'MAPEL06', 'nama_mapel' => 'Pemrograman Web (Produktif)', 'urutan' => 6],
            ['kode_mapel' => 'MAPEL07', 'nama_mapel' => 'Desain Grafis (Produktif)', 'urutan' => 7],
            ['kode_mapel' => 'MAPEL08', 'nama_mapel' => 'Jaringan Komputer (Produktif)', 'urutan' => 8],
        ];

        $mapelModels = [];
        foreach ($mapels as $mapel) {
            $mapelModels[$mapel['kode_mapel']] = MataPelajaran::create($mapel);
        }

        // 6. Seed Murid & Users (Murid)
        $muridData = [
            // XII RPL 1
            ['nis' => '22001', 'nisn' => '0061234501', 'nama' => 'Ahmad Fauzi', 'jk' => 'L', 'kelas_id' => $kelasRpl1->id, 'angkatan' => 2022],
            ['nis' => '22002', 'nisn' => '0061234502', 'nama' => 'Budi Santoso', 'jk' => 'L', 'kelas_id' => $kelasRpl1->id, 'angkatan' => 2022],
            ['nis' => '22003', 'nisn' => '0061234503', 'nama' => 'Citra Lestari', 'jk' => 'P', 'kelas_id' => $kelasRpl1->id, 'angkatan' => 2022],
            // XII RPL 2
            ['nis' => '22004', 'nisn' => '0061234504', 'nama' => 'Dewi Anggraini', 'jk' => 'P', 'kelas_id' => $kelasRpl2->id, 'angkatan' => 2022],
            ['nis' => '22005', 'nisn' => '0061234505', 'nama' => 'Eko Prasetyo', 'jk' => 'L', 'kelas_id' => $kelasRpl2->id, 'angkatan' => 2022],
            ['nis' => '22006', 'nisn' => '0061234506', 'nama' => 'Fitri Handayani', 'jk' => 'P', 'kelas_id' => $kelasRpl2->id, 'angkatan' => 2022],
            // XII DKV 1
            ['nis' => '22007', 'nisn' => '0061234507', 'nama' => 'Gerry Ramadhan', 'jk' => 'L', 'kelas_id' => $kelasDkv1->id, 'angkatan' => 2022],
            ['nis' => '22008', 'nisn' => '0061234508', 'nama' => 'Hani Wijaya', 'jk' => 'P', 'kelas_id' => $kelasDkv1->id, 'angkatan' => 2022],
            // XII DKV 2
            ['nis' => '22009', 'nisn' => '0061234509', 'nama' => 'Indah Permatasari', 'jk' => 'P', 'kelas_id' => $kelasDkv2->id, 'angkatan' => 2022],
            ['nis' => '22010', 'nisn' => '0061234510', 'nama' => 'Joko Susilo', 'jk' => 'L', 'kelas_id' => $kelasDkv2->id, 'angkatan' => 2022],
            // XII TKJ 1
            ['nis' => '22011', 'nisn' => '0061234511', 'nama' => 'Kurniawan', 'jk' => 'L', 'kelas_id' => $kelasTkj1->id, 'angkatan' => 2022],
            ['nis' => '22012', 'nisn' => '0061234512', 'nama' => 'Lestari Ningsih', 'jk' => 'P', 'kelas_id' => $kelasTkj1->id, 'angkatan' => 2022],
        ];

        // Daftar mapel per jurusan
        // RPL: MAPEL01, MAPEL02, MAPEL03, MAPEL04, MAPEL05, MAPEL06
        // DKV: MAPEL01, MAPEL02, MAPEL03, MAPEL04, MAPEL05, MAPEL07
        // TKJ: MAPEL01, MAPEL02, MAPEL03, MAPEL04, MAPEL05, MAPEL08

        foreach ($muridData as $data) {
            // Buat User
            $user = User::create([
                'name' => $data['nama'],
                'username' => $data['nis'],
                'password' => Hash::make('siswa123'),
                'role' => 'murid',
            ]);

            // Buat Murid
            $murid = Murid::create([
                'user_id' => $user->id,
                'nis' => $data['nis'],
                'nisn' => $data['nisn'],
                'nama_lengkap' => $data['nama'],
                'jenis_kelamin' => $data['jk'],
                'kelas_id' => $data['kelas_id'],
                'angkatan' => $data['angkatan'],
                'status' => 'aktif',
            ]);

            // Dapatkan jurusan dari kelas
            $kelas = Kelas::find($data['kelas_id']);
            $jurusan = Jurusan::find($kelas->jurusan_id);

            // Tentukan subject list
            $subjectCodes = ['MAPEL01', 'MAPEL02', 'MAPEL03', 'MAPEL04', 'MAPEL05'];
            if ($jurusan->kode_jurusan === 'RPL') {
                $subjectCodes[] = 'MAPEL06';
            } elseif ($jurusan->kode_jurusan === 'DKV') {
                $subjectCodes[] = 'MAPEL07';
            } elseif ($jurusan->kode_jurusan === 'TKJ') {
                $subjectCodes[] = 'MAPEL08';
            }

            // Seed Nilai untuk Semester 1 s.d. 5 (Semester 6 sengaja dikosongkan sebagian atau diisi sedikit untuk test input)
            // Kita seed Semester 1 s.d. 5 lengkap untuk melihat perkembangan nilai (Chart.js)
            for ($sem = 1; $sem <= 5; $sem++) {
                foreach ($subjectCodes as $code) {
                    // Berikan nilai acak yang terdistribusi wajar (misal 65 - 98)
                    // Tapi berikan nilai dasar agar ada ranking yang konsisten (misal murid 1 lebih pintar dari murid 2)
                    $baseOffset = 75 + ($murid->id % 5) * 4 - ($code === 'MAPEL01' ? 5 : 0); // Modifikasi kecil
                    $randomGrade = min(100, max(0, $baseOffset + rand(-5, 5)));

                    Nilai::create([
                        'murid_id' => $murid->id,
                        'semester_id' => $semesters[$sem]->id,
                        'mata_pelajaran_id' => $mapelModels[$code]->id,
                        'nilai' => $randomGrade,
                    ]);
                }
            }
        }

        // 7. Seed Settings
        $settings = [
            'app_name' => 'SINM',
            'footer_text' => '© 2026 SINM. All Rights Reserved.',
            'school_name' => 'SMK Negeri 1 Kota Belajar',
            'school_address' => 'Jl. Pendidikan No. 45, Kota Belajar',
            'school_phone' => '(021) 1234567',
            'school_website' => 'www.smkn1kotabelajar.sch.id',
            'headmaster_name' => 'Drs. H. Mulyono, M.Pd.',
            'headmaster_nip' => '19750812 200003 1 002',
        ];

        foreach ($settings as $key => $value) {
            \App\Models\Setting::create([
                'key' => $key,
                'value' => $value,
            ]);
        }
    }
}

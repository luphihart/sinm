<?php

namespace App\Services;

use App\Models\User;
use App\Models\Murid;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class ImportMuridService
{
    /**
     * Parse Excel/CSV and return preview data with validation errors.
     */
    public function preview($file)
    {
        $rows = Excel::toArray([], $file)[0] ?? [];

        if (empty($rows)) {
            return [
                'isValid' => false,
                'errors' => ['File Excel kosong atau tidak terbaca.'],
                'data' => []
            ];
        }

        // Cek header. Jika baris pertama adalah header, kita skip.
        $firstRow = $rows[0];
        $hasHeader = false;
        if (isset($firstRow[0]) && is_string($firstRow[0]) && preg_match('/nis/i', $firstRow[0])) {
            $hasHeader = true;
        }

        $previewData = [];
        $isValidFile = true;
        $globalErrors = [];

        // Cache database lookups to optimize queries
        $existingNis = Murid::pluck('nis')->toArray();
        $existingNisn = Murid::whereNotNull('nisn')->pluck('nisn')->toArray();
        $kelasCache = Kelas::all()->pluck('id', 'nama_kelas')->toArray(); // e.g. ["X RPL 1" => 1]
        
        // Dynamic lookups for case-insensitive class name matches
        $kelasCacheLower = [];
        foreach ($kelasCache as $namaKelas => $id) {
            $kelasCacheLower[strtolower(trim($namaKelas))] = $id;
        }

        $startIndex = $hasHeader ? 1 : 0;
        $rowCount = count($rows);

        // Keep track of duplicates within the uploaded file
        $seenNis = [];
        $seenNisn = [];

        for ($i = $startIndex; $i < $rowCount; $i++) {
            $row = $rows[$i];

            // Ignore empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Map columns (NIS, NISN, Nama Lengkap, Jenis Kelamin, Nama Kelas, Angkatan)
            $nis = isset($row[0]) ? trim((string)$row[0]) : '';
            $nisn = isset($row[1]) ? trim((string)$row[1]) : '';
            $namaLengkap = isset($row[2]) ? trim((string)$row[2]) : '';
            $jenisKelamin = isset($row[3]) ? strtoupper(trim((string)$row[3])) : '';
            $namaKelasInput = isset($row[4]) ? trim((string)$row[4]) : '';
            $angkatan = isset($row[5]) ? intval(trim((string)$row[5])) : 0;

            $rowErrors = [];
            $kelasId = null;

            // 1. Validasi NIS
            if (empty($nis)) {
                $rowErrors[] = "NIS tidak boleh kosong.";
            } elseif (in_array($nis, $existingNis)) {
                $rowErrors[] = "NIS '$nis' sudah terdaftar di sistem.";
            } elseif (in_array($nis, $seenNis)) {
                $rowErrors[] = "NIS '$nis' ganda di dalam file Excel.";
            } else {
                $seenNis[] = $nis;
            }

            // 2. Validasi NISN (optional tapi harus unik jika diisi)
            if (!empty($nisn)) {
                if (in_array($nisn, $existingNisn)) {
                    $rowErrors[] = "NISN '$nisn' sudah terdaftar di sistem.";
                } elseif (in_array($nisn, $seenNisn)) {
                    $rowErrors[] = "NISN '$nisn' ganda di dalam file Excel.";
                } else {
                    $seenNisn[] = $nisn;
                }
            }

            // 3. Validasi Nama Lengkap
            if (empty($namaLengkap)) {
                $rowErrors[] = "Nama Lengkap tidak boleh kosong.";
            }

            // 4. Validasi Jenis Kelamin
            if (empty($jenisKelamin)) {
                $rowErrors[] = "Jenis Kelamin tidak boleh kosong.";
            } elseif ($jenisKelamin !== 'L' && $jenisKelamin !== 'P') {
                $rowErrors[] = "Jenis Kelamin harus 'L' atau 'P'.";
            }

            // 5. Validasi Kelas
            if (empty($namaKelasInput)) {
                $rowErrors[] = "Nama Kelas tidak boleh kosong.";
            } else {
                $kelasLower = strtolower($namaKelasInput);
                if (isset($kelasCacheLower[$kelasLower])) {
                    $kelasId = $kelasCacheLower[$kelasLower];
                } else {
                    $rowErrors[] = "Kelas '$namaKelasInput' tidak ditemukan di database.";
                }
            }

            // 6. Validasi Angkatan
            if (empty($angkatan)) {
                $rowErrors[] = "Tahun Angkatan tidak boleh kosong.";
            } elseif ($angkatan < 2000 || $angkatan > 2100) {
                $rowErrors[] = "Tahun Angkatan harus berada di rentang 2000 s.d. 2100.";
            }

            if (!empty($rowErrors)) {
                $isValidFile = false;
            }

            $previewData[] = [
                'row_number' => $i + 1,
                'nis' => $nis,
                'nisn' => $nisn ?: null,
                'nama_lengkap' => $namaLengkap,
                'jenis_kelamin' => $jenisKelamin,
                'nama_kelas' => $namaKelasInput,
                'kelas_id' => $kelasId,
                'angkatan' => $angkatan,
                'errors' => $rowErrors,
                'is_valid' => empty($rowErrors)
            ];
        }

        return [
            'isValid' => $isValidFile,
            'errors' => $globalErrors,
            'data' => $previewData
        ];
    }

    /**
     * Import students and create user accounts within a transaction.
     */
    public function import(array $data)
    {
        DB::beginTransaction();

        try {
            foreach ($data as $item) {
                if (empty($item['nis']) || empty($item['nama_lengkap']) || empty($item['kelas_id'])) {
                    throw new \Exception("Data tidak valid untuk diimport pada baris " . $item['row_number']);
                }

                // 1. Buat Akun User login (username: NIS, password default: NIS)
                $user = User::create([
                    'name' => $item['nama_lengkap'],
                    'username' => $item['nis'],
                    'password' => Hash::make($item['nis']),
                    'role' => 'murid',
                ]);

                // 2. Buat Data Murid
                Murid::create([
                    'nis' => $item['nis'],
                    'nisn' => $item['nisn'] ?: null,
                    'nama_lengkap' => $item['nama_lengkap'],
                    'jenis_kelamin' => $item['jenis_kelamin'],
                    'kelas_id' => $item['kelas_id'],
                    'angkatan' => $item['angkatan'],
                    'status' => 'aktif',
                    'user_id' => $user->id,
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

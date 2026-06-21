<?php

namespace App\Services;

use App\Models\Murid;
use App\Models\Semester;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ImportNilaiService
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

        // Hubungkan database untuk lookup cepat (caching sederhana)
        $muridCache = Murid::all()->pluck('id', 'nis')->toArray();
        $semesterCache = Semester::all()->pluck('id', 'semester_ke')->toArray();
        
        $mapelCacheKode = MataPelajaran::all()->pluck('id', 'kode_mapel')->toArray();
        $mapelCacheNama = MataPelajaran::all()->pluck('id', 'nama_mapel')->toArray();

        $startIndex = $hasHeader ? 1 : 0;
        $rowCount = count($rows);

        for ($i = $startIndex; $i < $rowCount; $i++) {
            $row = $rows[$i];
            
            // Hiraukan baris kosong
            if (empty(array_filter($row))) {
                continue;
            }

            // Map data kolom
            $nis = isset($row[0]) ? trim((string)$row[0]) : '';
            $semesterKe = isset($row[1]) ? intval(trim((string)$row[1])) : 0;
            $mapelInput = isset($row[2]) ? trim((string)$row[2]) : '';
            $nilaiInput = isset($row[3]) ? floatval(trim((string)$row[3])) : 0.0;

            $rowErrors = [];
            $muridId = null;
            $semesterId = null;
            $mapelId = null;
            $namaLengkap = '-';
            $namaMapel = '-';

            // 1. Validasi NIS
            if (empty($nis)) {
                $rowErrors[] = "NIS tidak boleh kosong.";
            } elseif (!isset($muridCache[$nis])) {
                $rowErrors[] = "Siswa dengan NIS '$nis' tidak ditemukan.";
            } else {
                $muridId = $muridCache[$nis];
                $namaLengkap = Murid::find($muridId)->nama_lengkap;
            }

            // 2. Validasi Semester
            if (empty($semesterKe)) {
                $rowErrors[] = "Semester tidak boleh kosong.";
            } elseif (!isset($semesterCache[$semesterKe])) {
                $rowErrors[] = "Semester ke-$semesterKe tidak terdaftar di sistem.";
            } else {
                $semesterId = $semesterCache[$semesterKe];
            }

            // 3. Validasi Mata Pelajaran (bisa berdasarkan Kode atau Nama)
            if (empty($mapelInput)) {
                $rowErrors[] = "Mata pelajaran tidak boleh kosong.";
            } else {
                if (isset($mapelCacheKode[$mapelInput])) {
                    $mapelId = $mapelCacheKode[$mapelInput];
                    $namaMapel = MataPelajaran::find($mapelId)->nama_mapel;
                } elseif (isset($mapelCacheNama[$mapelInput])) {
                    $mapelId = $mapelCacheNama[$mapelInput];
                    $namaMapel = MataPelajaran::find($mapelId)->nama_mapel;
                } else {
                    // Cari case-insensitive fallback
                    $mapelDb = MataPelajaran::where('kode_mapel', 'LIKE', $mapelInput)
                        ->orWhere('nama_mapel', 'LIKE', $mapelInput)
                        ->first();
                    if ($mapelDb) {
                        $mapelId = $mapelDb->id;
                        $namaMapel = $mapelDb->nama_mapel;
                    } else {
                        $rowErrors[] = "Mata pelajaran '$mapelInput' tidak ditemukan.";
                    }
                }
            }

            // 4. Validasi Nilai
            if (!is_numeric($row[3])) {
                $rowErrors[] = "Nilai harus berupa angka.";
            } elseif ($nilaiInput < 0 || $nilaiInput > 100) {
                $rowErrors[] = "Nilai harus berada di rentang 0 s.d. 100.";
            }

            // 5. Cek Duplikasi Nilai dalam database
            if ($muridId && $semesterId && $mapelId) {
                $duplikatDb = Nilai::where('murid_id', $muridId)
                    ->where('semester_id', $semesterId)
                    ->where('mata_pelajaran_id', $mapelId)
                    ->exists();

                if ($duplikatDb) {
                    $rowErrors[] = "Nilai untuk murid ini pada semester & mapel tersebut sudah ada di database (Akan ditimpa jika dilanjutkan).";
                }
            }

            // Cek duplikasi di dalam file itu sendiri
            $uniqueKey = "{$nis}-{$semesterKe}-{$mapelId}";
            if (isset($previewData[$uniqueKey])) {
                $rowErrors[] = "Duplikasi baris di dalam file Excel.";
            }

            if (!empty($rowErrors)) {
                $isValidFile = false;
            }

            $previewData[$uniqueKey] = [
                'row_number' => $i + 1,
                'nis' => $nis,
                'nama_lengkap' => $namaLengkap,
                'semester_ke' => $semesterKe,
                'semester_id' => $semesterId,
                'murid_id' => $muridId,
                'mapel_input' => $mapelInput,
                'mapel_id' => $mapelId,
                'nama_mapel' => $namaMapel,
                'nilai' => $nilaiInput,
                'errors' => $rowErrors,
                'is_valid' => empty($rowErrors)
            ];
        }

        return [
            'isValid' => $isValidFile,
            'errors' => $globalErrors,
            'data' => array_values($previewData)
        ];
    }

    /**
     * Import the previewed data into the database within a transaction.
     */
    public function import(array $data)
    {
        DB::beginTransaction();

        try {
            foreach ($data as $item) {
                if (empty($item['murid_id']) || empty($item['semester_id']) || empty($item['mapel_id'])) {
                    throw new \Exception("Data tidak valid untuk diimport pada baris " . $item['row_number']);
                }

                // Update atau Create jika nilainya sudah ada
                Nilai::updateOrCreate(
                    [
                        'murid_id' => $item['murid_id'],
                        'semester_id' => $item['semester_id'],
                        'mata_pelajaran_id' => $item['mapel_id'],
                    ],
                    [
                        'nilai' => $item['nilai']
                    ]
                );
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Parse Class Grid Excel and return preview data with validation errors.
     */
    public function previewClassGrid($file)
    {
        $rows = Excel::toArray([], $file)[0] ?? [];
        
        if (empty($rows)) {
            return [
                'isValid' => false,
                'errors' => ['File Excel kosong atau tidak terbaca.'],
                'data' => []
            ];
        }

        // Header wajib ada di baris pertama
        $header = $rows[0];
        
        // Validasi header dasar
        if (!isset($header[0]) || !preg_match('/nis/i', $header[0])) {
            return [
                'isValid' => false,
                'errors' => ['Format file tidak sesuai. Header kolom pertama harus "NIS".'],
                'data' => []
            ];
        }

        // Cari mapel berdasarkan header mulai dari indeks ke-3
        $subjectsInHeader = [];
        $mapelCacheKode = MataPelajaran::all()->pluck('id', 'kode_mapel')->toArray();

        for ($col = 3; $col < count($header); $col++) {
            $subjectCode = trim((string)$header[$col]);
            if (empty($subjectCode)) {
                continue;
            }

            if (isset($mapelCacheKode[$subjectCode])) {
                $subjectsInHeader[$col] = [
                    'id' => $mapelCacheKode[$subjectCode],
                    'code' => $subjectCode,
                    'nama_mapel' => MataPelajaran::find($mapelCacheKode[$subjectCode])->nama_mapel
                ];
            } else {
                return [
                    'isValid' => false,
                    'errors' => ["Mata pelajaran dengan kode '$subjectCode' pada kolom header tidak terdaftar di sistem."],
                    'data' => []
                ];
            }
        }

        $previewData = [];
        $isValidFile = true;
        $globalErrors = [];

        // Caching lookups
        $muridCache = Murid::all()->pluck('id', 'nis')->toArray();
        $semesterCache = Semester::all()->pluck('id', 'semester_ke')->toArray();

        $rowCount = count($rows);

        for ($i = 1; $i < $rowCount; $i++) {
            $row = $rows[$i];
            
            // Hiraukan baris kosong
            if (empty(array_filter($row))) {
                continue;
            }

            $nis = isset($row[0]) ? trim((string)$row[0]) : '';
            $namaSiswa = isset($row[1]) ? trim((string)$row[1]) : '';
            $semesterKe = isset($row[2]) ? intval(trim((string)$row[2])) : 0;

            $rowErrors = [];
            $muridId = null;
            $semesterId = null;
            $namaLengkap = $namaSiswa ?: '-';

            // 1. Validasi NIS
            if (empty($nis)) {
                $rowErrors[] = "NIS tidak boleh kosong.";
            } elseif (!isset($muridCache[$nis])) {
                $rowErrors[] = "Siswa dengan NIS '$nis' tidak ditemukan.";
            } else {
                $muridId = $muridCache[$nis];
                $namaLengkap = Murid::find($muridId)->nama_lengkap;
            }

            // 2. Validasi Semester
            if (empty($semesterKe)) {
                $rowErrors[] = "Semester tidak boleh kosong.";
            } elseif (!isset($semesterCache[$semesterKe])) {
                $rowErrors[] = "Semester ke-$semesterKe tidak terdaftar di sistem.";
            } else {
                $semesterId = $semesterCache[$semesterKe];
            }

            // Masing-masing nilai mapel pada kolom
            $gradesMapped = [];
            foreach ($subjectsInHeader as $colIdx => $subj) {
                $nilaiRaw = isset($row[$colIdx]) ? trim((string)$row[$colIdx]) : '';
                
                // Jika kosong, abaikan (tidak diupdate/tidak diinput)
                if ($nilaiRaw === '') {
                    continue;
                }

                $nilaiVal = floatval($nilaiRaw);
                $colErrors = [];

                if (!is_numeric($nilaiRaw)) {
                    $colErrors[] = "Nilai harus berupa angka.";
                } elseif ($nilaiVal < 0 || $nilaiVal > 100) {
                    $colErrors[] = "Nilai harus berada di rentang 0 s.d. 100.";
                }

                if (!empty($colErrors)) {
                    $rowErrors = array_merge($rowErrors, array_map(function($err) use ($subj) {
                        return "Mapel {$subj['code']}: " . $err;
                    }, $colErrors));
                }

                $gradesMapped[] = [
                    'mapel_id' => $subj['id'],
                    'kode_mapel' => $subj['code'],
                    'nama_mapel' => $subj['nama_mapel'],
                    'nilai' => $nilaiVal
                ];
            }

            if (!empty($rowErrors)) {
                $isValidFile = false;
            }

            $previewData[] = [
                'row_number' => $i + 1,
                'nis' => $nis,
                'nama_lengkap' => $namaLengkap,
                'semester_ke' => $semesterKe,
                'semester_id' => $semesterId,
                'murid_id' => $muridId,
                'grades' => $gradesMapped,
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
     * Import the class grid previewed data into the database within a transaction.
     */
    public function importClassGrid(array $data)
    {
        DB::beginTransaction();

        try {
            foreach ($data as $item) {
                if (empty($item['murid_id']) || empty($item['semester_id'])) {
                    throw new \Exception("Data tidak valid untuk diimport pada baris " . $item['row_number']);
                }

                foreach ($item['grades'] as $grade) {
                    Nilai::updateOrCreate(
                        [
                            'murid_id' => $item['murid_id'],
                            'semester_id' => $item['semester_id'],
                            'mata_pelajaran_id' => $grade['mapel_id'],
                        ],
                        [
                            'nilai' => $grade['nilai']
                        ]
                    );
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

# Sistem Informasi Nilai Murid (SINM)

Sistem Informasi Nilai Murid (SINM) adalah aplikasi portal akademik berbasis web yang dirancang khusus untuk memfasilitasi sekolah dalam mengelola data nilai secara transparan, aman, dan efisien. Murid dapat melihat perkembangan nilai mereka secara privat dari semester 1 hingga semester 6, sedangkan administrator memiliki kontrol penuh atas pengelolaan data akademis.

---

## 🚀 Fitur Utama

### 1. Panel Administrator (Admin)
*   **Dashboard Statistik:** Menyajikan visualisasi data yang informatif seperti total murid, total kelas, total jurusan, sebaran nilai (Pie Chart), dan rata-rata nilai per kelas (Bar Chart).
*   **Leaderboard (Papan Peringkat):** Menampilkan 10 peringkat murid terbaik skala sekolah dan skala jurusan secara real-time.
*   **Manajemen Data Master (CRUD):** 
    *   Pengelolaan Jurusan
    *   Pengelolaan Kelas
    *   Pengelolaan Murid (disertai pembuatan akun login otomatis dan fitur **Import Murid via Excel**)
    *   Pengelolaan Mata Pelajaran (disertai pengaturan urutan tampilan mapel)
    *   Pengelolaan Semester & Tahun Ajaran
*   **Manajemen Nilai & Ranking:**
    *   Input, edit, dan hapus nilai murid per semester.
    *   **Import Nilai Massal (1 Kelas):** Fitur untuk mengunggah nilai satu kelas penuh untuk semua mata pelajaran langsung dalam satu file Excel (grid template).
    *   Papan Peringkat Semester untuk Kelas dan Jurusan (Paralel) beserta opsi ekspor ke **Excel** dan **PDF**.
    *   Cetak Rapor Semester PDF & Transkrip Nilai Lengkap PDF.
*   **Pengaturan Sistem Global:** Mengatur Nama Aplikasi (secara global pada Title, Header, Login), Teks Footer, Identitas Sekolah untuk KOP Surat PDF, dan tanda tangan Kepala Sekolah.

### 2. Panel Murid (Siswa)
*   **Profil Murid:** Informasi detail murid seperti NIS, NISN, Kelas, Jurusan, Angkatan, dan Status.
*   **Papan Statistik Nilai:** Menampilkan Total Nilai, Rata-rata Nilai, Ranking Kelas, dan Ranking Paralel Jurusan pada semester terpilih.
*   **Grafik Perkembangan (Line Chart):** Visualisasi tren rata-rata nilai murid dari semester ke semester (Semester 1 s.d. 6).
*   **Rincian Nilai:** Daftar mata pelajaran beserta nilai akademis pada semester aktif.
*   **Cetak Rapor & Transkrip Mandiri:** Mengunduh berkas PDF Rapor Semester dan PDF Transkrip Nilai Lengkap secara aman.

---

## 🛠️ Teknologi & Pustaka

*   **Core Framework:** Laravel 12 (PHP 8.2+)
*   **Database:** MySQL 8 / MariaDB
*   **Styling & UI:** Vanilla CSS + Bootstrap 5.3 (gaya premium glassmorphism & responsive layout)
*   **Icons:** FontAwesome 6 (Loaded via CDN)
*   **Interactive Tables:** jQuery DataTables (Loaded via CDN)
*   **Data Visualization:** Chart.js (Loaded via CDN)
*   **Excel Import/Export:** Laravel Excel (maatwebsite/excel)
*   **PDF Generation:** DomPDF (barryvdh/laravel-dompdf)

---

## 📋 Persyaratan Sistem

Pastikan perangkat Anda telah terinstall perangkat lunak berikut:
*   PHP >= 8.2
*   Composer >= 2.0
*   MySQL >= 8.0 / MariaDB >= 10.4
*   Ekstensi PHP wajib: `bcmath`, `ctype`, `fileinfo`, `gd`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip`.

---

## ⚙️ Cara Instalasi & Menjalankan Project Secara Lokal

1.  **Ekstrak atau Clone Source Code**
    Pastikan folder project diletakkan di direktori kerja Anda (misal `C:\xampp\htdocs\Nilai` atau `D:\Projek\Nilai`).

2.  **Konfigurasi Environment (`.env`)**
    Salin file `.env.example` menjadi `.env`:
    ```bash
    cp .env.example .env
    ```
    Buka file `.env` dan sesuaikan konfigurasi database Anda:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=db_sinm
    DB_USERNAME=root
    DB_PASSWORD=
    ```

3.  **Install Dependensi PHP (Composer)**
    Jalankan perintah berikut di Terminal / PowerShell pada root direktori project:
    ```bash
    composer install
    ```

4.  **Generate Application Key**
    ```bash
    php artisan key:generate
    ```

5.  **Migrasi Database & Seeder Data Testing**
    Jalankan migrasi database untuk membuat tabel sekaligus mengisi data simulasi (sekolah, jurusan, kelas, mapel, murid, nilai semester 1-5, dan pengaturan identitas default):
    ```bash
    php artisan migrate:fresh --seed
    ```

6.  **Jalankan Aplikasi**
    Mulai local development server Laravel:
    ```bash
    php artisan serve
    ```
    Buka browser Anda dan akses alamat: [http://localhost:8000](http://localhost:8000). Aplikasi akan mengarahkan Anda langsung ke halaman Login.

---

## 🔑 Akun Demo Pengujian (Kredensial Default)

Setelah berhasil menjalankan seeder database, Anda dapat menggunakan akun-akun simulasi berikut untuk menguji aplikasi:

### 1. Akun Administrator (Akses Penuh)
*   **Username:** `admin`
*   **Password:** `admin123`

### 2. Akun Murid (Contoh Kelas XII RPL 1)
*   **Username (NIS):** `22001` (Ahmad Fauzi)
*   **Password:** `siswa123`

*   **Username (NIS):** `22002` (Budi Santoso)
*   **Password:** `siswa123`

### 3. Akun Murid (Contoh Kelas XII DKV 1)
*   **Username (NIS):** `22007` (Gerry Ramadhan)
*   **Password:** `siswa123`

*Catatan: Seluruh password murid yang di-seed default adalah `siswa123`.*

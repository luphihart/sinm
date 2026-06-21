# Panduan Deployment - Shared Hosting (cPanel / Hostinger)

Dokumen ini menjelaskan langkah-langkah detail untuk melakukan deployment aplikasi **Sistem Informasi Nilai Murid (SINM)** pada shared hosting. 

Ada dua metode utama untuk mendeploy Laravel di shared hosting. Metode **Pemisahan Folder (Sangat Direkomendasikan)** memisahkan kode inti aplikasi (di luar `public_html`) dengan file publik (di dalam `public_html`) demi keamanan maksimal agar file konfigurasi sensitif seperti `.env` tidak dapat diakses secara langsung dari internet.

---

## 📌 Prasyarat Hosting
Sebelum memulai, pastikan shared hosting Anda memenuhi persyaratan berikut:
1.  **Versi PHP:** PHP 8.2 atau PHP 8.3 (Dapat diatur melalui menu *Select PHP Version* di cPanel).
2.  **Ekstensi PHP Aktif:** `bcmath`, `ctype`, `fileinfo`, `gd`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip`.
3.  **Database:** MySQL 8.0+ atau MariaDB 10.4+.
4.  **Akses File Manager & phpMyAdmin.**

---

## 📂 Struktur Folder Setelah Deployment (Rekomendasi Keamanan)

Struktur direktori pada akun hosting Anda akan terlihat seperti ini:

```text
/home/username/                 <-- Direktori Root Akun Hosting Anda
├── sinm_core/                  <-- Folder Kode Inti Laravel (Aman dari akses publik)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── vendor/
│   ├── .env                    <-- Berkas konfigurasi rahasia Anda
│   └── ...
└── public_html/                <-- Folder Publik Hosting (Web Root)
    ├── assets/
    ├── storage/                <-- Symlink ke direktori storage
    ├── index.php               <-- Berkas entri utama yang sudah dimodifikasi
    ├── .htaccess
    └── ...
```

---

## 🛠️ Langkah-Langkah Deployment

### Langkah 1: Persiapan Berkas di Komputer Lokal
1.  Buka terminal/command prompt pada direktori project lokal Anda.
2.  Jalankan perintah optimasi Laravel berikut untuk membersihkan cache konfigurasi lama dan membuat file cache baru agar mempercepat loading di shared hosting:
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```
3.  Hapus folder `storage` yang ada di dalam folder `public` lokal (jika ada):
    ```bash
    # Pada Windows PowerShell
    Remove-Item -Recurse -Force public/storage
    ```
4.  Kompresi seluruh file project ke dalam satu file ZIP (misal: `sinm.zip`). 
    > [!IMPORTANT]
    > **Jangan sertakan** folder `node_modules` (jika ada), folder `.git`, dan file `.env` lokal Anda ke dalam zip. Folder `vendor` sebaiknya diikutkan jika Anda tidak memiliki akses SSH di hosting untuk menjalankan `composer install`.

### Langkah 2: Mengunggah dan Mengekstrak Berkas di cPanel
1.  Login ke cPanel atau panel hosting Hostinger Anda.
2.  Buka **File Manager**.
3.  Masuk ke direktori root Anda (`/home/username/` di luar `public_html`).
4.  Buat folder baru bernama `sinm_core`.
5.  Masuk ke folder `sinm_core`, klik **Upload**, lalu unggah file `sinm.zip` yang telah Anda buat pada Langkah 1.
6.  Setelah proses upload selesai, klik kanan file `sinm.zip` lalu pilih **Extract**.
7.  Hapus file `sinm.zip` setelah diekstrak untuk menghemat kuota hosting.

### Langkah 3: Memindahkan Folder Publik
1.  Di dalam File Manager, masuk ke `/home/username/sinm_core/public/`.
2.  Pilih semua file dan folder yang ada di dalam folder `public/` tersebut (termasuk file `.htaccess` dan `index.php`), lalu klik tombol **Move** (Pindahkan).
3.  Ubah tujuan pemindahan direktori menjadi direktori web root hosting Anda, biasanya `/public_html/` atau `/domains/domainanda.com/public_html/`.
4.  Klik **Move Files**. Sekarang folder `public` di dalam `sinm_core` kosong, dan semua file publik berada langsung di bawah `public_html`.

### Langkah 4: Modifikasi Berkas `index.php`
Karena lokasi file bootstrap Laravel telah dipindahkan, kita harus mengarahkan berkas `index.php` yang berada di `public_html` ke folder `sinm_core`.

1.  Buka `/public_html/index.php`, klik **Edit**.
2.  Cari baris code pemuatan autoloader dan bootstrap (biasanya di baris 10-25 untuk Laravel 11/12).
3.  Ubah baris berikut:
    ```php
    // SEBELUMNYA:
    require __DIR__.'/../vendor/autoload.php';
    
    // MENJADI (Arahkan ke folder sinm_core):
    require __DIR__.'/../sinm_core/vendor/autoload.php';
    ```
4.  Ubah juga baris pemuatan bootstrap app:
    ```php
    // SEBELUMNYA:
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    // MENJADI (Arahkan ke folder sinm_core):
    $app = require_once __DIR__.'/../sinm_core/bootstrap/app.php';
    ```
5.  Klik **Save Changes** dan tutup editor.

### Langkah 5: Konfigurasi Database & Environment (`.env`)
1.  Di cPanel, cari menu **MySQL® Database Wizard** atau database manager Hostinger.
2.  Buat database baru (misal: `userhosting_db_sinm`).
3.  Buat user database baru (misal: `userhosting_admin`) beserta password yang aman.
4.  Berikan hak akses penuh (*All Privileges*) kepada user tersebut ke database yang baru dibuat.
5.  Kembali ke **File Manager**, masuk ke `/home/username/sinm_core/`.
6.  Cari berkas `.env.example`, ubah namanya (*Rename*) menjadi `.env`. Klik **Edit**.
7.  Sesuaikan pengaturan database Anda dengan detail database hosting yang baru Anda buat:
    ```env
    APP_NAME=SINM
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://nama-domain-anda.com

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=userhosting_db_sinm
    DB_USERNAME=userhosting_admin
    DB_PASSWORD=PasswordDatabaseAnda
    ```
8.  Klik **Save Changes**.

### Langkah 6: Impor Database (SQL)
1.  Ekspor database lokal Anda dari phpMyAdmin komputer lokal ke format berkas `.sql`.
2.  Di cPanel hosting, buka menu **phpMyAdmin**.
3.  Pilih nama database yang baru Anda buat (`userhosting_db_sinm`).
4.  Pilih tab **Import**, pilih file `.sql` lokal Anda, lalu klik **Go** atau **Import**.

### Langkah 7: Membuat Link Storage (Symlink) di Shared Hosting
Karena folder core Laravel dipisahkan, Anda harus membuat link simbolis (symlink) agar berkas publik di `public_html/storage` terhubung ke direktori penyimpanan file di `/sinm_core/storage/app/public`.

Karena shared hosting umumnya tidak menyediakan akses SSH terminal, gunakan salah satu metode berikut:

#### Metode A: Menggunakan Route Laravel (Paling Mudah)
1.  Buka berkas `/home/username/sinm_core/routes/web.php` menggunakan File Manager Editor.
2.  Tambahkan baris route berikut di paling bawah file:
    ```php
    Route::get('/generate-symlink', function () {
        // Hapus folder storage jika sudah terlanjur berupa folder biasa di public_html
        $target = public_path('storage');
        if (file_exists($target)) {
            if (is_link($target)) {
                unlink($target);
            } else {
                // Jika folder biasa, hapus secara rekursif
                array_map('unlink', glob("$target/*"));
                rmdir($target);
            }
        }
        
        // Jalankan perintah artisan link
        Artisan::call('storage:link');
        return 'Symlink storage berhasil dibuat!';
    });
    ```
3.  Buka browser Anda dan akses domain Anda: `https://nama-domain-anda.com/generate-symlink`.
4.  Setelah muncul pesan sukses, silakan hapus kembali baris route tersebut demi keamanan.

#### Metode B: Menggunakan Cron Job cPanel
1.  Cari menu **Cron Jobs** di panel cPanel Anda.
2.  Pada kolom waktu, pilih *Once Per Minute* atau gunakan interval pendek.
3.  Pada kolom command, jalankan perintah symlink linux berikut (sesuaikan `username` hosting Anda):
    ```bash
    ln -s /home/username/sinm_core/storage/app/public /home/username/public_html/storage
    ```
4.  Klik **Add New Cron Job**.
5.  Tunggu 1-2 menit hingga cron job tereksekusi sekali, lalu segera hapus cron job tersebut agar tidak terus berjalan setiap menit.

---

## 🔒 Tips Keamanan Tambahan

1.  **Matikan Debug Mode:**
    Pastikan `APP_DEBUG=false` selalu tersetting di dalam file `.env` produksi Anda. Menyalakan debug di server production dapat membocorkan kredensial database jika terjadi error.
2.  **Amankan File `.env`:**
    Dengan metode pemisahan folder di atas, file `.env` Anda sudah sangat aman karena letak fisiknya berada di luar web root `public_html`.
3.  **Hak Akses Folder (Permissions):**
    Di dalam direktori File Manager, pastikan permission untuk folder `/sinm_core/storage` dan `/sinm_core/bootstrap/cache` diatur ke `775` atau `755` agar server web dapat menulis file cache dan log.

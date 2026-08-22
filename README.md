# SIDATA NEPER (Sistem Informasi Data Terpadu Neper)

**SIDATA NEPER** adalah platform web Single Identity & Master Data Portal terintegrasi untuk **SMK Negeri 1 Cirebon**. Sistem ini dirancang untuk mempermudah sinkronisasi data Dapodik, pengelolaan data akademik (Siswa, Guru, Tendik, Rombel, Jurusan), verifikasi mandiri profil siswa, serta integrasi data dengan aplikasi eksternal sekolah via REST API & Webhook secara aman.

---

## ✨ Fitur Utama

### 1. Dashboard Statistik & Akademik
- Ringkasan data real-time jumlah Siswa, Guru & Tendik, Rombel, dan Jurusan.
- Distribusi tingkat kelas (X, XI, XII) dan jurusan interaktif.
- Detail detail siswa per rombel lengkap dengan status verifikasi.
- Panel pemantauan log audit aktivitas sistem terbaru.

### 2. Verifikasi Mandiri Data Siswa (Dapodik Alignment)
- Siswa dapat login menggunakan NIS dan melakukan pemeriksaan data profil mandiri.
- **Alur Verifikasi Aman**:
  - Jika data dikonfirmasi benar tanpa perubahan → status langsung terverifikasi.
  - Jika siswa melakukan perbaikan data → data disimpan dengan status **"Menunggu Validasi Admin"** dan tombol edit dikunci.
  - **Persetujuan Admin**: Admin dapat meninjau riwayat perubahan data siswa, lalu menyetujui (Approve) atau menolak (Reject) dengan menyertakan alasan.

### 3. Pengelolaan Periode Akademik
- Manajemen Tahun Ajaran (misal: `2026/2027`) beserta semester aktif (**Ganjil** & **Genap**).
- Mengatur status keaktifan periode sistem secara terpusat untuk memisahkan data transaksi per semester.

### 4. Ekspor-Impor Excel Dapodik Cerdas
- Mengimpor data siswa dan guru secara massal menggunakan format file bawaan Dapodik.
- **Dapodik Auto-Mapping**: Sistem secara otomatis mencocokkan siswa ke dalam Rombel dan Jurusan yang sesuai tanpa hardcoded ID.
- Mendukung fitur **Chunked Upload** untuk menangani pemrosesan data berukuran besar tanpa *timeout*.

### 5. Integrasi Eksternal (API & Webhooks)
- **REST API Terproteksi**: Dukungan autentikasi berbasis header `X-Client-ID` dan `X-Client-Secret` dengan izin berbasis *scope* (misal: `students.read`, `students.private`, `teachers.read`).
- **Dokumentasi API Interaktif**: Halaman panduan integrasi lengkap dengan contoh kode (fetch JS, PHP cURL, Python) langsung di panel admin.
- **Webhook Events**: Pengiriman event perubahan data secara real-time ke sistem/website sekolah eksternal lain saat profil diperbarui.

### 6. Audit Logging & Security
- Mencatat setiap aktivitas penting yang dilakukan oleh Admin, Guru, maupun Siswa (Log In, Log Out, Ubah Password, Reset Password, Ekspor/Impor, dan Validasi).

---

## 🛠️ Tech Stack
- **Framework**: Laravel 11.x / PHP 8.2+
- **Database**: MySQL 8.0+
- **Frontend**: Tailwind CSS / Vanilla CSS, Alpine.js, Blade Templates
- **Excel Processor**: PhpSpreadsheet

---

## 🚀 Panduan Instalasi Lokal

### 1. Clone Repositori
```bash
git clone https://github.com/vianferdian/bank-data.git
cd bank-data
```

### 2. Instal Dependensi
```bash
composer install
npm install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file `.env.example` ke `.env` dan sesuaikan kredensial database Anda:
```bash
cp .env.example .env
```
Pastikan pengaturan bahasa dan timezone sesuai:
```env
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_TIMEZONE=Asia/Jakarta
```

### 4. Setup Aplikasi
```bash
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

### 5. Jalankan Aplikasi
Jalankan dev server Laravel dan Vite secara bersamaan:
```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

---

## 👥 Pengguna Bawaan (Default Credentials)
Setelah melakukan seeding, Anda dapat masuk menggunakan akun berikut:
- **Admin**: Username: `admin` / Password: `Password123!`
- **Siswa / Guru**: Menggunakan NIS/NIP masing-masing, dengan password default **tanggal lahir format DDMMYYYY** (Contoh: `17081945` untuk kelahiran 17 Agustus 1945).

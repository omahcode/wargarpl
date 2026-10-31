# WargaRPL - Bank Data Siswa RPL & PPLG

**WargaRPL** adalah platform web Single Identity & Bank Data Portal terintegrasi yang diperuntukkan secara khusus untuk siswa dan siswi **Rekayasa Perangkat Lunak (RPL)** serta **Pengembangan Perangkat Lunak dan Gim (PPLG)** SMK Negeri 1 Cirebon.

Sistem ini dirancang untuk mempermudah sinkronisasi data Dapodik, manajemen data akademik warga RPL (Siswa, Guru Produktif, Rombel, Konsentrasi Keahlian), verifikasi mandiri profil siswa, serta integrasi data dengan aplikasi eksternal via REST API berbasis Bearer Token & Webhook secara aman.

---

## ✨ Fitur Utama

### 1. Khusus Siswa & Rombel RPL / PPLG
- Master data jurusan terfokus pada **PPLG** (Pengembangan Perangkat Lunak dan Gim) dan **RPL** (Rekayasa Perangkat Lunak).
- Validasi cerdas pada impor Dapodik: hanya memproses data siswa dari rombongan belajar RPL & PPLG.
- Rombel bawaan: Tingkat X PPLG, XI PPLG, dan XII RPL.

### 2. Integrasi REST API Berbasis Token (Bearer Token)
- **Token-Only Generation**: Pembuatan API Access langsung menghasilkan single token (format `wgr_live_...`) tanpa perlu konfigurasi ID & Secret ganda yang rumit.
- **Header Standar**:
  ```http
  Authorization: Bearer <TOKEN_WARGARPL>
  ```
  *(Mendukung juga header alternatif `X-API-Token: <TOKEN_WARGARPL>`)*.
- **Granular Scopes**: Izin akses fleksibel per token (`students.read`, `students.private`, `teachers.read`, `classes.read`).
- **Rate Limiting & Metrics**: Pembatasan request otomatis dan logging metrik realtime.

### 3. Dashboard Statistik Warga RPL
- Ringkasan data real-time jumlah Siswa (Warga RPL), Guru Produktif, Rombel, dan Konsentrasi Keahlian.
- Distribusi tingkat kelas (X, XI, XII) dan statistik gender.
- Panel pemantauan log audit aktivitas sistem terbaru.

### 4. Verifikasi Mandiri Data Siswa (Dapodik Alignment)
- Siswa dapat login menggunakan NIS dan melakukan pemeriksaan data profil mandiri.
- **Alur Verifikasi Aman**:
  - Jika data dikonfirmasi benar tanpa perubahan → status langsung terverifikasi.
  - Jika siswa melakukan perbaikan data → data disimpan dengan status **"Menunggu Validasi Admin"** dan tombol edit dikunci.
  - **Persetujuan Admin**: Admin dapat meninjau riwayat perubahan data siswa, lalu menyetujui (Approve) atau menolak (Reject) dengan alasan.

### 5. Ekspor-Impor Excel Dapodik
- Mengimpor data siswa dan guru secara massal menggunakan format file bawaan Dapodik.
- **Chunked Upload** untuk pemrosesan file besar tanpa timeout.

---

## 🛠️ Tech Stack
- **Backend Framework**: Laravel 12.x (PHP 8.3+)
- **Database**: MySQL 8.0+
- **Frontend / Styling**: Tailwind CSS, Alpine.js, Blade Templates
- **Excel Processing**: PhpSpreadsheet

---

## 🚀 Panduan Instalasi Lokal

### 1. Clone Repositori
```bash
git clone https://github.com/vianferdian/bank-data.git wargarpl
cd wargarpl
```

### 2. Instal Dependensi
```bash
composer install
npm install
npm run build
```

### 3. Konfigurasi Lingkungan (`.env`)
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database:
```env
APP_NAME=WargaRPL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wargarpl
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Setup Database & Key
```bash
php artisan key:generate
php artisan migrate --seed
```

Saat seeding dijalankan, sistem akan otomatis men-generate **API Token WargaRPL** bawaan yang langsung dapat digunakan.

### 5. Jalankan Dev Server
```bash
php artisan serve
```

---

## 👥 Akun Bawaan (Default Credentials)
- **Super Admin**: Username `admin` / Password `Password123!`
- **Admin Jurusan RPL**: Username `adminrpl` / Password `Password123!`
- **Siswa / Guru**: NIS/NIP masing-masing, password default **tanggal lahir format DDMMYYYY** (Contoh: `17081945`).

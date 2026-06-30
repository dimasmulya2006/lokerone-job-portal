# LokerOne

LokerOne adalah platform pencarian kerja modern dan terpercaya di Indonesia yang menghubungkan talenta terbaik dengan perusahaan-perusahaan terkemuka. Dibangun dengan fokus pada kecepatan, kemudahan penggunaan, dan antarmuka pengguna (UI) yang dinamis, bersih, serta profesional.

## Fitur Utama

### Untuk Pencari Kerja (Pelamar)
- **Cari & Filter Lowongan**: Pencarian cepat berdasarkan kata kunci, lokasi, dan kategori dengan fitur auto-scroll yang cerdas.
- **Pencarian Populer**: Akses instan ke kategori pekerjaan yang paling banyak dicari (Frontend, Data, Marketing, dll).
- **Profil & Lamaran Saya**: Lacak status semua lamaran kerja Anda (Menunggu, Direview, Diterima, Ditolak) secara real-time.
- **Gaji & Karier**: Lihat rata-rata statistik gaji per industri berdasarkan data terkini, dilengkapi tips karier profesional.

### Untuk Perusahaan (Employer)
- **Dashboard Perusahaan Khusus**: Kelola lowongan kerja dan pantau statistik pelamar di satu tempat terpusat.
- **Buat Lowongan Baru**: Publikasikan lowongan kerja baru dengan mudah, termasuk detail gaji (bisa disembunyikan/ditampilkan) dan tag lokasi (remote/on-site).
- **Manajemen Pelamar (Applicant Tracking)**: Lihat profil pelamar, baca cover letter, dan ubah status lamaran mereka dalam satu klik.
- **Profil Perusahaan & Upload Logo**: Personalisasi halaman perusahaan publik dengan deskripsi, lokasi, website, hingga fitur **Upload Logo Perusahaan**.

## Teknologi yang Digunakan

- **Backend**: PHP 8.1+ (Native dengan PDO)
- **Database**: MySQL (Relational Database)
- **Frontend / Styling**: Tailwind CSS (via CDN untuk pengembangan cepat), HTML5, Vanilla JavaScript.
- **Desain UI/UX**: Pendekatan Glassmorphism, animasi interaktif (hover/micro-interactions), dan desain berbasis komponen.

## Cara Instalasi & Menjalankan (Local Development)

Proyek ini dirancang agar sangat mudah dijalankan menggunakan environment lokal seperti **Laragon** atau XAMPP.

### 1. Persiapan Database
1. Buat database baru di MySQL dengan nama `lokerone`.
2. Anda dapat membuat tabel-tabel utamanya melalui skrip PHP atau melakukan *import* struktur tabel jika tersedia. Pastikan tabel-tabel berikut ada:
   - `users` (id, NAME, email, PASSWORD, role, company_id)
   - `companies` (id, NAME, logo_initial, logo_path, color, description, location, website)
   - `jobs` (id, company_id, title, description, requirements, category, TYPE, location_key, location_label, STATUS)
   - `applications` (id, job_id, user_id, full_name, email, phone, cover_letter, STATUS)

### 2. Menjalankan Aplikasi
Kami telah menyediakan skrip *wrapper* ringan agar Anda tidak perlu memindahkan file ke folder `htdocs` atau `www`.
Cukup jalankan perintah berikut di dalam terminal/command prompt pada direktori proyek:

```bash
php run.php
```

Perintah di atas akan secara otomatis menyalakan *built-in server* PHP di `http://localhost:8000`.

### 3. Login Akun Demo (Jika sudah diisi Dummy Data)
- **Admin Perusahaan (Contoh)**: 
  - Email: `adminperusahaan@gmail.com`
  - Password: `(Gunakan password default yang di-hash di DB)`
- **Pelamar (Contoh)**:
  - Email: `admin@gmail.com`

*Catatan: Sangat disarankan untuk mencoba membuat akun baru melalui halaman **Daftar Akun** agar dapat mencoba alur lengkap pendaftaran Perusahaan (Company) maupun Pencari Kerja (User).*

---
Dibuat dengan ❤️ untuk kemajuan karier masa depanmu.
# lokerone-job-portal

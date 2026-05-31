# PANDUAN LENGKAP INSTALASI LARAVEL PATANI

## 📋 DAFTAR ISI
1. [Persyaratan Sistem](#persyaratan-sistem)
2. [Instalasi Laravel](#instalasi-laravel)
3. [Setup Database](#setup-database)
4. [Konfigurasi Environment](#konfigurasi-environment)
5. [Struktur Project](#struktur-project)
6. [Cara Menjalankan](#cara-menjalankan)
7. [Login Credentials](#login-credentials)
8. [Fitur-Fitur Aplikasi](#fitur-fitur-aplikasi)

---

## 🔧 PERSYARATAN SISTEM

### Software yang Dibutuhkan:
- PHP >= 8.1
- Composer
- MySQL >= 5.7 atau MariaDB
- XAMPP (untuk Windows) atau LAMP/MAMP (untuk Mac/Linux)
- Git (opsional)

### Extension PHP yang Diperlukan:
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo

---

## 📦 INSTALASI LARAVEL

### Langkah 1: Install Laravel Fresh
```bash
# Buka terminal/command prompt
cd C:\xampp\htdocs

# Install Laravel menggunakan Composer
composer create-project laravel/laravel patani

cd patani
```

### Langkah 2: Copy File Project
Setelah Laravel terinstall, copy semua file dari folder `laravel-patani` ke folder `patani` yang baru dibuat.

```
laravel-patani/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   └── RegisterController.php
│   │   │   ├── Admin/
│   │   │   │   ├── AdminDashboardController.php
│   │   │   │   ├── PetaniController.php
│   │   │   │   ├── SawahController.php
│   │   │   │   ├── LaporanController.php
│   │   │   │   ├── PengaturanController.php
│   │   │   │   └── ForumController.php
│   │   │   ├── Petani/
│   │   │   │   ├── SawahController.php
│   │   │   │   ├── PrediksiController.php
│   │   │   │   ├── PerawatanController.php
│   │   │   │   ├── CuacaController.php
│   │   │   │   ├── ChatbotController.php
│   │   │   │   ├── RiwayatController.php
│   │   │   │   └── ForumController.php
│   │   │   └── DashboardController.php
│   │   └── Middleware/
│   │       └── CheckRole.php
│   └── Models/
│       ├── User.php
│       ├── Sawah.php
│       ├── Perawatan.php
│       ├── RiwayatPanen.php
│       ├── PrediksiPanen.php
│       ├── ForumTopic.php
│       └── ForumModels.php (berisi ForumReply, ForumLike, Cuaca, ChatbotConversation, Pengaturan)
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_users_table.php
│   │   ├── 2024_01_01_000002_create_sawah_table.php
│   │   ├── 2024_01_01_000003_create_perawatan_table.php
│   │   ├── 2024_01_01_000004_create_riwayat_panen_table.php
│   │   ├── 2024_01_01_000005_create_prediksi_panen_table.php
│   │   ├── 2024_01_01_000006_create_forum_tables.php
│   │   ├── 2024_01_01_000007_create_cuaca_table.php
│   │   ├── 2024_01_01_000008_create_chatbot_conversations_table.php
│   │   └── 2024_01_01_000009_create_pengaturan_table.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── UserSeeder.php
│   │   └── SawahSeeder.php
│   └── patani_database.sql (SQL dump lengkap)
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   ├── dashboard.blade.php
│       │   └── admin.blade.php
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── dashboard/
│       │   ├── index.blade.php
│       │   ├── sawah.blade.php
│       │   ├── prediksi.blade.php
│       │   ├── perawatan.blade.php
│       │   ├── cuaca.blade.php
│       │   ├── chatbot.blade.php
│       │   ├── forum.blade.php
│       │   └── riwayat.blade.php
│       ├── admin/
│       │   ├── index.blade.php
│       │   ├── petani.blade.php
│       │   ├── sawah.blade.php
│       │   ├── laporan.blade.php
│       │   ├── pengaturan.blade.php
│       │   └── forum.blade.php
│       └── landing/
│           └── index.blade.php
└── routes/
    └── web.php
```

---

## 🗄️ SETUP DATABASE

### Metode 1: Menggunakan phpMyAdmin (RECOMMENDED)

1. **Buka XAMPP Control Panel**
   - Start Apache
   - Start MySQL

2. **Buka phpMyAdmin**
   - Browser: `http://localhost/phpmyadmin`
   
3. **Import Database**
   - Klik tab "Import"
   - Pilih file `database/patani_database.sql`
   - Klik "Go"

Database `patani_db` akan otomatis dibuat dengan semua tabel dan data sample.

### Metode 2: Menggunakan MySQL Command Line

```bash
mysql -u root -p
```

```sql
SOURCE C:/xampp/htdocs/patani/database/patani_database.sql;
```

### Metode 3: Menggunakan Laravel Migration & Seeder

```bash
# Pastikan database patani_db sudah dibuat
php artisan migrate:fresh --seed
```

---

## ⚙️ KONFIGURASI ENVIRONMENT

### Edit file `.env`

```env
APP_NAME=PATANI
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost/patani/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=patani_db
DB_USERNAME=root
DB_PASSWORD=
```

### Generate Application Key

```bash
php artisan key:generate
```

### Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## 🚀 CARA MENJALANKAN

### Menggunakan PHP Built-in Server

```bash
cd C:\xampp\htdocs\patani
php artisan serve
```

Akses: `http://localhost:8000`

### Menggunakan XAMPP

1. Pastikan Apache sudah running
2. Akses: `http://localhost/patani/public`

**PENTING:** Jika menggunakan XAMPP, pastikan DocumentRoot mengarah ke folder `public`

---

## 🔑 LOGIN CREDENTIALS

### Admin
- **Email:** admin@patani.com
- **Password:** password

### Petani 1
- **Email:** ahmad@patani.com
- **Password:** password

### Petani 2
- **Email:** budi@patani.com
- **Password:** password

### Petani 3
- **Email:** siti@patani.com
- **Password:** password

---

## 🎯 FITUR-FITUR APLIKASI

### Dashboard Petani
1. **Dashboard Utama**
   - Statistik lahan
   - Status tanaman
   - Grafik produksi
   - Info cuaca real-time

2. **Data Sawah**
   - CRUD data sawah
   - Monitoring kondisi tanah & air
   - Tracking fase tanam
   - Estimasi panen

3. **Prediksi Panen**
   - Algoritma prediksi hasil panen
   - Analisis faktor-faktor pertumbuhan
   - Rekomendasi tindakan

4. **Perawatan**
   - Jadwal pemupukan
   - Jadwal penyemprotan
   - Tracking biaya perawatan
   - History kegiatan

5. **Info Cuaca**
   - Data cuaca real-time
   - Prediksi cuaca 7 hari
   - Alert cuaca ekstrem

6. **Chatbot AI**
   - Konsultasi pertanian
   - Tips & trik budidaya
   - Diagnosa hama & penyakit

7. **Forum Diskusi**
   - Berbagi pengalaman
   - Tanya jawab sesama petani
   - Kategori topik terorganisir

8. **Riwayat Panen**
   - Record hasil panen
   - Analisis produktivitas
   - Laporan pendapatan

### Dashboard Admin
1. **Dashboard Admin**
   - Statistik keseluruhan
   - Monitoring aktivitas
   - Grafik pertumbuhan

2. **Manajemen Petani**
   - CRUD data petani
   - Monitoring status petani
   - Detail profil & lahan

3. **Manajemen Sawah**
   - Overview semua sawah
   - Monitoring produktivitas
   - Analisis regional

4. **Laporan**
   - Laporan produksi
   - Laporan keuangan
   - Export ke Excel/PDF

5. **Pengaturan**
   - Konfigurasi sistem
   - Manajemen role & permission
   - Backup & restore

6. **Moderasi Forum**
   - Monitor diskusi
   - Hapus konten tidak sesuai
   - Pin topik penting

---

## 📊 DATABASE SCHEMA (ERD)

```
users
├── id (PK)
├── name
├── email (unique)
├── password
├── role (admin/petani)
├── nik
├── desa
├── kecamatan
└── status

sawah
├── id (PK)
├── user_id (FK → users)
├── nama_sawah
├── lokasi
├── luas
├── jenis_padi
├── tanggal_tanam
├── estimasi_panen
├── kondisi_tanah
├── fase_tanam
└── status

perawatan
├── id (PK)
├── sawah_id (FK → sawah)
├── tanggal
├── jenis_perawatan
├── nama_kegiatan
├── bahan_digunakan
└── biaya

riwayat_panen
├── id (PK)
├── sawah_id (FK → sawah)
├── tanggal_panen
├── hasil_panen
├── kualitas
├── harga_jual
└── total_pendapatan

forum_topics
├── id (PK)
├── user_id (FK → users)
├── title
├── content
├── category
├── views
└── likes

forum_replies
├── id (PK)
├── topic_id (FK → forum_topics)
├── user_id (FK → users)
└── content
```

---

## 🛠️ TROUBLESHOOTING

### Error: "Class not found"
```bash
composer dump-autoload
```

### Error: Database connection
1. Pastikan MySQL sudah running
2. Cek kredensial di file `.env`
3. Pastikan database `patani_db` sudah dibuat

### Error: "Route not defined"
```bash
php artisan route:clear
php artisan config:clear
```

### Error: Permission denied
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

---

## 📝 CATATAN PENTING

1. **Middleware Role:** Sistem menggunakan middleware `CheckRole` untuk memisahkan akses admin dan petani
2. **Soft Deletes:** Beberapa tabel menggunakan soft delete untuk data historis
3. **File Upload:** Untuk production, konfigurasi file upload di `config/filesystems.php`
4. **API Integration:** Untuk cuaca real-time, integrate dengan weather API (OpenWeatherMap, dll)

---

## 📞 SUPPORT

Jika ada pertanyaan atau kendala:
- Email: support@patani.com
- GitHub Issues: [Create Issue]
- Documentation: [Wiki]

---

**Selamat menggunakan PATANI! 🌾**

Developed with ❤️ for Indonesian Farmers

# SIVAST - Sistem Informasi Verifikasi SPJ Online
## BKPSDM Kabupaten Bandung

Aplikasi web untuk mengelola proses verifikasi Surat Pertanggungjawaban (SPJ) secara online dengan fitur lengkap meliputi master data, verifikasi dokumen, realisasi anggaran, dan dashboard monitoring.

## Fitur Utama

### 1. Dashboard
- Overview statistik per bidang
- Rekapitulasi pengumpulan SPJ
- Monitoring real-time status verifikasi
- Card informasi untuk 5 bidang (Sekretariat, PKPA, PPIK, DIKLAT, MPASN)

### 2. Master Data
- CRUD data master lengkap
- Export/Import Excel
- Template download
- Print PDF
- Filter dan pencarian data

### 3. Verifikasi SPJ
- Proses verifikasi dokumen SPJ
- Notifikasi email otomatis ke PPTK dan Bendahara
- Status tracking (Lengkap/Belum Lengkap)
- Export dan import data

### 4. Hasil Verifikasi
- Halaman terpisah untuk 5 bidang
- Filter otomatis berdasarkan bidang
- View-only access untuk user

### 5. Realisasi Anggaran
- Auto-populate dari master data
- Kalkulasi otomatis semester dan total
- Format rupiah
- Progress monitoring

### 6. Sistem Login & User Management
- Role-based access (Admin/User)
- Manajemen user
- Session management

### 7. Email Notification
- Setting SMTP Gmail
- Template email kustomisasi
- Auto-send notification

## Teknologi

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Server**: Apache (XAMPP)
- **Libraries**: 
  - Font Awesome (Icons)
  - PHPMailer (Email)
  - PhpSpreadsheet (Excel)
  - TCPDF (PDF)

## Instalasi di XAMPP

### Persyaratan Sistem
- XAMPP dengan PHP 7.4+ dan MySQL 5.7+ 
- Web browser modern (Chrome, Firefox, Safari, Edge)
- Koneksi internet untuk CDN dan email
- PHPMailer library (untuk fitur email)

### Langkah Instalasi

1. **Download dan Extract**
   ```
   Download aplikasi SIVAST
   Extract ke folder: C:/xampp/htdocs/sivast/
   ```

2. **Jalankan Installation Wizard**
   - Buka browser dan akses: http://localhost/sivast/install.php
   - Ikuti wizard instalasi 3 langkah:
     1. Konfigurasi database
     2. Install database dan tabel
     3. Selesai

   **ATAU Manual Setup Database:**
   - Buka phpMyAdmin (http://localhost/phpmyadmin)
   - Buat database baru dengan nama `sivast`
   - Import file SQL: `supabase/migrations/create_sivast_database.sql`
   - Atau jalankan script SQL yang ada di file tersebut

3. **Konfigurasi Database**
   - Edit file `config/database.php`
   - Sesuaikan pengaturan database jika perlu:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'sivast');
   ```

4. **Install PHPMailer (Untuk Email Notification)**
   ```
   Opsi 1: Download manual dari GitHub PHPMailer
   - Extract ke folder vendor/phpmailer/
   
   Opsi 2: Menggunakan Composer (Recommended)
   - composer require phpmailer/phpmailer
   ```

5. **Setup Folder dan Permission**
   ```
   - Pastikan folder uploads/ dapat ditulis
   - Pastikan folder assets/ dapat diakses
   - Buat folder temp/ untuk file sementara
   ```

6. **Setup Assets**
   - Upload gambar logo ke `assets/images/logo-bkpsdm.png`
   - Upload header image ke `assets/images/header-image.jpg`
   - Upload foto profil bidang ke folder `assets/images/`

7. **Akses Aplikasi**
   - Buka browser dan akses: http://localhost/sivast/
   - Login dengan akun default:
     - **Admin**: username `admin`, password `admin123`
     - **User**: username `user`, password `admin123`

8. **Ubah Password Default**
   - Login sebagai admin
   - Buka menu Manajemen User
   - Ubah password default untuk keamanan

## Cara Menggunakan

### Login Sistem
1. Buka browser dan akses: http://localhost/sivast/
2. Login dengan akun default:
   - **Admin**: username `admin`, password `admin123`
   - **User**: username `user`, password `admin123`

### Menggunakan Installation Wizard
1. Akses: http://localhost/sivast/install.php
2. Ikuti 3 langkah instalasi:
   - Step 1: Konfigurasi database
   - Step 2: Install database
   - Step 3: Selesai dan akses aplikasi

### Mengelola Profile
1. Klik menu Profile di header
2. Update informasi nama user
3. Ubah password jika diperlukan

### Backup Database
1. Login sebagai admin
2. Buka menu Pengaturan
3. Pilih tab Backup
4. Klik tombol Download Backup

### Menggunakan Fitur Master Data
1. Login sebagai admin
2. Buka menu Master Data

## Setup Email Notification

### Install PHPMailer
```bash
# Menggunakan Composer (Recommended)
composer require phpmailer/phpmailer

# Atau download manual dari:
# https://github.com/PHPMailer/PHPMailer/releases
# Extract ke folder vendor/phpmailer/
```

### Konfigurasi Gmail SMTP

1. **Persiapan Gmail**
   - Login ke akun Gmail yang akan digunakan
   - Aktifkan 2-Factor Authentication
   - Generate App Password khusus untuk aplikasi

2. **Setting di Aplikasi**
   - Login sebagai admin
   - Buka menu "Setting Notifikasi"
   - Isi konfigurasi berikut:
   ```
   SMTP Host: smtp.gmail.com
   SMTP Port: 587
   SMTP Username: your-email@gmail.com
   SMTP Password: [App Password yang di-generate]
   From Email: your-email@gmail.com
   From Name: SIVAST - BKPSDM Kabupaten Bandung
   ```

3. **Template Email**
   - Customize template email sesuai kebutuhan
   - Gunakan placeholder: {bidang}, {nama_pptk}, {nama_bendahara}, dll.
   - Test pengiriman email

### Troubleshooting Email

**Error "Could not authenticate":**
- Pastikan menggunakan App Password, bukan password Gmail biasa
- Periksa username dan password

**Error "Connection refused":**
- Periksa koneksi internet
- Pastikan port 587 tidak diblok firewall

**Email masuk spam:**
- Tambahkan domain ke whitelist
- Setup SPF record jika menggunakan domain custom

## Troubleshooting Umum

**Error saat instalasi:**
- Pastikan XAMPP sudah running (Apache + MySQL)
- Periksa permission folder
- Pastikan database dapat diakses

**Error saat backup:**
- Pastikan folder temp/ dapat ditulis
- Periksa permission database

**File upload error:**
- Pastikan folder uploads/ dapat ditulis
- Periksa ukuran maksimal file upload di php.ini

**PHPMailer not found:**
- Install PHPMailer menggunakan Composer
- Atau download manual dan extract ke vendor/phpmailer/

## Struktur File
```
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   ├── master-data.js
│   │   ├── dashboard.js
│   │   └── [other-js-files]
│   └── images/
├── api/
│   ├── master-data.php
│   ├── verifikasi-spj.php
│   └── export-excel.php
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   └── sidebar.php
├── supabase/migrations/
│   └── create_sivast_database.sql
├── temp/
├── uploads/
├── vendor/phpmailer/
├── index.php
├── login.php
├── users.php
└── README.md
```

3. Dashboard menampilkan overview semua bidang
4. Card statistik menunjukkan status verifikasi dan realisasi anggaran

3. Gunakan tombol Export/Import untuk mengelola data massal
1. Menu Verifikasi SPJ untuk proses verifikasi
2. Data Sub Kegiatan dan Rekening Belanja otomatis dari Master Data
3. Pilih status "Lengkap" atau "Belum Lengkap"
3. View-only access untuk user biasa
4. Export dan print tersedia
2. Input nilai pagu dan realisasi per bulan
## Backup dan Restore

### Backup Otomatis
1. Login sebagai admin
2. Buka menu Pengaturan > Tab Backup
3. Klik "Download Backup"
4. File SQL akan terdownload otomatis

### Backup Manual
```sql
mysqldump -u root -p sivast > backup_sivast.sql
```

- Backup khusus folder `uploads/` yang berisi file upload user

### Restore
```sql
mysql -u root -p sivast < backup_sivast.sql
```

### Error Database Connection
- Periksa konfigurasi di `config/database.php`
- Pastikan MySQL service running
- Periksa username/password database

### Error File Upload
- Periksa permission folder `uploads/`
- Periksa setting `upload_max_filesize` di php.ini

### Performance Issues
- Optimize database dengan menjalankan `OPTIMIZE TABLE`
- Enable PHP OPCache
- Compress assets CSS/JS untuk production

## Keamanan

2. **HTTPS** - Gunakan SSL certificate untuk production
3. **Database Security** - Buat user database khusus dengan permission terbatas
4. **File Permission** - Set permission yang tepat untuk file dan folder
5. **Regular Update** - Update XAMPP dan dependency secara berkala
6. **Backup Rutin** - Lakukan backup database dan file secara regular

### Validasi Input
- Aplikasi sudah include validasi input dan sanitization
- Menggunakan prepared statement untuk mencegah SQL injection
- CSRF protection pada form-form penting

## Fitur Lengkap

### ✅ Dashboard
- Real-time statistics per bidang
- Card informasi dengan data dinamis
- Tabel rekapitulasi dengan color coding
- Auto-refresh monitoring

### ✅ Master Data (Admin Only)
- CRUD lengkap dengan modal forms
- Export Excel, Import Excel, Print PDF
- Template download untuk import
- Filter dan pencarian advanced

### ✅ Verifikasi SPJ (Admin Only)
- CRUD lengkap dengan validasi
- Auto-populate dropdown dari Master Data
- Email notification ke PPTK & Bendahara
- Export/Import/Print functionality

### ✅ Hasil Verifikasi (5 Bidang)
- Auto-filter per bidang
- View-only access untuk user
- Export dan print per bidang
- Filter multi-kriteria

### ✅ Realisasi Anggaran
- Auto-populate dari Master Data
- Edit pagu dan realisasi bulanan
- Kalkulasi otomatis semester dan total
- Format rupiah untuk semua nilai

### ✅ User Management (Admin Only)
- CRUD user dengan role control
- Password management
- Session security

### ✅ Email System
- Gmail SMTP integration
- Template customization
- Test email functionality
- Bulk notification system

### ✅ Settings & Profile
- User profile management
- System settings
- Database backup
- Activity monitoring

## Support

Untuk pertanyaan, bug report, atau request fitur:
- Dokumentasi lengkap tersedia di folder `docs/`
- Contact: IT Support BKPSDM Kabupaten Bandung
- Email: support@bkpsdm.bandungkab.go.id

## Lisensi

Aplikasi ini dikembangkan khusus untuk BKPSDM Kabupaten Bandung.
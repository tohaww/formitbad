# CustomFormBuilder — Platform Form Builder Mandiri (Jotform Clone)

[![Tests](https://img.shields.io/badge/PHPUnit-41%20Passed-emerald.svg)](https://laravel.com)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-rose.svg)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

Platform Form Builder mandiri (*Self-hosted*) modern yang dibangun dengan arsitektur tangguh berbasis **Laravel 12** dan antarmuka visual presisi terinspirasi dari **Jotform Form Builder Workspace**. 

Aplikasi ini dilengkapi autentikasi **Google OAuth 2.0**, sinkronisasi otomatis **Google Sheets API v4** (mendukung *Service Account* maupun *User Consent OAuth*), pengiriman webhook instan berverifikasi HMAC SHA-256, serta antrean latar belakang (*Asynchronous Queue Workers*).

---

## 🚀 Fitur Unggulan

### 1. Autentikasi Pengguna & Alur Navigasi Lengkap
* **Google OAuth 2.0 Social Login**: Masuk mudah menggunakan akun Google institusi maupun personal.
* **Mode Cepat Pengembang (Dev Login Bypass)**: Akses cepat dashboard lokal tanpa konfigurasi kredensial Google di awal.
* **Route Protection**: Middleware `auth` yang ketat untuk dashboard, dan rute publik terbuka untuk pengisian formulir (`/f/{slug}`).

### 2. Beranda Dashboard ("My Forms" Jotform-Style)
* **Pita Statistik Real-Time**: Menampilkan Total Formulir, Formulir Aktif, dan Total Respon Masuk.
* **Kartu Formulir & Filter Cepat**: Pencarian client-side instan berdasarkan judul atau slug.
* **Aksi Cepat**: *Edit Form*, *View Form*, *Submissions/Data*, dan tombol *Hapus Formulir* dengan **Pop-up Modal Modern di Tengah Layar (*Centered Modal Dialog*)**.
* **Tombol "+ Create Form"**: Membuat formulir baru dalam kondisi kanvas kosong polos (*clean slate*).

### 3. Form Builder Workspace UI
* **Top Navigation Bar Oranye**:
  * **Tab BUILD**: Kanvas editor form interaktif dengan container kartu kertas putih.
  * **Tab SETTINGS**: Pusat konfigurasi judul, status form, integrasi Google Sheets & Webhooks, dan kustomisasi Thank You Page.
  * **Tab PUBLISH**: Tautan publik siap bagikan (`/f/{slug}`) dan kode sematan *iFrame Embed*.
  * **Toggle Switch Preview**: Pratinjau interaktif langsung tanpa meninggalkan builder.
* **Floating Button & Left Drawer ("+ Add Element")**:
  * Arsitektur **Extensible Widget Registry** (`widgetRegistry.js`) memudahkan penambahan tipe pertanyaan baru.
  * Kategori **Basic**: Heading, Full Name, Email, Short Text, Long Text, Number, Phone, Dropdown, Single Choice (Radio), Multiple Choice (Checkbox), Date Picker, File Upload, dan Star Rating.
  * Kategori **Widgets**: Remote Data Dropdown (Google Sheets Proxy).
* **Right Properties Drawer**: Panel samping kanan dinamis untuk mengubah label, placeholder, status *required*, dan opsi konfigurasi khusus field yang aktif.
* **Live Drag & Drop Reordering**: Didukung oleh library SortableJS dengan auto-save persisten ke backend.

### 4. Integrasi Google Sheets Cerdas (Dua Metode)
* **Metode 1: Google Service Account Sentral**:
  * Menggunakan file kredensial JSON robot sistem (`storage/credentials/`).
  * Cocok untuk implementasi terpusat institusi.
* **Metode 2: OAuth User Consent (Gaya Jotform)**:
  * Pengguna cukup mengklik tombol **"Hubungkan dengan Google (OAuth)"**.
  * **Mode 1-Klik**: Sistem otomatis membuat file Google Spreadsheet baru di Google Drive pengguna (`[Judul Form] (Tanggapan)`) lengkap dengan baris header kolom.
  * **Mode Manual**: Menghubungkan ID spreadsheet yang sudah ada tanpa perlu share manual ke robot.
  * Auto-refresh access token di latar belakang saat queue berjalan.

### 5. Remote Data Proxy Engine
* Endpoint: `GET /api/public/forms/{slug}/fields/{field_id}/remote-options`.
* Membaca ribuan baris data referensi (misal: Data Dosen, Prodi, Wilayah) langsung dari Google Sheets.
* Dilengkapi **Cache Server (TTL 15 menit)** untuk menghemat kuota Google Sheets API.
* Server-side case-insensitive text filtering & paginasi dinamis.

### 6. Submissions Ingestion & Worker Asinkron
* Endpoint publik aman: `POST /api/public/forms/{slug}/submit`.
* **SyncToGoogleSheetJob**: Menulis data submission baru ke Google Sheets di latar belakang dengan mekanisme auto-retry eksponensial (3x).
* **DispatchWebhookJob**: Mengirim payload HTTP POST ke URL eksternal dengan signature `X-Webhook-Signature: sha256={hmac_hash}`.
* **Submissions Inbox**: Tabel riwayat pengisian formulir, status sinkronisasi, tombol *Resync* manual, dan ekspor ke **CSV**.

---

## 🛠️ Persyaratan Sistem

* **PHP**: Versi 8.2 atau lebih baru (dengan ekstensi `pdo`, `sqlite3` atau `pgsql`, `curl`, `mbstring`, `openssl`).
* **Composer**: Versi 2.x.
* **Database**: SQLite (default lokal) atau PostgreSQL 14+.
* **Web Server**: Apache, Nginx, atau bawaan `php artisan serve`.

---

## 📦 Panduan Instalasi & Menjalankan Lokal

### 1. Clone Repository
```bash
git clone https://github.com/USERNAME/REPO_NAME.git
cd REPO_NAME
```

### 2. Install Dependensi PHP
```bash
composer install
```

### 3. Salin Konfigurasi Environment
```bash
cp .env.example .env
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Jalankan Database Migration & Seeder
```bash
# Pastikan database/database.sqlite sudah ada jika menggunakan SQLite:
touch database/database.sqlite

php artisan migrate --seed
```

### 6. Konfigurasi Google OAuth (Opsional untuk Google Sign-In)
Buka file `.env` dan masukkan kredensial dari **[Google Cloud Console](https://console.cloud.google.com/)**:
```env
GOOGLE_CLIENT_ID=your_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```
*(Catatan: Anda dapat menggunakan tombol **"Masuk Cepat Pengembang (Bypass OAuth)"** di halaman `/login` jika ingin mencoba langsung tanpa setup Google Console).*

### 7. Jalankan Server Aplikasi
```bash
php artisan serve
```
Aplikasi dapat diakses di browser melalui: **`http://localhost:8000`**.

---

## 🧪 Menjalankan Automated Tests

Aplikasi dilengkapi test suite komprehensif (Unit & Feature Tests) yang mencakup seluruh alur User Journey, Database ORM, Integrasi Google Sheets, Caching Proxy, dan Webhooks:

```bash
php artisan test
```

Semua **41 feature test** diverifikasi lulus dengan 288 assertions.

---

## 📁 Struktur Direktori Utama

```text
├── app/
│   ├── Enums/
│   │   └── FieldType.php                 # Enum tipe elemen form
│   ├── Http/Controllers/
│   │   ├── Auth/                         # Google OAuth & Login controller
│   │   ├── DashboardController.php       # Beranda "My Forms"
│   │   ├── FormBuilderUIController.php   # Workspace Builder & Submissions
│   │   ├── FormController.php            # CRUD & Ekspor CSV
│   │   ├── FormFieldController.php       # Sinkronisasi susunan elemen
│   │   ├── FormIntegrationController.php # Integrasi Sheets & Webhook
│   │   └── PublicFormController.php      # Renderer publik /f/{slug}
│   ├── Jobs/
│   │   ├── SyncToGoogleSheetJob.php      # Antrean sinkronisasi Sheets
│   │   └── DispatchWebhookJob.php        # Antrean dispatch webhook
│   ├── Models/                           # User, Form, FormField, Submission
│   └── Services/Google/
│       └── GoogleSheetService.php        # Client Sheets v4 & OAuth helper
├── database/
│   ├── migrations/                       # Skema database UUID & relasi
│   └── seeders/                          # Data inisial pengujian
├── public/
│   └── js/widgetRegistry.js              # Extensible Widget Registry
├── resources/views/
│   ├── auth/login.blade.php              # Halaman Login
│   ├── dashboard/index.blade.php         # Beranda My Forms
│   ├── builder/
│   │   ├── index.blade.php               # Canvas Form Builder Workspace
│   │   └── submissions.blade.php         # Submissions Inbox & Resync
│   └── forms/public_form.blade.php       # Renderer Formulir Publik
└── tests/Feature/                        # Automated Feature Tests
```

---

## 🔒 Keamanan & Kebijakan Data
* File `.env`, file kredensial JSON (`storage/credentials/*.json`), serta file SQLite database lokal secara otomatis diabaikan oleh `.gitignore` untuk mencegah kebocoran kunci rahasia (*secrets*) ke repository publik.

---

## 📄 Lisensi
Platform ini dirilis di bawah lisensi [MIT License](LICENSE).

# Dokumen Desain Sistem & Arsitektur Perangkat Lunak
## Proyek: ngeKosYuk (Sistem Informasi Sewa Kos Berbasis Web)

**Kelompok 4 - Pemrograman Web Lanjut:**
* Miselea Aisya Saram
* Naufal Hisyam Attosy
* Miqdad Dzakiy Arroyan
* Rafael Putut Arli

---

## 1. Ringkasan & Arsitektur Tingkat Tinggi

### 1.1 Deskripsi Produk
**ngeKosYuk** adalah aplikasi web terpadu penyedia jasa pencarian, pengelolaan properti, dan transaksi sewa kamar kos. Sistem ini memusatkan interaksi antara **Admin**, **Pemilik Kos**, dan **Penyewa** dalam satu alur kerja transparan.

### 1.2 Tech Stack
* **Framework Backend:** Laravel 11 (PHP 8.2+) dengan arsitektur MVC (Model-View-Controller).
* **Database:** PostgreSQL (Cloud Instance di Neon.tech).
* **Template Engine:** Laravel Blade Templating.
* **Styling & UI:** Tailwind CSS (Responsive Grid, Mobile & Desktop Friendly).
* **Penyimpanan Berkas:** Local Storage / Public Disk (Khusus berkas `bukti_pembayaran` dan foto properti).

---

## 2. Panduan Desain Antarmuka (UI & Brand Identity)

Untuk menjaga konsistensi tampilan dari tahap *mockup* hingga implementasi Blade:

### 2.1 Skema Warna (*Color Palette*)
| Peran Warna | Hex Code | Tailwind Class | Penggunaan Utama |
| :--- | :--- | :--- | :--- |
| **Primary Blue** | `#1E40AF` | `bg-blue-800` / `text-blue-800` | Header, Sidebar, Tombol Aksi Utama, Navbar |
| **Secondary Teal** | `#0D9488` | `bg-teal-600` / `text-teal-600` | Badge status aktif, aksen ikon, tombol konfirmasi |
| **Light Blue Accent** | `#E0F2FE` | `bg-sky-100` | Latar belakang kartu (*card*), *hover state* |
| **Neutral Background** | `#F8FAFC` | `bg-slate-50` | Latar belakang halaman utama aplikasi |
| **Neutral Card** | `#FFFFFF` | `bg-white` | Kontainer form, kartu kamar, dan tabel data |
| **Text Primary** | `#0F172A` | `text-slate-900` | Judul, label penting, teks utama |
| **Text Secondary** | `#64748B` | `text-slate-500` | Deskripsi fasilitas, teks bantuan (*hint*) |

### 2.2 Tipografi & Komponen
* **Font Family:** Inter / Poppins (Sans-serif, bersih, dan modern).
* **Card Style:** Sudut membulat sedang (`rounded-xl`), bayangan lembut (`shadow-sm` hingga `shadow-md`), dan garis tepi halus (`border border-slate-200`).
* **Status Badges:**
  * `Menunggu` / `Pending`: Kuning / Amber (`bg-amber-100 text-amber-800`)
  * `Disetujui` / `Lunas`: Hijau / Emerald (`bg-emerald-100 text-emerald-800`)
  * `Ditolak` / `Gagal`: Merah / Rose (`bg-rose-100 text-rose-800`)

---

## 3. Matriks Peran Pengguna (*Role & Permissions*)

Sistem menerapkan kontrol akses berbasis peran (*Role-Based Access Control / RBAC*) dengan 3 tingkatan hak akses:

```
                  ┌────────────────────────┐
                  │      Auth / Login      │
                  └───────────┬────────────┘
                              │
          ┌───────────────────┼───────────────────┐
          ▼                   ▼                   ▼
     [ Admin ]         [ Pemilik Kos ]       [ Penyewa ]
   - Pantau Semua      - Kelola Kos          - Cari & Filter Kos
   - Kelola User       - Kelola Kamar        - Booking / Sewa
   - Monitoring Sewa   - Validasi Sewa       - Upload Bukti Bayar
   - Monitoring Bayar  - Validasi Bayar      - Riwayat Sewa
```

| ID Fitur | Modul / Fitur | Admin | Pemilik Kos | Penyewa | Publik (Tamu) |
| :--- | :--- | :---: | :---: | :---: | :---: |
| FR-01 - 03 | Registrasi, Login, Logout | ✅ | ✅ | ✅ | ✅ |
| FR-04 - 05 | Profil & Hak Akses Sesuai Role | ✅ | ✅ | ✅ | ❌ |
| FR-06 - 11 | Dashboard & Monitoring Menyeluruh | ✅ | ❌ | ❌ | ❌ |
| FR-12 - 16 | CRUD Data Kos & Kamar Miliknya | ❌ (Hanya lihat) | ✅ | ❌ | ❌ |
| FR-17 - 20 | Persetujuan Sewa & Verifikasi Pembayaran | ❌ (Hanya lihat) | ✅ | ❌ | ❌ |
| FR-22 - 26 | Cari, Filter (Putra/Putri/Campur), Detail Kos | ✅ | ✅ | ✅ | ✅ |
| FR-27 - 32 | Booking Kamar, Upload Bayar, Riwayat Sewa | ❌ | ❌ | ✅ | ❌ |

---

## 4. Struktur Basis Data & Hubungan Antar Model

### 4.1 Diagram Hubungan Entitas (ERD Relational Logic)

```
[users] 1 ─────────< M [kos] 1 ─────────< M [kamars]
  │                                           │ 1
  │                                           │
  │                                           ▼ M
  └───────────────────────────────< M [sewas]
                                        │ 1
                                        │
                                        ▼ 1..M
                                  [pembayarans]
```

### 4.2 Spesifikasi Skema Tabel

#### 1. Tabel `users`
Mewakili semua entitas akun di dalam sistem.
* `id` : `BIGSERIAL PRIMARY KEY`
* `name` : `VARCHAR(255)`
* `email` : `VARCHAR(255) UNIQUE`
* `password` : `VARCHAR(255)`
* `role` : `ENUM('admin', 'pemilik', 'penyewa') DEFAULT 'penyewa'`
* `created_at`, `updated_at` : `TIMESTAMP`

#### 2. Tabel `kos`
Menyimpan identitas properti indekos milik seorang mitra pemilik.
* `id` : `BIGSERIAL PRIMARY KEY`
* `pemilik_id` : `BIGINT NOT NULL, FK -> users(id) ON DELETE CASCADE`
* `nama_kos` : `VARCHAR(255)`
* `jenis_penghuni` : `ENUM('Putra', 'Putri', 'Campur')`
* `alamat` : `TEXT`
* `deskripsi` : `TEXT`
* `fasilitas` : `TEXT`
* `created_at`, `updated_at` : `TIMESTAMP`

#### 3. Tabel `kamars`
Menyimpan rincian unit ruangan di bawah properti kos tertentu.
* `id` : `BIGSERIAL PRIMARY KEY`
* `kos_id` : `BIGINT NOT NULL, FK -> kos(id) ON DELETE CASCADE`
* `nomor_kamar` : `VARCHAR(50)`
* `harga` : `DECIMAL(15, 2)`
* `status` : `VARCHAR(50) DEFAULT 'tersedia'` (Nilai: `tersedia`, `disewa`, `pemeliharaan`)
* `created_at`, `updated_at` : `TIMESTAMP`

#### 4. Tabel `sewas`
Mencatat kontrak pemesanan kamar yang diajukan oleh penyewa.
* `id` : `BIGSERIAL PRIMARY KEY`
* `kamar_id` : `BIGINT NOT NULL, FK -> kamars(id) ON DELETE CASCADE`
* `penyewa_id` : `BIGINT NOT NULL, FK -> users(id) ON DELETE CASCADE`
* `tanggal_mulai` : `DATE`
* `tanggal_selesai` : `DATE`
* `durasi` : `INT` (Jumlah bulan sewa)
* `total_harga` : `DECIMAL(15, 2)`
* `status` : `VARCHAR(50) DEFAULT 'menunggu'` (Nilai: `menunggu`, `disetujui`, `ditolak`, `selesai`)
* `created_at`, `updated_at` : `TIMESTAMP`

#### 5. Tabel `pembayarans`
Menyimpan transaksi pembayaran dan bukti transfer atas kontrak sewa tertentu.
* `id` : `BIGSERIAL PRIMARY KEY`
* `sewa_id` : `BIGINT NOT NULL, FK -> sewas(id) ON DELETE CASCADE`
* `tanggal_bayar` : `DATE`
* `jumlah` : `DECIMAL(15, 2)`
* `bukti_pembayaran` : `VARCHAR(255)` (Path berkas gambar)
* `status` : `VARCHAR(50) DEFAULT 'menunggu_verifikasi'` (Nilai: `menunggu_verifikasi`, `lunas`, `ditolak`)
* `created_at`, `updated_at` : `TIMESTAMP`

### 4.3 Pemetaan Relasi Model Eloquent Laravel

| Model File | Metode Relasi | Kode Definisi | Deskripsi |
| :--- | :--- | :--- | :--- |
| `User.php` | `kos()` | `$this->hasMany(Kos::class, 'pemilik_id');` | 1 Pemilik memiliki banyak properti kos. |
| `User.php` | `sewas()` | `$this->hasMany(Sewa::class, 'penyewa_id');` | 1 Penyewa memiliki banyak riwayat sewa. |
| `Kos.php` | `pemilik()` | `$this->belongsTo(User::class, 'pemilik_id');` | Properti kos merujuk ke 1 akun pemilik. |
| `Kos.php` | `kamars()` | `$this->hasMany(Kamar::class, 'kos_id');` | 1 Properti kos memiliki banyak kamar. |
| `Kamar.php` | `kos()` | `$this->belongsTo(Kos::class, 'kos_id');` | Kamar terdaftar pada 1 properti kos. |
| `Kamar.php` | `sewas()` | `$this->hasMany(Sewa::class, 'kamar_id');` | Kamar tercatat di riwayat kontrak sewa. |
| `Sewa.php` | `kamar()` | `$this->belongsTo(Kamar::class, 'kamar_id');` | Transaksi sewa merujuk pada unit kamar spesifik. |
| `Sewa.php` | `penyewa()` | `$this->belongsTo(User::class, 'penyewa_id');` | Kontrak sewa dimiliki oleh 1 akun penyewa. |
| `Sewa.php` | `pembayarans()` | `$this->hasMany(Pembayaran::class, 'sewa_id');` | 1 Sewa bisa memiliki berkas bukti pembayaran. |
| `Pembayaran.php` | `sewa()` | `$this->belongsTo(Sewa::class, 'sewa_id');` | Bukti bayar terikat pada 1 ID transaksi sewa. |

---

## 5. Alur Transaksi Utama (State Transition)

### 5.1 Siklus Status Sewa & Pembayaran
```
[Penyewa: Checkout Kamar]
         │
         ▼
[Status Sewa: 'menunggu'] ──(Pemilik Tolak)──> [Status Sewa: 'ditolak']
         │
    (Pemilik Setujui)
         │
         ▼
[Status Sewa: 'disetujui']
         │
         ▼
[Penyewa Upload Bukti Transfer] ──> [Status Bayar: 'menunggu_verifikasi']
                                                 │
                                ┌────────────────┴────────────────┐
                                ▼                                 ▼
                     (Pemilik Verifikasi Sah)           (Pemilik Tolak Bukti)
                                │                                 │
                                ▼                                 ▼
                    [Status Bayar: 'lunas']            [Status Bayar: 'ditolak']
                    [Kamar: status = 'disewa']         (Penyewa diminta re-upload)
```

---

## 6. Desain Struktur File Laravel (Sitemap & MVC Routing)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php             # Login, Register, Logout, Profile
│   │   ├── Admin/
│   │   │   └── AdminDashboardController.php # Pantau user, kos, kamar, & bayar
│   │   ├── Pemilik/
│   │   │   ├── KosController.php          # CRUD Kos
│   │   │   ├── KamarController.php        # CRUD Kamar & atur status kamar
│   │   │   ├── PengajuanSewaController.php# Setujui / tolak pengajuan
│   │   │   └── VerifikasiBayarController.php # Konfirmasi pembayaran
│   │   └── Penyewa/
│   │       ├── JelajahKosController.php   # Katalog, cari, filter, detail
│   │       ├── SewaController.php         # Ajukan sewa kamar & riwayat
│   │       └── PembayaranController.php   # Upload berkas & status transaksi
│   └── Middleware/
│       └── RoleMiddleware.php             # Pengecekan role: admin, pemilik, penyewa
└── Models/
    ├── User.php
    ├── Kos.php
    ├── Kamar.php
    ├── Sewa.php
    └── Pembayaran.php

resources/views/
├── layouts/
│   ├── app.blade.php                      # Kerangka layout utama (Tailwind + Nav)
│   ├── navigation.blade.php               # Navigasi kondisional per role
│   └── footer.blade.php
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   └── profile.blade.php
├── admin/
│   └── dashboard.blade.php                # Statistik ringkasan platform
├── pemilik/
│   ├── dashboard.blade.php
│   ├── kos/ {index, create, edit}.blade.php
│   ├── kamar/ {index, create, edit}.blade.php
│   ├── pengajuan/ index.blade.php
│   └── pembayaran/ index.blade.php
├── penyewa/
│   ├── katalog.blade.php                  # Halaman cari & filter kos
│   ├── detail-kos.blade.php
│   ├── form-sewa.blade.php
│   ├── riwayat-sewa.blade.php
│   └── upload-pembayaran.blade.php
└── errors/
    ├── 403.blade.php                      # Unauthorized Access
    ├── 404.blade.php                      # Halaman Tidak Ditemukan
    └── 500.blade.php                      # Server Error Handler
```

---

## 7. Rencana Pengujian Black-Box (Test Matrix)

| No | Modul Skenario | Input Uji | Output yang Diharapkan |
| :---: | :--- | :--- | :--- |
| **1** | Autentikasi Pengguna | Registrasi akun baru dengan role `pemilik` & `penyewa`. | Akun tersimpan dengan role yang sesuai dan bisa diarahkan ke dashboard masing-masing. |
| **2** | Keamanan Middleware | Penyewa memasukkan URL `/admin/dashboard` secara paksa di URL browser. | Sistem menolak akses dan mengarahkan ke halaman `403 Forbidden` kustom. |
| **3** | Pengelolaan Properti | Pemilik menambah kamar baru pada kos miliknya. | Kamar muncul di daftar kamar kos tersebut dengan status awal `tersedia`. |
| **4** | Pencarian & Filter | Penyewa memfilter jenis kelamin: `Putri` dan lokasi: `Gedong Meneng`. | Hanya kos dengan tipe Putri di area tersebut yang tampil pada katalog. |
| **5** | Alur Booking Sewa | Penyewa klik "Sewa", menentukan tanggal & durasi 6 bulan. | Sistem menghitung `total_harga = harga * durasi`, status sewa menjadi `menunggu`. |
| **6** | Verifikasi Bayar | Penyewa mengunggah file bukti transfer `.jpg` / `.png`. | Pemilik dapat melihat berkas gambar, status bayar berubah jadi `lunas`, dan status kamar otomatis berubah menjadi `disewa`. |
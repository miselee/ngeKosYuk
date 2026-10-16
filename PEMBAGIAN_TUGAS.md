# Pembagian Tugas Project PWL — ngeKosYuK
**Kelompok:** Misele, Naufal, Miqdad, Rafael  

---

## Aturan Kolaborasi
1. Branch `main` tidak boleh langsung di-push.
2. Setiap anggota wajib membuat branch masing-masing dari `main`:
   ```bash
   git checkout -b feature/nama-fitur
   ```
3. Project ini hanya menyediakan struktur dasar Laravel. Setiap modul dan tabel dikerjakan sendiri oleh penanggung jawab sesuai pembagian di bawah.

---

## Pembagian Modul & Tugas

### 1. Rafael Putut Arli
**Fokus: Autentikasi, Akun & Layout Utama (FR-01 s/d FR-05)**

* **Backend:**
  - Update migration & model `users` (tambahkan kolom `role` dan `nomor_hp`).
  - Middleware hak akses `EnsureUserRole` untuk proteksi route per role (`admin`, `pemilik`, `penyewa`).
  - `AuthController`:
    - Registrasi akun (role: Pemilik Kos / Penyewa).
    - Login & logout multi-role.
    - Edit profil (nama, email, nomor HP, password).
* **Frontend:**
  - Layout utama Blade `layouts/app.blade.php` (navbar dinamis, footer).
  - Halaman login (`auth/login.blade.php`).
  - Halaman register (`auth/register.blade.php`).
  - Halaman edit profil (`profile/edit.blade.php`).

---

### 2. Misele Aisya Saram
**Fokus: Modul Pemilik Kos — Data Kos & Kamar (FR-12 s/d FR-16, FR-18)**

* **Backend:**
  - Migration & model tabel `kos`:
    - Kolom: `pemilik_id` (FK users), `nama_kos`, `jenis_penghuni`, `alamat`, `deskripsi`, `fasilitas`.
  - Migration & model tabel `kamars`:
    - Kolom: `kos_id` (FK kos), `nomor_kamar`, `harga`, `status` (tersedia/disewa).
  - Setup relasi antar model (`User` -> `Kos` -> `Kamar`).
  - `PemilikController`: dashboard ringkasan data kos milik pemilik.
  - `KosController`: CRUD properti kos.
  - `KamarController`: CRUD kamar, atur harga, dan atur ketersediaan kamar.
* **Frontend:**
  - Dashboard pemilik (`pemilik/dashboard.blade.php`).
  - Halaman kelola kos: list kos, form tambah & edit kos (`pemilik/kos/*`).
  - Halaman kelola kamar: list kamar, form tambah & edit kamar (`pemilik/kamar/*`).

---

### 3. Miqdad Dzakiy Arroyan
**Fokus: Modul Penyewa — Pencarian, Sewa & Pembayaran (FR-21 s/d FR-32)**

* **Backend:**
  - Migration & model tabel `sewas`:
    - Kolom: `kamar_id` (FK kamars), `penyewa_id` (FK users), `tanggal_mulai`, `tanggal_selesai`, `durasi`, `total_harga`, `status`.
  - Migration & model tabel `pembayarans`:
    - Kolom: `sewa_id` (FK sewas), `tanggal_bayar`, `jumlah`, `bukti_pembayaran`, `status`.
  - Setup relasi antar model (`Kamar` -> `Sewa` -> `Pembayaran`).
  - `PenyewaController`: katalog publik, pencarian nama/lokasi, filter jenis kos (Putra/Putri/Campur), detail kamar.
  - `SewaController`: form booking kamar, hitung total harga sewa, cek status pengajuan, riwayat sewa.
  - `PembayaranController`: upload file bukti transfer gambar ke storage.
* **Frontend:**
  - Halaman katalog & search/filter kos (`penyewa/katalog.blade.php`).
  - Halaman detail kos & ketersediaan kamar (`penyewa/detail.blade.php`).
  - Form pengajuan sewa & halaman upload bukti pembayaran (`penyewa/sewa.blade.php`, `penyewa/pembayaran.blade.php`).
  - Dashboard penyewa & riwayat sewa (`penyewa/dashboard.blade.php`).

---

### 4. Naufal Hisyam Attosy
**Fokus: Modul Admin, Manajemen User & Verifikasi Transaksi (FR-06 s/d FR-11, FR-17, FR-19, FR-20)**

* **Backend:**
  - `AdminController`:
    - Dashboard admin: rekap total user, total kos, total kamar, dan total transaksi.
    - Monitoring data kos, data kamar, serta riwayat sewa pengguna.
    - Verifikasi bukti pembayaran: setujui atau tolak bukti transfer dari penyewa.
  - `UserController` (Admin): CRUD kelola data seluruh pengguna.
  - `DatabaseSeeder`: buat dummy data akun admin, pemilik, penyewa, serta contoh kos untuk kebutuhan demo.
* **Frontend:**
  - Dashboard admin (`admin/dashboard.blade.php`).
  - Halaman kelola data user (`admin/users/*`).
  - Halaman monitoring kos, kamar, dan riwayat transaksi (`admin/monitoring/*`).
  - Halaman verifikasi bukti transfer (`admin/pembayaran/index.blade.php`).

---

## Matriks Rekap Tugas

| Anggota | Modul | Tanggung Jawab Utama |
|---|---|---|
| **Rafael** | Auth & Akun | Tabel `users`, `AuthController`, layout master Blade |
| **Misele** | Pemilik Kos | Tabel `kos` & `kamars`, CRUD kos & kamar, dashboard pemilik |
| **Miqdad** | Penyewa | Tabel `sewas` & `pembayarans`, alur sewa, upload bukti transfer |
| **Naufal** | Admin & Verifikasi | Dashboard admin, CRUD user, verifikasi bukti bayar, seeder |

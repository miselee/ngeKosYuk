# Struktur Folder Aset Gambar ngeKosYuk

Folder ini digunakan untuk menyimpan aset gambar statis aplikasi **ngeKosYuk** yang dipanggil melalui helper `asset('images/...')`.

## Pembagian Subfolder

1. **`logo/`**
   - Menyimpan logo resmi aplikasi (`logo-ngekosyuk.jpeg`).
   - Pemanggilan di Blade: `{{ asset('images/logo/logo-ngekosyuk.jpeg') }}`

2. **`kos/`**
   - Menyimpan foto-foto bangunan/properti kos statis atau foto default kos.
   - Pemanggilan di Blade: `{{ asset('images/kos/nama-file.jpg') }}`
   - *(Catatan: Foto kos yang diunggah dinamis melalui form tersimpan di `storage/app/public/foto_kos` dan diakses melalui `asset('storage/foto_kos/...')`)*.

3. **`kamar/`**
   - Menyimpan foto interior unit kamar kos statis atau placeholder kamar.
   - Pemanggilan di Blade: `{{ asset('images/kamar/nama-file.jpg') }}`
   - *(Catatan: Foto kamar unggahan dinamis tersimpan di `storage/app/public/foto_kamar`)*.

4. **`pembayaran/`**
   - Menyimpan aset visual terkait pembayaran (misalnya gambar QRIS statis atau panduan pembayaran).
   - *(Catatan: Bukti transfer yang diunggah penyewa tersimpan di `storage/app/public/bukti_pembayaran`)*.

5. **`banners/`**
   - Menyimpan gambar latar halaman utama (hero banner) dan ilustrasi halaman autentikasi (login/register).


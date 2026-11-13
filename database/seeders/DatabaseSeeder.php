<?php

namespace Database\Seeders;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pembayaran;
use App\Models\Sewa;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin Kos',
            'email' => 'admin@kos.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nomor_hp' => '081234567890',
        ]);

        $pemilik = User::create([
            'name' => 'Budi Pemilik',
            'email' => 'pemilik@kos.com',
            'password' => Hash::make('password'),
            'role' => 'pemilik',
            'nomor_hp' => '081234567891',
        ]);

        $penyewa = User::create([
            'name' => 'Andi Penyewa',
            'email' => 'penyewa@kos.com',
            'password' => Hash::make('password'),
            'role' => 'penyewa',
            'nomor_hp' => '081234567892',
        ]);

        $dataKos = [
            [
                'nama_kos' => 'Kos Harmoni Sejahtera',
                'alamat' => 'Jl. Merdeka No. 10, Malang',
                'deskripsi' => 'Kos nyaman dan bersih, dekat kampus dan pusat kota. Lingkungan aman dan tenang.',
                'tipe_kos' => 'campur',
                'foto' => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800',
            ],
            [
                'nama_kos' => 'Kos Mawar Indah',
                'alamat' => 'Jl. Soekarno-Hatta No. 25, Malang',
                'deskripsi' => 'Kos khusus putri dengan fasilitas lengkap. Cocok untuk mahasiswi.',
                'tipe_kos' => 'putri',
                'foto' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=800',
            ],
            [
                'nama_kos' => 'Kos Putra Jaya',
                'alamat' => 'Jl. Veteran No. 5, Malang',
                'deskripsi' => 'Kos putra strategis dekat kampus UB. Parkiran luas dan wifi cepat.',
                'tipe_kos' => 'putra',
                'foto' => 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?w=800',
            ],
            [
                'nama_kos' => 'Kos Griya Asri',
                'alamat' => 'Jl. Diponegoro No. 88, Malang',
                'deskripsi' => 'Kos eksklusif dengan desain modern minimalis. Kamar luas dan nyaman.',
                'tipe_kos' => 'campur',
                'foto' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800',
            ],
        ];

        $kosModels = [];
        foreach ($dataKos as $kos) {
            $kosModels[] = Kos::create(array_merge($kos, ['user_id' => $pemilik->id]));
        }

        $fasilitasOptions = [
            'Kasur, Lemari, Meja Belajar, WiFi',
            'Kasur, Lemari, AC, Kamar Mandi Dalam',
            'Kasur, Lemari, Meja Belajar, AC, WiFi, Kamar Mandi Dalam',
            'Kasur, Lemari, Kipas Angin, WiFi',
        ];

        $hargaOptions = [500000, 750000, 1000000, 1500000];
        $kamarIndex = 0;

        foreach ($kosModels as $kos) {
            $jumlahKamar = rand(2, 4);
            for ($i = 1; $i <= $jumlahKamar; $i++) {
                Kamar::create([
                    'kos_id' => $kos->id,
                    'nomor_kamar' => 'K-' . str_pad($kamarIndex + 1, 3, '0', STR_PAD_LEFT),
                    'harga_per_bulan' => $hargaOptions[array_rand($hargaOptions)],
                    'fasilitas' => $fasilitasOptions[array_rand($fasilitasOptions)],
                    'status' => 'tersedia',
                ]);
                $kamarIndex++;
            }
        }

        $kamarPertama = Kamar::first();
        $kamarKedua = Kamar::skip(3)->first();

        $sewa1 = Sewa::create([
            'user_id' => $penyewa->id,
            'kamar_id' => $kamarPertama->id,
            'kos_id' => $kamarPertama->kos_id,
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_selesai' => now()->addMonths(6)->format('Y-m-d'),
            'status' => 'disetujui',
            'catatan' => 'Saya mahasiswa semester 5, butuh kos dekat kampus.',
        ]);

        $kamarPertama->update(['status' => 'terisi']);

        $sewa2 = Sewa::create([
            'user_id' => $penyewa->id,
            'kamar_id' => $kamarKedua->id,
            'kos_id' => $kamarKedua->kos_id,
            'tanggal_mulai' => now()->subMonths(6)->format('Y-m-d'),
            'tanggal_selesai' => now()->subMonth()->format('Y-m-d'),
            'status' => 'selesai',
            'catatan' => null,
        ]);

        Pembayaran::create([
            'sewa_id' => $sewa1->id,
            'jumlah' => $kamarPertama->harga_per_bulan,
            'metode_pembayaran' => 'transfer_bank',
            'bukti_pembayaran' => null,
            'status' => 'diverifikasi',
            'tanggal_bayar' => now()->format('Y-m-d'),
            'keterangan' => 'Pembayaran bulan pertama.',
        ]);

        Pembayaran::create([
            'sewa_id' => $sewa1->id,
            'jumlah' => $kamarPertama->harga_per_bulan,
            'metode_pembayaran' => 'e_wallet',
            'bukti_pembayaran' => null,
            'status' => 'pending',
            'tanggal_bayar' => now()->addMonth()->format('Y-m-d'),
            'keterangan' => 'Pembayaran bulan kedua.',
        ]);

        Pembayaran::create([
            'sewa_id' => $sewa2->id,
            'jumlah' => $kamarKedua->harga_per_bulan,
            'metode_pembayaran' => 'tunai',
            'bukti_pembayaran' => null,
            'status' => 'diverifikasi',
            'tanggal_bayar' => now()->subMonths(5)->format('Y-m-d'),
            'keterangan' => 'Pembayaran sewa yang sudah selesai.',
        ]);

        Ulasan::create([
            'user_id' => $penyewa->id,
            'kos_id' => $kamarKedua->kos_id,
            'sewa_id' => $sewa2->id,
            'rating' => 5,
            'komentar' => 'Kos sangat nyaman dan bersih. Pemilik ramah dan responsif. Sangat direkomendasikan!',
        ]);

        Ulasan::create([
            'user_id' => $penyewa->id,
            'kos_id' => $kamarPertama->kos_id,
            'sewa_id' => $sewa1->id,
            'rating' => 4,
            'komentar' => 'Fasilitas lengkap sesuai deskripsi. Lokasi strategis dekat kampus. WiFi kadang lambat.',
        ]);

        $this->command->info('');
        $this->command->info("Kredensial Pengguna (Password: password)");
        $this->command->info("Admin   : {$admin->email}");
        $this->command->info("Pemilik : {$pemilik->email}");
        $this->command->info("Penyewa : {$penyewa->email}");
        $this->command->info('');
    }
}

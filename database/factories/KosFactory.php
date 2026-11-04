<?php

namespace Database\Factories;

use App\Models\Kos;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kos>
 */
class KosFactory extends Factory
{
    protected $model = Kos::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $namaKos = [
            'Kos Harmoni', 'Kos Sejahtera', 'Kos Mawar', 'Kos Melati',
            'Kos Dahlia', 'Kos Anggrek', 'Kos Cendana', 'Kos Permata',
            'Kos Mutiara', 'Kos Griya Asri', 'Kos Sakura', 'Kos Flamboyan',
        ];

        $alamat = [
            'Jl. Merdeka No. 10, Malang',
            'Jl. Soekarno-Hatta No. 25, Malang',
            'Jl. Veteran No. 5, Malang',
            'Jl. Diponegoro No. 88, Malang',
            'Jl. Sudirman No. 12, Malang',
            'Jl. MT. Haryono No. 33, Malang',
            'Jl. Bandung No. 7, Malang',
            'Jl. Jakarta No. 15, Malang',
        ];

        $deskripsi = [
            'Kos nyaman dan bersih, dekat kampus dan pusat kota. Lingkungan aman dan tenang.',
            'Kos strategis dengan fasilitas lengkap. Cocok untuk mahasiswa dan pekerja.',
            'Kos modern dengan desain minimalis. Tersedia parkiran luas dan wifi cepat.',
            'Kos murah meriah dengan lokasi strategis dekat pusat perbelanjaan.',
            'Kos eksklusif dengan fasilitas premium. Kamar luas dan nyaman.',
        ];

        return [
            'user_id' => User::factory(),
            'nama_kos' => fake()->randomElement($namaKos) . ' ' . fake()->numberBetween(1, 99),
            'alamat' => fake()->randomElement($alamat),
            'deskripsi' => fake()->randomElement($deskripsi),
            'tipe_kos' => fake()->randomElement(['putra', 'putri', 'campur']),
            'foto' => null,
        ];
    }
}

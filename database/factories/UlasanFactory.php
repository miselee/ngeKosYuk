<?php

namespace Database\Factories;

use App\Models\Kos;
use App\Models\Sewa;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ulasan>
 */
class UlasanFactory extends Factory
{
    protected $model = Ulasan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $komentars = [
            'Kos sangat nyaman dan bersih. Pemilik ramah dan responsif.',
            'Fasilitas lengkap sesuai deskripsi. Lokasi strategis dekat kampus.',
            'Kamar luas dan terawat. WiFi kencang. Recommended!',
            'Lumayan bagus untuk harganya. Lingkungan tenang.',
            'Kos bersih, aman, dan nyaman. Cocok untuk mahasiswa.',
        ];

        return [
            'user_id' => User::factory(),
            'kos_id' => Kos::factory(),
            'sewa_id' => Sewa::factory(),
            'rating' => fake()->numberBetween(3, 5),
            'komentar' => fake()->randomElement($komentars),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Kamar;
use App\Models\Kos;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kamar>
 */
class KamarFactory extends Factory
{
    protected $model = Kamar::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fasilitas = [
            'Kasur, Lemari, Meja Belajar, WiFi',
            'Kasur, Lemari, AC, Kamar Mandi Dalam',
            'Kasur, Lemari, Meja Belajar, AC, WiFi, Kamar Mandi Dalam',
            'Kasur, Lemari, Kipas Angin, WiFi',
            'Kasur, Lemari, AC, WiFi, Kamar Mandi Dalam, TV',
        ];

        return [
            'kos_id' => Kos::factory(),
            'nomor_kamar' => fake()->unique()->numerify('K-###'),
            'harga_per_bulan' => fake()->randomElement([500000, 750000, 1000000, 1250000, 1500000, 2000000]),
            'fasilitas' => fake()->randomElement($fasilitas),
            'status' => 'tersedia',
        ];
    }

    /**
     * Kamar yang sudah terisi.
     */
    public function terisi(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'terisi',
        ]);
    }
}

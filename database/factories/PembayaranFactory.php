<?php

namespace Database\Factories;

use App\Models\Pembayaran;
use App\Models\Sewa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pembayaran>
 */
class PembayaranFactory extends Factory
{
    protected $model = Pembayaran::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sewa_id' => Sewa::factory(),
            'jumlah' => fake()->randomElement([500000, 750000, 1000000, 1250000, 1500000, 2000000]),
            'metode_pembayaran' => fake()->randomElement(['transfer_bank', 'e_wallet', 'tunai']),
            'bukti_pembayaran' => null,
            'status' => 'pending',
            'tanggal_bayar' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'keterangan' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Pembayaran yang sudah diverifikasi.
     */
    public function diverifikasi(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'diverifikasi',
        ]);
    }
}

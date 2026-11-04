<?php

namespace Database\Factories;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Sewa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sewa>
 */
class SewaFactory extends Factory
{
    protected $model = Sewa::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tanggalMulai = fake()->dateTimeBetween('now', '+1 month');
        $tanggalSelesai = (clone $tanggalMulai)->modify('+' . fake()->randomElement([3, 6, 12]) . ' months');

        return [
            'user_id' => User::factory(),
            'kamar_id' => Kamar::factory(),
            'kos_id' => Kos::factory(),
            'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
            'tanggal_selesai' => $tanggalSelesai->format('Y-m-d'),
            'status' => 'pending',
            'catatan' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Sewa yang sudah disetujui.
     */
    public function disetujui(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'disetujui',
        ]);
    }

    /**
     * Sewa yang sudah selesai.
     */
    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'selesai',
        ]);
    }
}

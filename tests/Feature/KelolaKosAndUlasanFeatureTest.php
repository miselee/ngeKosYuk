<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Sewa;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelolaKosAndUlasanFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $pemilik;

    private Kos $kos;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemilik = User::factory()->create(['role' => 'pemilik']);
        $this->kos = Kos::create([
            'user_id' => $this->pemilik->id,
            'nama_kos' => 'Kos Mawar',
            'alamat' => 'Jalan Mawar 10',
            'tipe_kos' => 'putri',
            'deskripsi' => 'Kos nyaman',
        ]);
    }

    public function test_owner_can_create_update_and_view_owned_kos(): void
    {
        $response = $this->actingAs($this->pemilik)->get(route('pemilik.kelola-kos.index'));
        $response->assertOk()->assertSee('Kos Mawar');

        $this->get(route('pemilik.kelola-kos.create'))
            ->assertOk()
            ->assertSee('Tambah Kos Baru');
        $this->get(route('pemilik.kelola-kos.edit', $this->kos))
            ->assertOk()
            ->assertSee('Kos Mawar');

        $response = $this->post(route('pemilik.kelola-kos.store'), [
            'nama_kos' => 'Kos Melati',
            'alamat' => 'Jalan Melati 12',
            'tipe_kos' => 'campur',
            'deskripsi' => 'Dekat kampus',
            'foto' => 'https://example.com/kos.jpg',
        ]);

        $kosBaru = Kos::where('nama_kos', 'Kos Melati')->firstOrFail();
        $response->assertRedirect(route('pemilik.kelola-kos.index'));
        $this->assertSame($this->pemilik->id, $kosBaru->user_id);

        $response = $this->put(route('pemilik.kelola-kos.update', $kosBaru), [
            'nama_kos' => 'Kos Melati Baru',
            'alamat' => 'Jalan Melati 15',
            'tipe_kos' => 'putra',
            'deskripsi' => 'Sudah direnovasi',
            'foto' => '',
        ]);

        $response->assertRedirect(route('pemilik.kelola-kos.index'));
        $this->assertDatabaseHas('kos', [
            'id' => $kosBaru->id,
            'nama_kos' => 'Kos Melati Baru',
            'alamat' => 'Jalan Melati 15',
            'tipe_kos' => 'putra',
        ]);
    }

    public function test_owner_cannot_manage_another_owners_kos(): void
    {
        $pemilikLain = User::factory()->create(['role' => 'pemilik']);

        $this->actingAs($pemilikLain)
            ->get(route('pemilik.kelola-kos.edit', $this->kos))
            ->assertForbidden();

        $this->actingAs($pemilikLain)
            ->put(route('pemilik.kelola-kos.update', $this->kos), [
                'nama_kos' => 'Kos Diubah',
                'alamat' => 'Alamat lain',
                'tipe_kos' => 'putra',
            ])
            ->assertForbidden();
    }

    public function test_owner_cannot_delete_kos_with_rental_history(): void
    {
        $penyewa = User::factory()->create(['role' => 'penyewa']);
        $kamar = Kamar::create([
            'kos_id' => $this->kos->id,
            'nomor_kamar' => 'A1',
            'harga_per_bulan' => 500000,
            'status' => 'tersedia',
        ]);
        Sewa::create([
            'user_id' => $penyewa->id,
            'kamar_id' => $kamar->id,
            'kos_id' => $this->kos->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'status' => 'selesai',
        ]);

        $this->actingAs($this->pemilik)
            ->delete(route('pemilik.kelola-kos.destroy', $this->kos))
            ->assertRedirect(route('pemilik.kelola-kos.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('kos', ['id' => $this->kos->id]);
    }

    public function test_owner_can_view_and_filter_reviews_only_for_owned_kos(): void
    {
        $penyewa = User::factory()->create(['role' => 'penyewa']);
        $kamar = Kamar::create([
            'kos_id' => $this->kos->id,
            'nomor_kamar' => 'A1',
            'harga_per_bulan' => 500000,
            'status' => 'tersedia',
        ]);
        $sewa = Sewa::create([
            'user_id' => $penyewa->id,
            'kamar_id' => $kamar->id,
            'kos_id' => $this->kos->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'status' => 'selesai',
        ]);
        Ulasan::create([
            'user_id' => $penyewa->id,
            'kos_id' => $this->kos->id,
            'sewa_id' => $sewa->id,
            'rating' => 5,
            'komentar' => 'Bersih dan nyaman',
        ]);

        $this->actingAs($this->pemilik)
            ->get(route('pemilik.ulasan.index'))
            ->assertOk()
            ->assertSee('Bersih dan nyaman')
            ->assertSee('Kos Mawar');

        $this->get(route('pemilik.ulasan.index', ['rating' => 4]))
            ->assertOk()
            ->assertDontSee('Bersih dan nyaman');
    }
}

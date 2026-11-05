<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Sewa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelolaKamarFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $pemilik;
    private User $pemilikLain;
    private User $penyewa;
    private Kos $kosPemilik;
    private Kamar $kamar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemilik = User::factory()->create([
            'role' => 'pemilik',
            'nomor_hp' => '081234567801',
        ]);

        $this->pemilikLain = User::factory()->create([
            'role' => 'pemilik',
            'nomor_hp' => '081234567802',
        ]);

        $this->penyewa = User::factory()->create([
            'role' => 'penyewa',
            'nomor_hp' => '081234567803',
        ]);

        $this->kosPemilik = Kos::create([
            'user_id' => $this->pemilik->id,
            'nama_kos' => 'Kos Pemilik Utama',
            'alamat' => 'Jl. Soekarno Hatta No. 10',
            'tipe_kos' => 'campur',
            'deskripsi' => 'Kos modern dan bersih',
        ]);

        $this->kamar = Kamar::create([
            'kos_id' => $this->kosPemilik->id,
            'nomor_kamar' => 'A-101',
            'harga_per_bulan' => 850000,
            'fasilitas' => 'AC, WiFi, Kasur',
            'status' => 'tersedia',
        ]);
    }

    public function test_pemilik_can_access_dashboard_and_see_summary_statistics(): void
    {
        $response = $this->actingAs($this->pemilik)->get('/pemilik/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Kos Pemilik Utama');
        $response->assertSee('A-101');
        $response->assertSee('Total Kamar');
    }

    public function test_penyewa_cannot_access_pemilik_dashboard(): void
    {
        $response = $this->actingAs($this->penyewa)->get('/pemilik/dashboard');

        $response->assertStatus(403);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/pemilik/kelola-kamar');

        $response->assertRedirect('/login');
    }

    public function test_pemilik_can_view_room_list(): void
    {
        $response = $this->actingAs($this->pemilik)->get(route('pemilik.kelola-kamar.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Kamar Kos');
        $response->assertSee('A-101');
        $response->assertSee('850.000');
    }

    public function test_pemilik_can_filter_room_list_by_status(): void
    {
        Kamar::create([
            'kos_id' => $this->kosPemilik->id,
            'nomor_kamar' => 'B-202',
            'harga_per_bulan' => 900000,
            'fasilitas' => 'Kasur',
            'status' => 'terisi',
        ]);

        $responseTersedia = $this->actingAs($this->pemilik)->get(route('pemilik.kelola-kamar.index', ['status' => 'tersedia']));
        $responseTersedia->assertStatus(200);
        $responseTersedia->assertSee('A-101');
        $responseTersedia->assertDontSee('B-202');

        $responseTerisi = $this->actingAs($this->pemilik)->get(route('pemilik.kelola-kamar.index', ['status' => 'terisi']));
        $responseTerisi->assertStatus(200);
        $responseTerisi->assertSee('B-202');
        $responseTerisi->assertDontSee('A-101');
    }

    public function test_pemilik_can_view_create_room_page(): void
    {
        $response = $this->actingAs($this->pemilik)->get(route('pemilik.kelola-kamar.create'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Kamar Kos Baru');
        $response->assertSee('Kos Pemilik Utama');
    }

    public function test_pemilik_can_store_new_room(): void
    {
        $payload = [
            'kos_id' => $this->kosPemilik->id,
            'nomor_kamar' => 'C-303',
            'harga_per_bulan' => 1200000,
            'fasilitas' => 'AC, WiFi, Meja Belajar',
            'status' => 'tersedia',
        ];

        $response = $this->actingAs($this->pemilik)->post(route('pemilik.kelola-kamar.store'), $payload);

        $response->assertRedirect(route('pemilik.kelola-kamar.index'));
        $this->assertDatabaseHas('kamars', [
            'kos_id' => $this->kosPemilik->id,
            'nomor_kamar' => 'C-303',
            'harga_per_bulan' => 1200000,
            'status' => 'tersedia',
        ]);
    }

    public function test_pemilik_cannot_create_room_for_another_owners_kos(): void
    {
        $kosLain = Kos::create([
            'user_id' => $this->pemilikLain->id,
            'nama_kos' => 'Kos Milik Orang Lain',
            'alamat' => 'Jl. Lain',
            'tipe_kos' => 'putra',
        ]);

        $payload = [
            'kos_id' => $kosLain->id,
            'nomor_kamar' => 'X-999',
            'harga_per_bulan' => 1000000,
            'status' => 'tersedia',
        ];

        $response = $this->actingAs($this->pemilik)->post(route('pemilik.kelola-kamar.store'), $payload);

        $response->assertSessionHasErrors('kos_id');
        $this->assertDatabaseMissing('kamars', [
            'nomor_kamar' => 'X-999',
        ]);
    }

    public function test_pemilik_can_view_edit_room_page(): void
    {
        $response = $this->actingAs($this->pemilik)->get(route('pemilik.kelola-kamar.edit', $this->kamar));

        $response->assertStatus(200);
        $response->assertSee('Edit Kamar A-101');
        $response->assertSee('850000');
    }

    public function test_pemilik_cannot_access_edit_for_another_owners_room(): void
    {
        $kosLain = Kos::create([
            'user_id' => $this->pemilikLain->id,
            'nama_kos' => 'Kos Milik Orang Lain',
            'alamat' => 'Jl. Lain',
            'tipe_kos' => 'putra',
        ]);

        $kamarLain = Kamar::create([
            'kos_id' => $kosLain->id,
            'nomor_kamar' => 'L-1',
            'harga_per_bulan' => 500000,
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($this->pemilik)->get(route('pemilik.kelola-kamar.edit', $kamarLain));

        $response->assertStatus(403);
    }

    public function test_pemilik_can_update_room(): void
    {
        $updatePayload = [
            'kos_id' => $this->kosPemilik->id,
            'nomor_kamar' => 'A-101-UPDATED',
            'harga_per_bulan' => 950000,
            'fasilitas' => 'AC, WiFi, Kasur, Lemari Baru',
            'status' => 'terisi',
        ];

        $response = $this->actingAs($this->pemilik)->put(route('pemilik.kelola-kamar.update', $this->kamar), $updatePayload);

        $response->assertRedirect(route('pemilik.kelola-kamar.index'));
        $this->assertDatabaseHas('kamars', [
            'id' => $this->kamar->id,
            'nomor_kamar' => 'A-101-UPDATED',
            'harga_per_bulan' => 950000,
            'status' => 'terisi',
        ]);
    }

    public function test_pemilik_can_toggle_room_status(): void
    {
        $this->assertEquals('tersedia', $this->kamar->status);

        $response = $this->actingAs($this->pemilik)->patch(route('pemilik.kelola-kamar.toggle-status', $this->kamar));

        $response->assertRedirect();
        $this->assertEquals('terisi', $this->kamar->fresh()->status);

        // Toggle back
        $this->actingAs($this->pemilik)->patch(route('pemilik.kelola-kamar.toggle-status', $this->kamar));
        $this->assertEquals('tersedia', $this->kamar->fresh()->status);
    }

    public function test_pemilik_can_delete_room_without_active_sewa(): void
    {
        $response = $this->actingAs($this->pemilik)->delete(route('pemilik.kelola-kamar.destroy', $this->kamar));

        $response->assertRedirect(route('pemilik.kelola-kamar.index'));
        $this->assertDatabaseMissing('kamars', [
            'id' => $this->kamar->id,
        ]);
    }

    public function test_pemilik_cannot_delete_room_with_active_sewa(): void
    {
        Sewa::create([
            'user_id' => $this->penyewa->id,
            'kamar_id' => $this->kamar->id,
            'kos_id' => $this->kosPemilik->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(1)->toDateString(),
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($this->pemilik)->delete(route('pemilik.kelola-kamar.destroy', $this->kamar));

        $response->assertRedirect(route('pemilik.kelola-kamar.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('kamars', [
            'id' => $this->kamar->id,
        ]);
    }

    public function test_pemilik_can_quick_store_kos(): void
    {
        $payload = [
            'nama_kos' => 'Kos Baru Banget',
            'alamat' => 'Jl. Baru No. 1',
            'tipe_kos' => 'putri',
            'deskripsi' => 'Kos putri bersih dan nyaman',
        ];

        $response = $this->actingAs($this->pemilik)->post(route('pemilik.kos.quick-store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('kos', [
            'user_id' => $this->pemilik->id,
            'nama_kos' => 'Kos Baru Banget',
            'tipe_kos' => 'putri',
        ]);
    }
}

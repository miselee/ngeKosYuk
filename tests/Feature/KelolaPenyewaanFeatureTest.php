<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Sewa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelolaPenyewaanFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $pemilik;
    private User $pemilikLain;
    private User $penyewa;
    private Kos $kos;
    private Kamar $kamar;
    private Sewa $sewaPending;

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
            'name' => 'Budi Santoso',
            'role' => 'penyewa',
            'nomor_hp' => '089876543210',
        ]);

        $this->kos = Kos::create([
            'user_id' => $this->pemilik->id,
            'nama_kos' => 'Kos Pemilik Makmur',
            'alamat' => 'Jl. Makmur No. 99',
            'tipe_kos' => 'campur',
            'deskripsi' => 'Kos nyaman',
        ]);

        $this->kamar = Kamar::create([
            'kos_id' => $this->kos->id,
            'nomor_kamar' => 'K-101',
            'harga_per_bulan' => 900000,
            'fasilitas' => 'AC, WiFi',
            'status' => 'tersedia',
        ]);

        $this->sewaPending = Sewa::create([
            'user_id' => $this->penyewa->id,
            'kamar_id' => $this->kamar->id,
            'kos_id' => $this->kos->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(1)->toDateString(),
            'status' => 'pending',
            'catatan' => 'Mohon segera diproses.',
        ]);
    }

    public function test_pemilik_can_view_daftar_penyewaan(): void
    {
        $response = $this->actingAs($this->pemilik)->get(route('pemilik.penyewaan.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Penyewaan Kos');
        $response->assertSee('Budi Santoso');
        $response->assertSee('K-101');
    }

    public function test_penyewa_cannot_access_pemilik_daftar_penyewaan(): void
    {
        $response = $this->actingAs($this->penyewa)->get(route('pemilik.penyewaan.index'));

        $response->assertStatus(403);
    }

    public function test_pemilik_can_filter_penyewaan_by_status(): void
    {
        Sewa::create([
            'user_id' => $this->penyewa->id,
            'kamar_id' => $this->kamar->id,
            'kos_id' => $this->kos->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(2)->toDateString(),
            'status' => 'disetujui',
        ]);

        $resPending = $this->actingAs($this->pemilik)->get(route('pemilik.penyewaan.index', ['status' => 'pending']));
        $resPending->assertStatus(200);
        $resPending->assertSee('Mohon segera diproses.');

        $resDisetujui = $this->actingAs($this->pemilik)->get(route('pemilik.penyewaan.index', ['status' => 'disetujui']));
        $resDisetujui->assertStatus(200);
    }

    public function test_pemilik_can_setujui_pending_sewa_and_room_becomes_terisi(): void
    {
        $this->assertEquals('tersedia', $this->kamar->status);

        $response = $this->actingAs($this->pemilik)->patch(route('pemilik.penyewaan.setujui', $this->sewaPending));

        $response->assertRedirect();
        $this->assertEquals('disetujui', $this->sewaPending->fresh()->status);
        $this->assertEquals('terisi', $this->kamar->fresh()->status);
    }

    public function test_pemilik_can_tolak_pending_sewa_with_reason(): void
    {
        $response = $this->actingAs($this->pemilik)->patch(route('pemilik.penyewaan.tolak', $this->sewaPending), [
            'alasan_penolakan' => 'Kamar sedang direnovasi',
        ]);

        $response->assertRedirect();
        $freshSewa = $this->sewaPending->fresh();
        $this->assertEquals('ditolak', $freshSewa->status);
        $this->assertStringContainsString('Kamar sedang direnovasi', $freshSewa->catatan);
    }

    public function test_pemilik_cannot_setujui_another_owners_sewa(): void
    {
        $response = $this->actingAs($this->pemilikLain)->patch(route('pemilik.penyewaan.setujui', $this->sewaPending));

        $response->assertStatus(403);
    }

    public function test_pemilik_can_mark_approved_sewa_as_selesai_and_room_becomes_tersedia(): void
    {
        $this->sewaPending->update(['status' => 'disetujui']);
        $this->kamar->update(['status' => 'terisi']);

        $response = $this->actingAs($this->pemilik)->patch(route('pemilik.penyewaan.selesai', $this->sewaPending));

        $response->assertRedirect();
        $this->assertEquals('selesai', $this->sewaPending->fresh()->status);
        $this->assertEquals('tersedia', $this->kamar->fresh()->status);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pembayaran;
use App\Models\Sewa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelolaPembayaranFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $pemilik;
    private User $pemilikLain;
    private User $penyewa;
    private Kos $kos;
    private Kamar $kamar;
    private Sewa $sewa;
    private Pembayaran $pembayaranPending;

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
            'name' => 'Dewi Sartika',
            'role' => 'penyewa',
            'nomor_hp' => '081299887766',
        ]);

        $this->kos = Kos::create([
            'user_id' => $this->pemilik->id,
            'nama_kos' => 'Kos Melati Bahagia',
            'alamat' => 'Jl. Bahagia No. 12',
            'tipe_kos' => 'putri',
            'deskripsi' => 'Kos putri nyaman',
        ]);

        $this->kamar = Kamar::create([
            'kos_id' => $this->kos->id,
            'nomor_kamar' => 'M-01',
            'harga_per_bulan' => 1000000,
            'fasilitas' => 'AC, Kamar Mandi Dalam',
            'status' => 'terisi',
        ]);

        $this->sewa = Sewa::create([
            'user_id' => $this->penyewa->id,
            'kamar_id' => $this->kamar->id,
            'kos_id' => $this->kos->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(1)->toDateString(),
            'status' => 'disetujui',
            'catatan' => 'Sewa bulan pertama',
        ]);

        $this->pembayaranPending = Pembayaran::create([
            'sewa_id' => $this->sewa->id,
            'jumlah' => 1000000,
            'metode_pembayaran' => 'transfer_bank',
            'bukti_pembayaran' => 'bukti_pembayaran/sample.jpg',
            'status' => 'pending',
            'tanggal_bayar' => now()->toDateString(),
            'keterangan' => 'Transfer via BCA',
        ]);
    }

    public function test_pemilik_can_view_pembayaran_list(): void
    {
        $response = $this->actingAs($this->pemilik)->get(route('pemilik.pembayaran.index'));

        $response->assertStatus(200);
        $response->assertSee('Konfirmasi Pembayaran');
        $response->assertSee('Dewi Sartika');
        $response->assertSee('1.000.000');
        $response->assertSee('Transfer Bank');
    }

    public function test_penyewa_cannot_access_pemilik_pembayaran_list(): void
    {
        $response = $this->actingAs($this->penyewa)->get(route('pemilik.pembayaran.index'));

        $response->assertStatus(403);
    }

    public function test_pemilik_can_filter_pembayaran_by_status(): void
    {
        Pembayaran::create([
            'sewa_id' => $this->sewa->id,
            'jumlah' => 1000000,
            'metode_pembayaran' => 'tunai',
            'status' => 'diverifikasi',
            'tanggal_bayar' => now()->toDateString(),
            'keterangan' => 'Bayar tunai bulan kedua',
        ]);

        $resPending = $this->actingAs($this->pemilik)->get(route('pemilik.pembayaran.index', ['status' => 'pending']));
        $resPending->assertStatus(200);
        $resPending->assertSee('Transfer via BCA');

        $resDiverifikasi = $this->actingAs($this->pemilik)->get(route('pemilik.pembayaran.index', ['status' => 'diverifikasi']));
        $resDiverifikasi->assertStatus(200);
        $resDiverifikasi->assertSee('Bayar tunai bulan kedua');
    }

    public function test_pemilik_can_verify_pending_pembayaran(): void
    {
        $this->assertEquals('pending', $this->pembayaranPending->status);

        $response = $this->actingAs($this->pemilik)->patch(route('pemilik.pembayaran.verifikasi', $this->pembayaranPending));

        $response->assertRedirect();
        $this->assertEquals('diverifikasi', $this->pembayaranPending->fresh()->status);
    }

    public function test_pemilik_can_tolak_pending_pembayaran_with_reason(): void
    {
        $response = $this->actingAs($this->pemilik)->patch(route('pemilik.pembayaran.tolak', $this->pembayaranPending), [
            'alasan_penolakan' => 'Nominal transfer tidak sesuai',
        ]);

        $response->assertRedirect();
        $fresh = $this->pembayaranPending->fresh();
        $this->assertEquals('ditolak', $fresh->status);
        $this->assertStringContainsString('Nominal transfer tidak sesuai', $fresh->keterangan);
    }

    public function test_pemilik_cannot_verify_another_owners_pembayaran(): void
    {
        $response = $this->actingAs($this->pemilikLain)->patch(route('pemilik.pembayaran.verifikasi', $this->pembayaranPending));

        $response->assertStatus(403);
    }
}

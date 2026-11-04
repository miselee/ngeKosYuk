<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pembayaran;
use App\Models\Sewa;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PenyewaFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $penyewa;
    private User $pemilik;
    private Kos $kos;
    private Kamar $kamar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemilik = User::factory()->create([
            'role' => 'pemilik',
            'nomor_hp' => '081234567891',
        ]);

        $this->penyewa = User::factory()->create([
            'role' => 'penyewa',
            'nomor_hp' => '081234567892',
        ]);

        $this->kos = Kos::create([
            'user_id' => $this->pemilik->id,
            'nama_kos' => 'Kos Melati Indah',
            'alamat' => 'Jl. Mawar No. 12, Malang',
            'deskripsi' => 'Kos aman dan nyaman.',
            'tipe_kos' => 'putri',
        ]);

        $this->kamar = Kamar::create([
            'kos_id' => $this->kos->id,
            'nomor_kamar' => 'K-001',
            'harga_per_bulan' => 750000,
            'fasilitas' => 'Kasur, WiFi, Lemari',
            'status' => 'tersedia',
        ]);
    }

    public function test_penyewa_can_access_dashboard_and_see_recommendations(): void
    {
        $response = $this->actingAs($this->penyewa)->get('/penyewa/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Kos Melati Indah');
    }

    public function test_penyewa_can_search_kos_with_filters(): void
    {
        $response = $this->actingAs($this->penyewa)->get('/penyewa/cari-kos?lokasi=Mawar&tipe_kos=putri');

        $response->assertStatus(200);
        $response->assertSee('Kos Melati Indah');

        $responseEmpty = $this->actingAs($this->penyewa)->get('/penyewa/cari-kos?lokasi=Jakarta');
        $responseEmpty->assertStatus(200);
        $responseEmpty->assertSee('Tidak Ada Kos yang Ditemukan');
    }

    public function test_penyewa_can_view_kos_detail_and_rooms(): void
    {
        $response = $this->actingAs($this->penyewa)->get('/penyewa/detail-kos/' . $this->kos->id);

        $response->assertStatus(200);
        $response->assertSee('Kos Melati Indah');
        $response->assertSee('Kamar K-001');
        $response->assertSee('Rp 750.000');
    }

    public function test_penyewa_can_submit_sewa_application(): void
    {
        $response = $this->actingAs($this->penyewa)->post('/penyewa/pengajuan-sewa', [
            'kamar_id' => $this->kamar->id,
            'kos_id' => $this->kos->id,
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_selesai' => now()->addMonths(3)->format('Y-m-d'),
            'catatan' => 'Mohon persetujuannya.',
        ]);

        $response->assertRedirect('/penyewa/riwayat-sewa');
        $this->assertDatabaseHas('sewas', [
            'user_id' => $this->penyewa->id,
            'kamar_id' => $this->kamar->id,
            'kos_id' => $this->kos->id,
            'status' => 'pending',
            'catatan' => 'Mohon persetujuannya.',
        ]);
    }

    public function test_penyewa_can_view_riwayat_sewa(): void
    {
        Sewa::create([
            'user_id' => $this->penyewa->id,
            'kamar_id' => $this->kamar->id,
            'kos_id' => $this->kos->id,
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_selesai' => now()->addMonths(6)->format('Y-m-d'),
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($this->penyewa)->get('/penyewa/riwayat-sewa');

        $response->assertStatus(200);
        $response->assertSee('Kos Melati Indah');
        $response->assertSee('Kamar #K-001');
        $response->assertSee('Disetujui');
    }

    public function test_penyewa_can_submit_pembayaran_with_proof(): void
    {
        Storage::fake('public');

        $sewa = Sewa::create([
            'user_id' => $this->penyewa->id,
            'kamar_id' => $this->kamar->id,
            'kos_id' => $this->kos->id,
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_selesai' => now()->addMonths(6)->format('Y-m-d'),
            'status' => 'disetujui',
        ]);

        $file = UploadedFile::fake()->image('bukti.png');

        $response = $this->actingAs($this->penyewa)->post('/penyewa/pembayaran', [
            'sewa_id' => $sewa->id,
            'jumlah' => 750000,
            'metode_pembayaran' => 'transfer_bank',
            'bukti_pembayaran' => $file,
            'tanggal_bayar' => now()->format('Y-m-d'),
            'keterangan' => 'Bayar bulan pertama',
        ]);

        $response->assertRedirect('/penyewa/pembayaran');
        $this->assertDatabaseHas('pembayarans', [
            'sewa_id' => $sewa->id,
            'jumlah' => 750000,
            'metode_pembayaran' => 'transfer_bank',
            'status' => 'pending',
            'keterangan' => 'Bayar bulan pertama',
        ]);
    }

    public function test_penyewa_can_view_daftar_pembayaran(): void
    {
        $sewa = Sewa::create([
            'user_id' => $this->penyewa->id,
            'kamar_id' => $this->kamar->id,
            'kos_id' => $this->kos->id,
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_selesai' => now()->addMonths(6)->format('Y-m-d'),
            'status' => 'disetujui',
        ]);

        Pembayaran::create([
            'sewa_id' => $sewa->id,
            'jumlah' => 750000,
            'metode_pembayaran' => 'transfer_bank',
            'status' => 'diverifikasi',
            'tanggal_bayar' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->penyewa)->get('/penyewa/pembayaran');

        $response->assertStatus(200);
        $response->assertSee('Kos Melati Indah');
        $response->assertSee('Rp 750.000');
        $response->assertSee('Diverifikasi');
    }

    public function test_penyewa_can_submit_ulasan_for_approved_or_completed_sewa(): void
    {
        $sewa = Sewa::create([
            'user_id' => $this->penyewa->id,
            'kamar_id' => $this->kamar->id,
            'kos_id' => $this->kos->id,
            'tanggal_mulai' => now()->subMonths(6)->format('Y-m-d'),
            'tanggal_selesai' => now()->subMonth()->format('Y-m-d'),
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($this->penyewa)->post('/penyewa/ulasan', [
            'sewa_id' => $sewa->id,
            'kos_id' => $this->kos->id,
            'rating' => 5,
            'komentar' => 'Tempat kos sangat bersih dan nyaman!',
        ]);

        $response->assertRedirect('/penyewa/riwayat-sewa');
        $this->assertDatabaseHas('ulasans', [
            'user_id' => $this->penyewa->id,
            'kos_id' => $this->kos->id,
            'sewa_id' => $sewa->id,
            'rating' => 5,
            'komentar' => 'Tempat kos sangat bersih dan nyaman!',
        ]);
    }

    public function test_penyewa_cannot_submit_duplicate_ulasan(): void
    {
        $sewa = Sewa::create([
            'user_id' => $this->penyewa->id,
            'kamar_id' => $this->kamar->id,
            'kos_id' => $this->kos->id,
            'tanggal_mulai' => now()->subMonths(6)->format('Y-m-d'),
            'tanggal_selesai' => now()->subMonth()->format('Y-m-d'),
            'status' => 'selesai',
        ]);

        Ulasan::create([
            'user_id' => $this->penyewa->id,
            'kos_id' => $this->kos->id,
            'sewa_id' => $sewa->id,
            'rating' => 4,
            'komentar' => 'Ulasan pertama.',
        ]);

        $response = $this->actingAs($this->penyewa)->post('/penyewa/ulasan', [
            'sewa_id' => $sewa->id,
            'kos_id' => $this->kos->id,
            'rating' => 5,
            'komentar' => 'Ulasan kedua yang harus ditolak.',
        ]);

        $response->assertRedirect('/penyewa/riwayat-sewa');
        $this->assertDatabaseCount('ulasans', 1);
    }

    public function test_non_penyewa_cannot_access_penyewa_routes(): void
    {
        $pemilik = User::factory()->create(['role' => 'pemilik']);

        $response = $this->actingAs($pemilik)->get('/penyewa/dashboard');
        $response->assertStatus(403);
    }
}

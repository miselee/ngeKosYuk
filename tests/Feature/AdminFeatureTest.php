<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pembayaran;
use App\Models\Sewa;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $pemilik;
    private User $penyewa;
    private Kos $kos;
    private Kamar $kamar;
    private Sewa $sewa;
    private Pembayaran $pembayaran;
    private Ulasan $ulasan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@kos.com',
            'role' => 'admin',
            'nomor_hp' => '081111111111',
        ]);

        $this->pemilik = User::factory()->create([
            'name' => 'Budi Pemilik',
            'email' => 'budi@kos.com',
            'role' => 'pemilik',
            'nomor_hp' => '081222222222',
        ]);

        $this->penyewa = User::factory()->create([
            'name' => 'Siti Penyewa',
            'email' => 'siti@kos.com',
            'role' => 'penyewa',
            'nomor_hp' => '081333333333',
        ]);

        $this->kos = Kos::create([
            'user_id' => $this->pemilik->id,
            'nama_kos' => 'Kos Nusantara',
            'alamat' => 'Jl. Merdeka No. 45',
            'tipe_kos' => 'campur',
            'deskripsi' => 'Kos strategis',
        ]);

        $this->kamar = Kamar::create([
            'kos_id' => $this->kos->id,
            'nomor_kamar' => 'N-101',
            'harga_per_bulan' => 1200000,
            'fasilitas' => 'AC, WiFi',
            'status' => 'terisi',
        ]);

        $this->sewa = Sewa::create([
            'user_id' => $this->penyewa->id,
            'kamar_id' => $this->kamar->id,
            'kos_id' => $this->kos->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
            'status' => 'disetujui',
        ]);

        $this->pembayaran = Pembayaran::create([
            'sewa_id' => $this->sewa->id,
            'jumlah' => 1200000,
            'metode_pembayaran' => 'transfer_bank',
            'status' => 'diverifikasi',
            'tanggal_bayar' => now()->toDateString(),
            'keterangan' => 'Pembayaran lunas bulan pertama',
        ]);

        $this->ulasan = Ulasan::create([
            'user_id' => $this->penyewa->id,
            'kos_id' => $this->kos->id,
            'sewa_id' => $this->sewa->id,
            'rating' => 5,
            'komentar' => 'Sangat nyaman dihuni.',
        ]);
    }

    public function test_admin_can_access_dashboard_with_platform_metrics(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Admin');
        $response->assertSee('Kos Nusantara');
        $response->assertSee('Siti Penyewa');
        $response->assertSee('1.200.000');
    }

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        $this->actingAs($this->pemilik)->get(route('admin.dashboard'))->assertStatus(403);
        $this->actingAs($this->penyewa)->get(route('admin.users.index'))->assertStatus(403);
    }

    public function test_admin_can_manage_users_create_update_and_delete(): void
    {
        $this->actingAs($this->admin)->get(route('admin.users.index'))
            ->assertStatus(200)
            ->assertSee('Budi Pemilik')
            ->assertSee('Siti Penyewa');

        $createResponse = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Mitra Baru',
            'email' => 'mitra@kos.com',
            'nomor_hp' => '081444444444',
            'role' => 'pemilik',
            'password' => 'password123',
        ]);

        $createResponse->assertRedirect(route('admin.users.index'));
        $newUser = User::where('email', 'mitra@kos.com')->firstOrFail();

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.users.update', $newUser), [
            'name' => 'Mitra Baru Diperbarui',
            'email' => 'mitra@kos.com',
            'nomor_hp' => '081555555555',
            'role' => 'penyewa',
        ]);

        $updateResponse->assertRedirect(route('admin.users.index'));
        $this->assertEquals('penyewa', $newUser->fresh()->role);

        // Admin cannot delete their own active account.
        $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin))
            ->assertSessionHas('error');

        $this->actingAs($this->admin)->delete(route('admin.users.destroy', $newUser))
            ->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $newUser->id]);
    }

    public function test_admin_can_monitor_kos_and_prevent_deleting_kos_with_active_sewa(): void
    {
        $this->actingAs($this->admin)->get(route('admin.kos.index'))
            ->assertStatus(200)
            ->assertSee('Kos Nusantara')
            ->assertSee('N-101');

        // Cannot delete kos while active sewa exists.
        $this->actingAs($this->admin)->delete(route('admin.kos.destroy', $this->kos))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('kos', ['id' => $this->kos->id]);

        // Mark sewa as selesai, then deletion succeeds.
        $this->sewa->update(['status' => 'selesai']);
        $this->actingAs($this->admin)->delete(route('admin.kos.destroy', $this->kos))
            ->assertRedirect(route('admin.kos.index'));
        $this->assertDatabaseMissing('kos', ['id' => $this->kos->id]);
    }

    public function test_admin_can_monitor_transactions_view_kwitansi_and_moderate_ulasan(): void
    {
        $this->actingAs($this->admin)->get(route('admin.transaksi.index', ['tab' => 'sewa']))
            ->assertStatus(200)
            ->assertSee('Siti Penyewa');

        $this->actingAs($this->admin)->get(route('admin.transaksi.index', ['tab' => 'pembayaran']))
            ->assertStatus(200)
            ->assertSee($this->pembayaran->kode_invoice);

        $this->actingAs($this->admin)->get(route('admin.pembayaran.kwitansi', $this->pembayaran))
            ->assertStatus(200)
            ->assertSee($this->pembayaran->kode_invoice)
            ->assertSee('Kembali ke Monitoring Transaksi');

        $this->actingAs($this->admin)->delete(route('admin.ulasan.destroy', $this->ulasan))
            ->assertRedirect(route('admin.transaksi.index', ['tab' => 'ulasan']));
        $this->assertDatabaseMissing('ulasans', ['id' => $this->ulasan->id]);
    }
}


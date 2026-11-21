<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Sewa;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelolaUlasanFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $pemilik;
    private User $pemilikLain;
    private User $penyewa;
    private Kos $kosPemilik;
    private Kos $kosLain;
    private Kamar $kamar;
    private Sewa $sewa;
    private Ulasan $ulasan;

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
            'name' => 'Bambang Sudirman',
            'role' => 'penyewa',
            'nomor_hp' => '089988776655',
        ]);

        $this->kosPemilik = Kos::create([
            'user_id' => $this->pemilik->id,
            'nama_kos' => 'Kos Griya Nyaman',
            'alamat' => 'Jl. Nyaman No. 10',
            'tipe_kos' => 'campur',
            'deskripsi' => 'Kos strategis dan bersih',
        ]);

        $this->kosLain = Kos::create([
            'user_id' => $this->pemilikLain->id,
            'nama_kos' => 'Kos Pemilik Sebelah',
            'alamat' => 'Jl. Sebelah No. 99',
            'tipe_kos' => 'putra',
            'deskripsi' => 'Kos orang lain',
        ]);

        $this->kamar = Kamar::create([
            'kos_id' => $this->kosPemilik->id,
            'nomor_kamar' => 'A-01',
            'harga_per_bulan' => 800000,
            'fasilitas' => 'Kasur, WiFi',
            'status' => 'terisi',
        ]);

        $this->sewa = Sewa::create([
            'user_id' => $this->penyewa->id,
            'kamar_id' => $this->kamar->id,
            'kos_id' => $this->kosPemilik->id,
            'tanggal_mulai' => now()->subMonths(1)->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
            'status' => 'selesai',
            'catatan' => 'Sewa selesai lancar',
        ]);

        $this->ulasan = Ulasan::create([
            'user_id' => $this->penyewa->id,
            'kos_id' => $this->kosPemilik->id,
            'sewa_id' => $this->sewa->id,
            'rating' => 5,
            'komentar' => 'Tempatnya sangat bersih dan nyaman, pemilik ramah.',
        ]);
    }

    public function test_pemilik_can_view_ulasan_list(): void
    {
        $response = $this->actingAs($this->pemilik)->get(route('pemilik.ulasan.index'));

        $response->assertStatus(200);
        $response->assertSee('Ulasan &amp; Rating Kos', false);
        $response->assertSee('Bambang Sudirman');
        $response->assertSee('Tempatnya sangat bersih dan nyaman, pemilik ramah.');
        $response->assertSee('Kos Griya Nyaman');
    }

    public function test_penyewa_cannot_access_pemilik_ulasan_list(): void
    {
        $response = $this->actingAs($this->penyewa)->get(route('pemilik.ulasan.index'));

        $response->assertStatus(403);
    }

    public function test_pemilik_only_sees_reviews_for_their_own_kos(): void
    {
        $kamarLain = Kamar::create([
            'kos_id' => $this->kosLain->id,
            'nomor_kamar' => 'Z-99',
            'harga_per_bulan' => 700000,
            'status' => 'tersedia',
        ]);

        $sewaLain = Sewa::create([
            'user_id' => $this->penyewa->id,
            'kamar_id' => $kamarLain->id,
            'kos_id' => $this->kosLain->id,
            'tanggal_mulai' => now()->subMonths(1)->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
            'status' => 'selesai',
        ]);

        Ulasan::create([
            'user_id' => $this->penyewa->id,
            'kos_id' => $this->kosLain->id,
            'sewa_id' => $sewaLain->id,
            'rating' => 3,
            'komentar' => 'Komentar rahasia kos lain yang tidak boleh terlihat.',
        ]);

        $response = $this->actingAs($this->pemilik)->get(route('pemilik.ulasan.index'));

        $response->assertStatus(200);
        $response->assertSee('Tempatnya sangat bersih dan nyaman, pemilik ramah.');
        $response->assertDontSee('Komentar rahasia kos lain yang tidak boleh terlihat.');
    }

    public function test_pemilik_can_filter_ulasan_by_rating(): void
    {
        Ulasan::create([
            'user_id' => $this->penyewa->id,
            'kos_id' => $this->kosPemilik->id,
            'sewa_id' => $this->sewa->id,
            'rating' => 1,
            'komentar' => 'Komentar rating buruk bintang satu.',
        ]);

        $resBintangLima = $this->actingAs($this->pemilik)->get(route('pemilik.ulasan.index', ['rating' => 5]));
        $resBintangLima->assertStatus(200);
        $resBintangLima->assertSee('Tempatnya sangat bersih dan nyaman, pemilik ramah.');
        $resBintangLima->assertDontSee('Komentar rating buruk bintang satu.');

        $resBintangSatu = $this->actingAs($this->pemilik)->get(route('pemilik.ulasan.index', ['rating' => 1]));
        $resBintangSatu->assertStatus(200);
        $resBintangSatu->assertSee('Komentar rating buruk bintang satu.');
        $resBintangSatu->assertDontSee('Tempatnya sangat bersih dan nyaman, pemilik ramah.');
    }
}


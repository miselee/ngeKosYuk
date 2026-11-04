<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pembayaran;
use App\Models\Sewa;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenyewaController extends Controller
{
    /**
     * Dashboard penyewa - menampilkan rekomendasi kos.
     */
    public function dashboard()
    {
        $rekomendasiKos = Kos::with(['kamars' => function ($query) {
            $query->where('status', 'tersedia');
        }, 'ulasans'])
            ->whereHas('kamars', function ($query) {
                $query->where('status', 'tersedia');
            })
            ->take(8)
            ->get();

        $kos = $rekomendasiKos;

        return view('penyewa.dashboard', compact('kos', 'rekomendasiKos'));
    }

    /**
     * Cari kos dengan filter (lokasi, harga, tipe, periode).
     */
    public function cariKos(Request $request)
    {
        $query = Kos::with(['kamars' => function ($q) {
            $q->where('status', 'tersedia');
        }, 'ulasans'])
            ->whereHas('kamars', function ($q) {
                $q->where('status', 'tersedia');
            });

        // Filter berdasarkan lokasi (pencarian di nama_kos dan alamat)
        if ($request->filled('lokasi')) {
            $lokasi = $request->input('lokasi');
            $query->where(function ($q) use ($lokasi) {
                $q->where('nama_kos', 'like', "%{$lokasi}%")
                    ->orWhere('alamat', 'like', "%{$lokasi}%");
            });
        }

        // Filter berdasarkan tipe kos (dukung parameter 'tipe_kos' dan 'penghuni')
        $tipeKos = $request->input('tipe_kos', $request->input('penghuni'));
        if (!empty($tipeKos)) {
            $query->where('tipe_kos', strtolower($tipeKos));
        }

        // Filter berdasarkan harga minimum
        if ($request->filled('harga_min')) {
            $query->whereHas('kamars', function ($q) use ($request) {
                $q->where('status', 'tersedia')
                    ->where('harga_per_bulan', '>=', (int) $request->input('harga_min'));
            });
        }

        // Filter berdasarkan harga maksimum
        if ($request->filled('harga_max')) {
            $query->whereHas('kamars', function ($q) use ($request) {
                $q->where('status', 'tersedia')
                    ->where('harga_per_bulan', '<=', (int) $request->input('harga_max'));
            });
        }

        // Sorting
        if ($request->input('sort') === 'termurah') {
            $query->withMin(['kamars as min_harga' => function ($q) {
                $q->where('status', 'tersedia');
            }], 'harga_per_bulan')->orderBy('min_harga', 'asc');
        } elseif ($request->input('sort') === 'termahal') {
            $query->withMax(['kamars as max_harga' => function ($q) {
                $q->where('status', 'tersedia');
            }], 'harga_per_bulan')->orderBy('max_harga', 'desc');
        } else {
            $query->latest();
        }

        $daftarKos = $query->paginate(12)->withQueryString();
        $kos = $daftarKos;

        return view('penyewa.cari-kos', compact('kos', 'daftarKos'));
    }

    /**
     * Detail kos beserta kamar yang tersedia.
     */
    public function detailKos(int $kosId)
    {
        $kos = Kos::with([
            'kamars',
            'pemilik',
            'user',
            'ulasans.penyewa',
        ])->findOrFail($kosId);

        $kamarTersedia = $kos->kamars->where('status', 'tersedia');
        $rataRating = $kos->ulasans->avg('rating');

        return view('penyewa.detail-kos', compact('kos', 'kamarTersedia', 'rataRating'));
    }

    /**
     * Menampilkan form pengajuan sewa.
     */
    public function formPengajuanSewa(int $kamarId)
    {
        $kamar = Kamar::with('kos')->where('status', 'tersedia')->findOrFail($kamarId);

        return view('penyewa.pengajuan-sewa', compact('kamar'));
    }

    /**
     * Menyimpan pengajuan sewa.
     */
    public function ajukanSewa(Request $request)
    {
        $validated = $request->validate([
            'kamar_id' => 'required|exists:kamars,id',
            'kos_id' => 'required|exists:kos,id',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'catatan' => 'nullable|string|max:1000',
        ]);

        // Pastikan kamar masih tersedia
        $kamar = Kamar::where('id', $validated['kamar_id'])
            ->where('status', 'tersedia')
            ->firstOrFail();

        Sewa::create([
            'user_id' => Auth::id(),
            'kamar_id' => $validated['kamar_id'],
            'kos_id' => $validated['kos_id'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'status' => 'pending',
            'catatan' => $validated['catatan'],
        ]);

        return redirect(url('/penyewa/riwayat-sewa'))
            ->with('success', 'Pengajuan sewa berhasil dikirim. Menunggu persetujuan pemilik.');
    }

    /**
     * Daftar pembayaran penyewa.
     */
    public function daftarPembayaran()
    {
        $pembayarans = Pembayaran::whereHas('sewa', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->with('sewa.kamar.kos')
            ->latest()
            ->paginate(10);

        return view('penyewa.pembayaran', compact('pembayarans'));
    }

    /**
     * Menampilkan form pembayaran untuk sewa tertentu.
     */
    public function formPembayaran(int $sewaId)
    {
        $sewa = Sewa::with('kamar.kos')
            ->where('user_id', Auth::id())
            ->where('status', 'disetujui')
            ->findOrFail($sewaId);

        return view('penyewa.form-pembayaran', compact('sewa'));
    }

    /**
     * Menyimpan pembayaran dengan upload bukti.
     */
    public function bayar(Request $request)
    {
        $validated = $request->validate([
            'sewa_id' => 'required|exists:sewas,id',
            'jumlah' => 'required|integer|min:1',
            'metode_pembayaran' => 'required|in:transfer_bank,e_wallet,tunai',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'tanggal_bayar' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        // Pastikan sewa milik penyewa yang sedang login dan sudah disetujui
        $sewa = Sewa::where('id', $validated['sewa_id'])
            ->where('user_id', Auth::id())
            ->where('status', 'disetujui')
            ->firstOrFail();

        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
        }

        Pembayaran::create([
            'sewa_id' => $sewa->id,
            'jumlah' => $validated['jumlah'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'bukti_pembayaran' => $buktiPath,
            'status' => 'pending',
            'tanggal_bayar' => $validated['tanggal_bayar'],
            'keterangan' => $validated['keterangan'],
        ]);

        return redirect(url('/penyewa/pembayaran'))
            ->with('success', 'Pembayaran berhasil dikirim. Menunggu verifikasi pemilik.');
    }

    /**
     * Riwayat sewa penyewa.
     */
    public function riwayatSewa()
    {
        $sewas = Sewa::with(['kamar.kos', 'pembayarans'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('penyewa.riwayat-sewa', compact('sewas'));
    }

    /**
     * Menampilkan form ulasan.
     */
    public function formUlasan(int $sewaId)
    {
        $sewa = Sewa::with('kamar.kos')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['disetujui', 'selesai'])
            ->findOrFail($sewaId);

        // Cek apakah sudah pernah memberi ulasan
        $sudahUlasan = Ulasan::where('sewa_id', $sewaId)
            ->where('user_id', Auth::id())
            ->exists();

        if ($sudahUlasan) {
            return redirect(url('/penyewa/riwayat-sewa'))
                ->with('info', 'Anda sudah memberikan ulasan untuk sewa ini.');
        }

        return view('penyewa.form-ulasan', compact('sewa'));
    }

    /**
     * Menyimpan ulasan.
     */
    public function simpanUlasan(Request $request)
    {
        $validated = $request->validate([
            'sewa_id' => 'required|exists:sewas,id',
            'kos_id' => 'required|exists:kos,id',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|max:2000',
        ]);

        // Pastikan sewa milik penyewa yang login
        $sewa = Sewa::where('id', $validated['sewa_id'])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['disetujui', 'selesai'])
            ->firstOrFail();

        // Cek duplikasi ulasan
        $sudahUlasan = Ulasan::where('sewa_id', $sewa->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($sudahUlasan) {
            return redirect(url('/penyewa/riwayat-sewa'))
                ->with('info', 'Anda sudah memberikan ulasan untuk sewa ini.');
        }

        Ulasan::create([
            'user_id' => Auth::id(),
            'kos_id' => $validated['kos_id'],
            'sewa_id' => $sewa->id,
            'rating' => $validated['rating'],
            'komentar' => $validated['komentar'],
        ]);

        return redirect(url('/penyewa/riwayat-sewa'))
            ->with('success', 'Ulasan berhasil disimpan. Terima kasih atas ulasan Anda!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KelolaPembayaranController extends Controller
{
    /**
     * Tampilkan daftar konfirmasi pembayaran dari penyewa.
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $daftarKos = $user->kos()->get();
        $kosIds = $daftarKos->pluck('id');

        $baseQuery = Pembayaran::whereHas('sewa', function ($q) use ($kosIds) {
            $q->whereIn('kos_id', $kosIds);
        })->with(['sewa.penyewa', 'sewa.kamar', 'sewa.kos']);

        // Ringkasan statistik dalam satu query
        $stats = Pembayaran::whereHas('sewa', function ($q) use ($kosIds) {
            $q->whereIn('kos_id', $kosIds);
        })->selectRaw("
            COUNT(*) as total,
            COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending,
            COALESCE(SUM(CASE WHEN status = 'diverifikasi' THEN 1 ELSE 0 END), 0) as diverifikasi,
            COALESCE(SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END), 0) as ditolak,
            COALESCE(SUM(CASE WHEN status = 'diverifikasi' THEN jumlah ELSE 0 END), 0) as nominal
        ")->first();

        $totalPembayaran = (int) ($stats->total ?? 0);
        $totalPending = (int) ($stats->pending ?? 0);
        $totalDiverifikasi = (int) ($stats->diverifikasi ?? 0);
        $totalDitolak = (int) ($stats->ditolak ?? 0);
        $totalNominalMasuk = (int) ($stats->nominal ?? 0);

        // Query dengan filter
        $query = clone $baseQuery;

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('kos_id')) {
            $kosId = $request->input('kos_id');
            $query->whereHas('sewa', function ($q) use ($kosId) {
                $q->where('kos_id', $kosId);
            });
        }

        if ($request->filled('metode')) {
            $query->where('metode_pembayaran', $request->input('metode'));
        }

        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('sewa.penyewa', function ($p) use ($keyword) {
                    $p->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('nomor_hp', 'like', "%{$keyword}%");
                })->orWhereHas('sewa.kamar', function ($k) use ($keyword) {
                    $k->where('nomor_kamar', 'like', "%{$keyword}%");
                })->orWhere('keterangan', 'like', "%{$keyword}%");
            });
        }

        $pembayarans = $query->latest('tanggal_bayar')->latest('id')->paginate(10)->withQueryString();

        return view('pemilik.pembayaran.index', compact(
            'pembayarans',
            'daftarKos',
            'totalPembayaran',
            'totalPending',
            'totalDiverifikasi',
            'totalDitolak',
            'totalNominalMasuk'
        ));
    }

    /**
     * Tampilkan halaman kwitansi resmi pembayaran untuk pemilik kos.
     */
    public function kwitansi(Pembayaran $pembayaran): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->ensurePembayaranBelongsToUser($pembayaran, $user->id);

        $pembayaran->loadMissing(['sewa.penyewa', 'sewa.kamar.kos.pemilik']);

        return view('pembayaran.kwitansi', compact('pembayaran'));
    }

    /**
     * Konfirmasi dan verifikasi pembayaran penyewa.
     */
    public function verifikasi(Pembayaran $pembayaran): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->ensurePembayaranBelongsToUser($pembayaran, $user->id);

        if ($pembayaran->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pembayaran dengan status Menunggu yang dapat diverifikasi.');
        }

        $pembayaran->update(['status' => 'diverifikasi']);

        if ($pembayaran->sewa && $pembayaran->sewa->kamar && $pembayaran->sewa->kamar->status !== 'terisi') {
            $pembayaran->sewa->kamar->update(['status' => 'terisi']);
        }

        $namaPenyewa = $pembayaran->sewa->penyewa->name ?? 'Penyewa';
        $nominal = number_format($pembayaran->jumlah, 0, ',', '.');

        return redirect()->back()
            ->with('success', "Pembayaran {$pembayaran->kode_invoice} sebesar Rp {$nominal} dari {$namaPenyewa} berhasil diverifikasi!");
    }

    /**
     * Tolak bukti pembayaran dari penyewa.
     */
    public function tolak(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->ensurePembayaranBelongsToUser($pembayaran, $user->id);

        if ($pembayaran->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pembayaran dengan status Menunggu yang dapat ditolak.');
        }

        $validated = $request->validate([
            'alasan_penolakan' => 'nullable|string|max:500',
        ]);

        $catatanLama = $pembayaran->keterangan ? $pembayaran->keterangan."\n" : '';
        $catatanBaru = ! empty($validated['alasan_penolakan'])
            ? $catatanLama.'[Ditolak Pemilik: '.$validated['alasan_penolakan'].']'
            : $pembayaran->keterangan;

        $pembayaran->update([
            'status' => 'ditolak',
            'keterangan' => $catatanBaru,
        ]);

        $namaPenyewa = $pembayaran->sewa->penyewa->name ?? 'Penyewa';

        return redirect()->back()
            ->with('success', "Pembayaran dari {$namaPenyewa} telah ditolak.");
    }

    /**
     * Pastikan pembayaran terkait dengan kos milik pemilik yang sedang login.
     */
    private function ensurePembayaranBelongsToUser(Pembayaran $pembayaran, int $userId): void
    {
        $pembayaran->loadMissing(['sewa.kos', 'sewa.penyewa']);

        if (! $pembayaran->sewa || ! $pembayaran->sewa->kos || $pembayaran->sewa->kos->user_id !== $userId) {
            abort(403, 'Anda tidak memiliki hak akses untuk memverifikasi data pembayaran ini.');
        }
    }
}

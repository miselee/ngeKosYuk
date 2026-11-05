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

        // Ringkasan statistik
        $totalPembayaran = (clone $baseQuery)->count();
        $totalPending = (clone $baseQuery)->where('status', 'pending')->count();
        $totalDiverifikasi = (clone $baseQuery)->where('status', 'diverifikasi')->count();
        $totalDitolak = (clone $baseQuery)->where('status', 'ditolak')->count();
        $totalNominalMasuk = (clone $baseQuery)->where('status', 'diverifikasi')->sum('jumlah');

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

        $namaPenyewa = $pembayaran->sewa->penyewa->name ?? 'Penyewa';
        $nominal = number_format($pembayaran->jumlah, 0, ',', '.');

        return redirect()->back()
            ->with('success', "Pembayaran sebesar Rp {$nominal} dari {$namaPenyewa} berhasil diverifikasi!");
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

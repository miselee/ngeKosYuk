<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KelolaPenyewaanController extends Controller
{
    /**
     * Tampilkan daftar pengajuan dan riwayat penyewaan untuk kos milik pemilik.
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $daftarKos = $user->kos()->get();
        $kosIds = $daftarKos->pluck('id');

        $baseQuery = Sewa::whereIn('kos_id', $kosIds)
            ->with(['penyewa', 'kamar', 'kos', 'pembayarans']);

        // Hitung total tiap status
        $totalSewa = (clone $baseQuery)->count();
        $totalPending = (clone $baseQuery)->where('status', 'pending')->count();
        $totalDisetujui = (clone $baseQuery)->where('status', 'disetujui')->count();
        $totalDitolak = (clone $baseQuery)->where('status', 'ditolak')->count();
        $totalSelesai = (clone $baseQuery)->where('status', 'selesai')->count();

        // Query dengan filter
        $query = clone $baseQuery;

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('kos_id')) {
            $query->where('kos_id', $request->input('kos_id'));
        }

        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('penyewa', function ($p) use ($keyword) {
                    $p->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('nomor_hp', 'like', "%{$keyword}%");
                })->orWhereHas('kamar', function ($k) use ($keyword) {
                    $k->where('nomor_kamar', 'like', "%{$keyword}%");
                });
            });
        }

        $sewas = $query->latest()->paginate(10)->withQueryString();

        return view('pemilik.penyewaan.index', compact(
            'sewas',
            'daftarKos',
            'totalSewa',
            'totalPending',
            'totalDisetujui',
            'totalDitolak',
            'totalSelesai'
        ));
    }

    /**
     * Setujui pengajuan sewa dari penyewa.
     */
    public function setujui(Sewa $sewa): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->ensureSewaBelongsToUser($sewa, $user->id);

        if ($sewa->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status Menunggu yang dapat disetujui.');
        }

        $sewa->update(['status' => 'disetujui']);

        // Tandai kamar menjadi terisi
        if ($sewa->kamar) {
            $sewa->kamar->update(['status' => 'terisi']);
        }

        $namaPenyewa = $sewa->penyewa->name ?? 'Penyewa';
        $nomorKamar = $sewa->kamar->nomor_kamar ?? '-';

        return redirect()->back()
            ->with('success', "Pengajuan sewa dari {$namaPenyewa} untuk kamar {$nomorKamar} berhasil disetujui!");
    }

    /**
     * Tolak pengajuan sewa dari penyewa.
     */
    public function tolak(Request $request, Sewa $sewa): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->ensureSewaBelongsToUser($sewa, $user->id);

        if ($sewa->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status Menunggu yang dapat ditolak.');
        }

        $validated = $request->validate([
            'alasan_penolakan' => 'nullable|string|max:500',
        ]);

        $catatanLama = $sewa->catatan ? $sewa->catatan."\n" : '';
        $catatanBaru = ! empty($validated['alasan_penolakan'])
            ? $catatanLama.'[Catatan Pemilik: '.$validated['alasan_penolakan'].']'
            : $sewa->catatan;

        $sewa->update([
            'status' => 'ditolak',
            'catatan' => $catatanBaru,
        ]);

        $namaPenyewa = $sewa->penyewa->name ?? 'Penyewa';

        return redirect()->back()
            ->with('success', "Pengajuan sewa dari {$namaPenyewa} telah ditolak.");
    }

    /**
     * Tandai masa sewa telah selesai.
     */
    public function selesai(Sewa $sewa): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->ensureSewaBelongsToUser($sewa, $user->id);

        if ($sewa->status !== 'disetujui') {
            return redirect()->back()->with('error', 'Hanya sewa yang sedang aktif/disetujui yang dapat ditandai selesai.');
        }

        $sewa->update(['status' => 'selesai']);

        // Jika tidak ada sewa aktif lain di kamar ini, kembalikan status kamar jadi tersedia
        if ($sewa->kamar) {
            $adaSewaAktifLain = Sewa::where('kamar_id', $sewa->kamar_id)
                ->where('id', '!=', $sewa->id)
                ->where('status', 'disetujui')
                ->exists();

            if (! $adaSewaAktifLain) {
                $sewa->kamar->update(['status' => 'tersedia']);
            }
        }

        $nomorKamar = $sewa->kamar->nomor_kamar ?? '-';

        return redirect()->back()
            ->with('success', "Masa sewa kamar {$nomorKamar} telah ditandai selesai. Kamar kini kembali berstatus Tersedia.");
    }

    /**
     * Pastikan data sewa terkait dengan kos yang dimiliki pemilik saat ini.
     */
    private function ensureSewaBelongsToUser(Sewa $sewa, int $userId): void
    {
        $sewa->loadMissing(['kos', 'kamar', 'penyewa']);

        if (! $sewa->kos || $sewa->kos->user_id !== $userId) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola pengajuan sewa ini.');
        }
    }
}

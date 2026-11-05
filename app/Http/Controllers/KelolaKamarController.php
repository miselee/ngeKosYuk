<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Kos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KelolaKamarController extends Controller
{
    /**
     * Tampilkan daftar kamar yang dikelola oleh pemilik kos.
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $daftarKos = $user->kos()->get();
        $kosIds = $daftarKos->pluck('id');

        $baseQuery = Kamar::whereIn('kos_id', $kosIds)->with('kos');

        // Ringkasan statistik keseluruhan kamar milik pemilik kos
        $totalKamar = (clone $baseQuery)->count();
        $totalTersedia = (clone $baseQuery)->where('status', 'tersedia')->count();
        $totalTerisi = (clone $baseQuery)->where('status', 'terisi')->count();

        // Query dengan filter untuk tampilan tabel
        $query = clone $baseQuery;

        if ($request->filled('kos_id')) {
            $query->where('kos_id', $request->input('kos_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_kamar', 'like', "%{$keyword}%")
                    ->orWhere('fasilitas', 'like', "%{$keyword}%");
            });
        }

        $kamars = $query->latest()->paginate(10)->withQueryString();

        return view('pemilik.kelola-kamar.index', compact(
            'kamars',
            'daftarKos',
            'totalKamar',
            'totalTersedia',
            'totalTerisi'
        ));
    }

    /**
     * Tampilkan form untuk menambahkan kamar baru oleh pemilik kos.
     */
    public function create(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $daftarKos = $user->kos()->get();

        return view('pemilik.kelola-kamar.create', compact('daftarKos'));
    }

    /**
     * Simpan data kamar baru.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'kos_id' => [
                'required',
                Rule::exists('kos', 'id')->where('user_id', $user->id),
            ],
            'nomor_kamar' => ['required', 'string', 'max:50'],
            'harga_per_bulan' => ['required', 'integer', 'min:10000'],
            'fasilitas' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:tersedia,terisi'],
        ]);

        $kamar = Kamar::create($validated);

        return redirect()->route('pemilik.kelola-kamar.index')
            ->with('success', "Kamar {$kamar->nomor_kamar} berhasil ditambahkan!");
    }

    /**
     * Tampilkan form untuk mengedit kamar oleh pemilik kos.
     */
    public function edit(Kamar $kamar): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->ensureKamarBelongsToUser($kamar, $user->id);

        $daftarKos = $user->kos()->get();

        return view('pemilik.kelola-kamar.edit', compact('kamar', 'daftarKos'));
    }

    /**
     * Perbarui data kamar oleh pemilik kos.
     */
    public function update(Request $request, Kamar $kamar): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->ensureKamarBelongsToUser($kamar, $user->id);

        $validated = $request->validate([
            'kos_id' => [
                'required',
                Rule::exists('kos', 'id')->where('user_id', $user->id),
            ],
            'nomor_kamar' => ['required', 'string', 'max:50'],
            'harga_per_bulan' => ['required', 'integer', 'min:10000'],
            'fasilitas' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:tersedia,terisi'],
        ]);

        $kamar->update($validated);

        return redirect()->route('pemilik.kelola-kamar.index')
            ->with('success', "Data kamar {$kamar->nomor_kamar} berhasil diperbarui!");
    }

    /**
     * Hapus kamar dari database jika tidak ada sewa aktif.
     */
    public function destroy(Kamar $kamar): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->ensureKamarBelongsToUser($kamar, $user->id);

        $hasActiveSewa = $kamar->sewas()
            ->whereIn('status', ['pending', 'disetujui'])
            ->exists();

        if ($hasActiveSewa) {
            return redirect()->route('pemilik.kelola-kamar.index')
                ->with('error', "Kamar {$kamar->nomor_kamar} tidak dapat dihapus karena masih terkait penyewaan yang aktif atau sedang diproses.");
        }

        $nomorKamar = $kamar->nomor_kamar;
        $kamar->delete();

        return redirect()->route('pemilik.kelola-kamar.index')
            ->with('success', "Kamar {$nomorKamar} berhasil dihapus.");
    }

    /**
     * Ubah status ketersediaan kamar secara cepat (tersedia / terisi).
     */
    public function toggleStatus(Kamar $kamar): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->ensureKamarBelongsToUser($kamar, $user->id);

        $statusBaru = $kamar->status === 'tersedia' ? 'terisi' : 'tersedia';
        $kamar->update(['status' => $statusBaru]);

        return redirect()->back()
            ->with('success', "Status kamar {$kamar->nomor_kamar} berhasil diubah menjadi ".ucfirst($statusBaru).'.');
    }

    /**
     * Pastikan kamar yang dikelola adalah milik pemilik kos saat ini.
     */
    private function ensureKamarBelongsToUser(Kamar $kamar, int $userId): void
    {
        $kamar->loadMissing('kos');

        if (! $kamar->kos || $kamar->kos->user_id !== $userId) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola kamar ini.');
        }
    }
}


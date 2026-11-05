<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Kos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PemilikController extends Controller
{
    /**
     * Tampilkan ringkasan dashboard pemilik kos.
     */
    public function dashboard(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $daftarKos = $user->kos()->with('kamars')->get();
        $kosIds = $daftarKos->pluck('id');

        $totalKos = $daftarKos->count();
        $totalKamar = Kamar::whereIn('kos_id', $kosIds)->count();
        $kamarTersedia = Kamar::whereIn('kos_id', $kosIds)->where('status', 'tersedia')->count();
        $kamarTerisi = Kamar::whereIn('kos_id', $kosIds)->where('status', 'terisi')->count();

        $kamarTerbaru = Kamar::whereIn('kos_id', $kosIds)
            ->with('kos')
            ->latest()
            ->take(6)
            ->get();

        return view('pemilik.dashboard', compact(
            'daftarKos',
            'totalKos',
            'totalKamar',
            'kamarTersedia',
            'kamarTerisi',
            'kamarTerbaru'
        ));
    }

    /**
     * Tambah kos baru cepat (jika pemilik belum memiliki kos).
     */
    public function quickStoreKos(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kos' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'tipe_kos' => 'required|in:putra,putri,campur',
            'deskripsi' => 'nullable|string|max:1000',
            'foto' => 'nullable|url|max:255',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->kos()->create([
            'nama_kos' => $validated['nama_kos'],
            'alamat' => $validated['alamat'],
            'tipe_kos' => $validated['tipe_kos'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'foto' => $validated['foto'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Kos berhasil didaftarkan! Sekarang Anda dapat menambahkan kamar.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KelolaUlasanController extends Controller
{
    /**
     * Menampilkan daftar ulasan kos yang masuk dari penyewa untuk kos milik pemilik.
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $daftarKos = $user->kos()->get();
        $kosIds = $daftarKos->pluck('id');

        $baseQuery = Ulasan::whereIn('kos_id', $kosIds)
            ->with(['penyewa', 'kos', 'sewa.kamar']);

        // Statistik ringkasan ulasan
        $totalUlasan = (clone $baseQuery)->count();
        $rataRataRating = $totalUlasan > 0 ? round((clone $baseQuery)->avg('rating'), 1) : 0;

        // Distribusi bintang
        $bintangCount = [
            5 => (clone $baseQuery)->where('rating', 5)->count(),
            4 => (clone $baseQuery)->where('rating', 4)->count(),
            3 => (clone $baseQuery)->where('rating', 3)->count(),
            2 => (clone $baseQuery)->where('rating', 2)->count(),
            1 => (clone $baseQuery)->where('rating', 1)->count(),
        ];

        // Query filter
        $query = clone $baseQuery;

        if ($request->filled('kos_id')) {
            $query->where('kos_id', $request->input('kos_id'));
        }

        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->input('rating'));
        }

        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('komentar', 'like', "%{$keyword}%")
                    ->orWhereHas('penyewa', function ($p) use ($keyword) {
                        $p->where('name', 'like', "%{$keyword}%");
                    })->orWhereHas('sewa.kamar', function ($k) use ($keyword) {
                        $k->where('nomor_kamar', 'like', "%{$keyword}%");
                    });
            });
        }

        $ulasans = $query->latest()->paginate(10)->withQueryString();

        return view('pemilik.ulasan.index', compact(
            'ulasans',
            'daftarKos',
            'totalUlasan',
            'rataRataRating',
            'bintangCount'
        ));
    }
}


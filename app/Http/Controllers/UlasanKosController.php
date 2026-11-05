<?php

namespace App\Http\Controllers;

use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UlasanKosController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        $daftarKos = $user->kos()->get();

        $query = Ulasan::whereIn('kos_id', $daftarKos->modelKeys())
            ->with(['kos', 'penyewa'])
            ->latest();

        if ($request->filled('kos_id')) {
            $query->where('kos_id', $request->input('kos_id'));
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->input('rating'));
        }

        $ulasans = $query->paginate(10)->withQueryString();
        $rataRataRating = Ulasan::whereIn('kos_id', $daftarKos->modelKeys())->avg('rating');

        return view('pemilik.ulasan.index', compact('daftarKos', 'ulasans', 'rataRataRating'));
    }
}

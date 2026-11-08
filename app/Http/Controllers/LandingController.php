<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    
    public function index()
    {
        $rekomendasiKos = Kos::with([
            'kamars' => function ($query) {
                $query->where('status', 'tersedia');
            }
        ])
        ->whereHas('kamars', function ($query) {
            $query->where('status', 'tersedia');
        })
        ->latest()
        ->take(8)
        ->get();

        return view('landing', compact('rekomendasiKos'));
    }

}

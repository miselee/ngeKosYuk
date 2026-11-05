<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KelolaKosController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $kos = $user->kos()
            ->withCount(['kamars', 'ulasans'])
            ->withAvg('ulasans', 'rating')
            ->latest()
            ->paginate(10);

        return view('pemilik.kelola-kos.index', compact('kos'));
    }

    public function create(): View
    {
        return view('pemilik.kelola-kos.form', ['kos' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $user->kos()->create($this->validateKos($request));

        return redirect()->route('pemilik.kelola-kos.index')
            ->with('success', 'Data kos berhasil ditambahkan.');
    }

    public function edit(Kos $kos): View
    {
        $this->ensureKosBelongsToUser($kos);

        return view('pemilik.kelola-kos.form', compact('kos'));
    }

    public function update(Request $request, Kos $kos): RedirectResponse
    {
        $this->ensureKosBelongsToUser($kos);
        $kos->update($this->validateKos($request));

        return redirect()->route('pemilik.kelola-kos.index')
            ->with('success', 'Data kos berhasil diperbarui.');
    }

    public function destroy(Kos $kos): RedirectResponse
    {
        $this->ensureKosBelongsToUser($kos);

        if ($kos->sewas()->exists()) {
            return redirect()->route('pemilik.kelola-kos.index')
                ->with('error', 'Kos tidak dapat dihapus karena memiliki riwayat penyewaan.');
        }

        $kos->delete();

        return redirect()->route('pemilik.kelola-kos.index')
            ->with('success', 'Data kos berhasil dihapus.');
    }

    /**
     * @return array{nama_kos: string, alamat: string, tipe_kos: string, deskripsi: ?string, foto: ?string}
     */
    private function validateKos(Request $request): array
    {
        return $request->validate([
            'nama_kos' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:5000'],
            'tipe_kos' => ['required', 'in:putra,putri,campur'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'foto' => ['nullable', 'url', 'max:255'],
        ]);
    }

    private function ensureKosBelongsToUser(Kos $kos): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($kos->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola kos ini.');
        }
    }
}

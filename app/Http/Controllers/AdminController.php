<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pembayaran;
use App\Models\Sewa;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $userStats = User::selectRaw("
            COUNT(*) as total,
            COALESCE(SUM(CASE WHEN role = 'pemilik' THEN 1 ELSE 0 END), 0) as pemilik,
            COALESCE(SUM(CASE WHEN role = 'penyewa' THEN 1 ELSE 0 END), 0) as penyewa
        ")->first();

        $totalUsers = (int) ($userStats->total ?? 0);
        $totalPemilik = (int) ($userStats->pemilik ?? 0);
        $totalPenyewa = (int) ($userStats->penyewa ?? 0);

        $totalKos = Kos::count();

        $kamarStats = Kamar::selectRaw("
            COUNT(*) as total,
            COALESCE(SUM(CASE WHEN status = 'tersedia' THEN 1 ELSE 0 END), 0) as tersedia,
            COALESCE(SUM(CASE WHEN status = 'terisi' THEN 1 ELSE 0 END), 0) as terisi
        ")->first();

        $totalKamar = (int) ($kamarStats->total ?? 0);
        $kamarTersedia = (int) ($kamarStats->tersedia ?? 0);
        $kamarTerisi = (int) ($kamarStats->terisi ?? 0);

        $sewaStats = Sewa::selectRaw("
            COALESCE(SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END), 0) as aktif,
            COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending
        ")->first();

        $sewaAktif = (int) ($sewaStats->aktif ?? 0);
        $sewaPending = (int) ($sewaStats->pending ?? 0);

        $bayarStats = Pembayaran::selectRaw("
            COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending,
            COALESCE(SUM(CASE WHEN status = 'diverifikasi' THEN jumlah ELSE 0 END), 0) as pendapatan
        ")->first();

        $pembayaranPending = (int) ($bayarStats->pending ?? 0);
        $totalPendapatanPlatform = (int) ($bayarStats->pendapatan ?? 0);

        $sewaTerbaru = Sewa::with(['penyewa', 'kamar.kos'])
            ->latest()
            ->take(6)
            ->get();

        $pembayaranTerbaru = Pembayaran::with(['sewa.penyewa', 'sewa.kamar.kos'])
            ->latest('tanggal_bayar')
            ->latest('id')
            ->take(6)
            ->get();

        $userTerbaru = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalPemilik',
            'totalPenyewa',
            'totalKos',
            'totalKamar',
            'kamarTersedia',
            'kamarTerisi',
            'sewaAktif',
            'sewaPending',
            'pembayaranPending',
            'totalPendapatanPlatform',
            'sewaTerbaru',
            'pembayaranTerbaru',
            'userTerbaru'
        ));
    }

    public function users(Request $request): View
    {
        $userStats = User::selectRaw("
            COUNT(*) as total,
            COALESCE(SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END), 0) as admin_count,
            COALESCE(SUM(CASE WHEN role = 'pemilik' THEN 1 ELSE 0 END), 0) as pemilik_count,
            COALESCE(SUM(CASE WHEN role = 'penyewa' THEN 1 ELSE 0 END), 0) as penyewa_count
        ")->first();

        $totalUsers = (int) ($userStats->total ?? 0);
        $totalAdmin = (int) ($userStats->admin_count ?? 0);
        $totalPemilik = (int) ($userStats->pemilik_count ?? 0);
        $totalPenyewa = (int) ($userStats->penyewa_count ?? 0);

        $query = User::withCount(['kos', 'sewas']);

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('nomor_hp', 'like', "%{$keyword}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'totalAdmin',
            'totalPemilik',
            'totalPenyewa'
        ));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'nomor_hp' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['admin', 'pemilik', 'penyewa'])],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nomor_hp' => $validated['nomor_hp'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$validated['name']} ({$validated['role']}) berhasil ditambahkan.");
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'nomor_hp' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['admin', 'pemilik', 'penyewa'])],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        // Prevent the currently signed-in admin from demoting their own role.
        if ($user->id === Auth::id() && $validated['role'] !== 'admin') {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat mengubah peran akun Admin yang sedang digunakan.');
        }

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nomor_hp' => $validated['nomor_hp'] ?? null,
            'role' => $validated['role'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        return redirect()->route('admin.users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroyUser(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Akun Admin yang sedang aktif tidak dapat dihapus.');
        }

        $nama = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Akun pengguna {$nama} telah dihapus dari sistem.");
    }

    public function kos(Request $request): View
    {
        $totalKos = Kos::count();

        $kamarStats = Kamar::selectRaw("
            COUNT(*) as total,
            COALESCE(SUM(CASE WHEN status = 'tersedia' THEN 1 ELSE 0 END), 0) as tersedia,
            COALESCE(SUM(CASE WHEN status = 'terisi' THEN 1 ELSE 0 END), 0) as terisi
        ")->first();

        $totalKamar = (int) ($kamarStats->total ?? 0);
        $totalTersedia = (int) ($kamarStats->tersedia ?? 0);
        $totalTerisi = (int) ($kamarStats->terisi ?? 0);

        $query = Kos::with(['pemilik', 'kamars'])
            ->withCount(['kamars', 'sewas', 'ulasans']);

        if ($request->filled('tipe_kos')) {
            $query->where('tipe_kos', $request->input('tipe_kos'));
        }

        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_kos', 'like', "%{$keyword}%")
                    ->orWhere('alamat', 'like', "%{$keyword}%")
                    ->orWhereHas('pemilik', function ($p) use ($keyword) {
                        $p->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        $daftarKos = $query->latest()->paginate(10)->withQueryString();

        return view('admin.kos.index', compact(
            'daftarKos',
            'totalKos',
            'totalKamar',
            'totalTersedia',
            'totalTerisi'
        ));
    }

    public function destroyKos(Kos $kos): RedirectResponse
    {
        $hasActiveSewa = $kos->sewas()->whereIn('status', ['pending', 'disetujui'])->exists();

        if ($hasActiveSewa) {
            return redirect()->back()
                ->with('error', "Properti {$kos->nama_kos} tidak dapat dihapus karena masih memiliki kontrak sewa aktif atau menunggu.");
        }

        $namaKos = $kos->nama_kos;
        $kos->delete();

        return redirect()->route('admin.kos.index')
            ->with('success', "Data properti {$namaKos} berhasil dihapus.");
    }

    public function transaksi(Request $request): View
    {
        $tab = $request->input('tab', 'sewa');

        $sewaStats = Sewa::selectRaw("
            COUNT(*) as total,
            COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending,
            COALESCE(SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END), 0) as aktif
        ")->first();

        $totalSewa = (int) ($sewaStats->total ?? 0);
        $sewaPending = (int) ($sewaStats->pending ?? 0);
        $sewaAktif = (int) ($sewaStats->aktif ?? 0);

        $bayarStats = Pembayaran::selectRaw("
            COUNT(*) as total,
            COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending,
            COALESCE(SUM(CASE WHEN status = 'diverifikasi' THEN jumlah ELSE 0 END), 0) as lunas
        ")->first();

        $totalPembayaran = (int) ($bayarStats->total ?? 0);
        $bayarPending = (int) ($bayarStats->pending ?? 0);
        $totalNominalLunas = (int) ($bayarStats->lunas ?? 0);

        $sewas = null;
        $pembayarans = null;
        $ulasans = null;
        $totalUlasan = Ulasan::count();

        if ($tab === 'sewa') {
            $sewaQuery = Sewa::with(['penyewa', 'kamar.kos.pemilik']);
            if ($request->filled('status')) {
                $sewaQuery->where('status', $request->input('status'));
            }
            if ($request->filled('q')) {
                $keyword = $request->input('q');
                $sewaQuery->where(function ($q) use ($keyword) {
                    $q->whereHas('penyewa', fn ($p) => $p->where('name', 'like', "%{$keyword}%"))
                        ->orWhereHas('kos', fn ($k) => $k->where('nama_kos', 'like', "%{$keyword}%"))
                        ->orWhereHas('kamar', fn ($km) => $km->where('nomor_kamar', 'like', "%{$keyword}%"));
                });
            }
            $sewas = $sewaQuery->latest()->paginate(10, ['*'], 'sewa_page')->withQueryString();
        } elseif ($tab === 'pembayaran') {
            $bayarQuery = Pembayaran::with(['sewa.penyewa', 'sewa.kamar.kos.pemilik']);
            if ($request->filled('status')) {
                $bayarQuery->where('status', $request->input('status'));
            }
            if ($request->filled('q')) {
                $keyword = $request->input('q');
                $bayarQuery->where(function ($q) use ($keyword) {
                    $q->whereHas('sewa.penyewa', fn ($p) => $p->where('name', 'like', "%{$keyword}%"))
                        ->orWhereHas('sewa.kamar.kos', fn ($k) => $k->where('nama_kos', 'like', "%{$keyword}%"))
                        ->orWhere('keterangan', 'like', "%{$keyword}%");
                });
            }
            $pembayarans = $bayarQuery->latest('tanggal_bayar')->latest('id')->paginate(10, ['*'], 'bayar_page')->withQueryString();
        } else {
            $ulasans = Ulasan::with(['penyewa', 'kos'])
                ->latest()
                ->paginate(10, ['*'], 'ulasan_page')
                ->withQueryString();
            $totalUlasan = $ulasans->total();
        }

        return view('admin.transaksi.index', compact(
            'tab',
            'totalSewa',
            'sewaPending',
            'sewaAktif',
            'totalPembayaran',
            'bayarPending',
            'totalNominalLunas',
            'totalUlasan',
            'sewas',
            'pembayarans',
            'ulasans'
        ));
    }

    public function kwitansi(Pembayaran $pembayaran): View
    {
        $pembayaran->loadMissing(['sewa.penyewa', 'sewa.kamar.kos.pemilik']);

        return view('pembayaran.kwitansi', compact('pembayaran'));
    }

    public function destroyUlasan(Ulasan $ulasan): RedirectResponse
    {
        $ulasan->delete();

        return redirect()->route('admin.transaksi.index', ['tab' => 'ulasan'])
            ->with('success', 'Ulasan penghuni telah dihapus oleh moderator Admin.');
    }
}


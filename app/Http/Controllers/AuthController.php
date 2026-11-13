<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect($this->redirectPathForRole(Auth::user()->role));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect($this->redirectPathForRole(Auth::user()->role));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->flush();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $sessionCookie = Cookie::forget(config('session.cookie'));
        $recallerCookie = Cookie::forget(Auth::guard('web')->getRecallerName());

        return redirect('/login')
            ->with('success', 'Berhasil keluar dari akun.')
            ->withCookie($sessionCookie)
            ->withCookie($recallerCookie);
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect($this->redirectPathForRole(Auth::user()->role));
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nomor_hp' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:pemilik,penyewa',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'nomor_hp' => $request->nomor_hp,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect($this->redirectPathForRole($user->role));
    }

    public function sessionCheck(Request $request)
    {
        return response()->json([
            'authenticated' => Auth::check(),
        ]);
    }

    private function redirectPathForRole(string $role): string
    {
        return match ($role) {
            'admin' => '/admin/dashboard',
            'pemilik' => '/pemilik/dashboard',
            default => '/penyewa/dashboard',
        };
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'nomor_hp' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return redirect()->route('profile.edit')->with('success', 'Data profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
        ]);

        $user->update(['password' => Hash::make($request->input('password'))]);

        return redirect()->route('profile.edit')->with('success_password', 'Kata sandi berhasil diganti.');
    }
}


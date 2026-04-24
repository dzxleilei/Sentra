<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view("auth.login");
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            "email" => ["required", "email"],
            "password" => ["required"],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect berdasarkan role
            $user = Auth::user();

            if ($user->role === 'peminjam' && !str_ends_with(strtolower($user->email), '@itbss.ac.id')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun peminjam wajib menggunakan email institusi @itbss.ac.id.',
                ])->onlyInput('email');
            }

            switch($user->role) {
                case 'admin':
                    if ((int) $user->penalty_points > AppSetting::integer('penalty_block_threshold', 20) && empty($user->blocked_at)) {
                        $user->forceFill(['blocked_at' => now()])->save();
                    }
                    return redirect()->route('admin.dashboard');
                case 'verifikator':
                    return redirect()->route('verifikator.dashboard');
                case 'peminjam':
                default:
                    $threshold = AppSetting::integer('penalty_block_threshold', 20);

                    if ((int) $user->penalty_points > $threshold && empty($user->blocked_at)) {
                        $user->forceFill(['blocked_at' => now()])->save();
                    }

                    if (!empty($user->blocked_at) || (int) $user->penalty_points > $threshold) {
                        return redirect()->route('peminjam.dashboard')->with('show_blocked_modal', true);
                    }

                    return redirect()->route('peminjam.dashboard');
            }
        }

        return back()->withErrors([
            "email" => "The provided credentials do not match our records.",
        ])->onlyInput("email");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/");
    }

    public function showChangePasswordForm()
    {
        $user = Auth::user();
        $threshold = AppSetting::integer('penalty_block_threshold', 20);

        if ($user && $user->role === 'peminjam') {
            return view('peminjam.pengaturan');
        }

        return view("auth.change-password", [
            'penaltyBlockThreshold' => $threshold,
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini diperlukan.',
            'new_password.required' => 'Password baru diperlukan.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    public function updatePenaltyThreshold(Request $request)
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'penalty_block_threshold' => ['required', 'integer', 'min:1'],
        ]);

        AppSetting::setValue('penalty_block_threshold', $validated['penalty_block_threshold']);

        return back()->with('success', 'Batas poin penalti sebelum blokir berhasil diperbarui.');
    }
}


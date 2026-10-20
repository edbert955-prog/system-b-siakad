<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectForRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'identity' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string', 'max:200'],
        ]);

        $identity = trim($credentials['identity']);
        $user = User::query()
            ->where('email', $identity)
            ->orWhere('nomor_induk', $identity)
            ->first();

        if (!$user || !Hash::check($credentials['password'], (string) $user->password)) {
            return back()->withErrors([
                'identity' => 'Email/Nomor Induk atau password tidak sesuai.',
            ])->onlyInput('identity');
        }

        $role = strtolower(trim((string) $user->role));
        $adminRoles = [
            'admin', 'administrator', 'super-admin', 'super_admin',
            'admin akademik', 'administrator akademik', 'admin siakadu',
            'baak', 'staf akademik', 'staff akademik', 'staff_akademik', 'staff',
        ];
        $studentRoles = ['mahasiswa', 'student'];

        if (in_array($role, $studentRoles, true)) {
            $npm = trim((string) $user->nomor_induk);
            if ($npm === '' || !Mahasiswa::where('npm', $npm)->exists()) {
                return back()->withErrors([
                    'identity' => 'Akun belum terhubung dengan data mahasiswa. Minta administrator menghubungkan Nomor Induk dengan NPM.',
                ])->onlyInput('identity');
            }
        } elseif (!in_array($role, $adminRoles, true)) {
            return back()->withErrors([
                'identity' => 'Role akun belum memiliki portal yang tersedia.',
            ])->onlyInput('identity');
        }

        // Tabel users pada project ini tidak memiliki remember_token.
        Auth::login($user, false);
        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        return $this->redirectForRole($user);
    }

    private function redirectForRole($user)
    {
        $role = strtolower(trim((string) ($user?->role ?? '')));

        if (in_array($role, ['mahasiswa', 'student'], true)) {
            return redirect()->route('student.dashboard');
        }

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Kamu sudah keluar dari SIAKADU.');
    }
}

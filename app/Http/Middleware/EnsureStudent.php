<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $role = strtolower(trim((string) ($user?->role ?? '')));

        if (!in_array($role, ['mahasiswa', 'student'], true)) {
            abort(403, 'Halaman ini hanya dapat diakses oleh mahasiswa.');
        }

        $npm = trim((string) ($user?->nomor_induk ?? ''));
        if ($npm === '' || !\App\Models\Mahasiswa::where('npm', $npm)->exists()) {
            abort(403, 'Akun mahasiswa belum terhubung ke data akademik. Hubungi administrator.');
        }

        return $next($request);
    }
}

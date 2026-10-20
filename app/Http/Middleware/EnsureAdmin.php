<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = strtolower(trim((string) ($request->user()->role ?? '')));
        $allowed = ['admin', 'administrator', 'super-admin', 'super_admin', 'admin akademik', 'administrator akademik', 'admin siakadu', 'baak', 'staf akademik', 'staff akademik', 'staff_akademik', 'staff'];

        if (!in_array($role, $allowed, true)) {
            abort(403, 'Akun ini tidak memiliki hak akses administrasi SIAKADU.');
        }

        return $next($request);
    }
}

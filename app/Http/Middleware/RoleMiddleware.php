<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        $user = Auth::user();

        // If 'owner' is required, only owner can access
        // If 'kasir' is allowed, both kasir and owner can access (owner has supervisor privilege)
        if (in_array('kasir', $roles) && $user->role === 'owner') {
            return $next($request);
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang (Role: ' . strtoupper($user->role) . ') untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
